<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyRequest;
use App\Models\Amenity;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ManagePropertyController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Property::with(['type', 'realtor', 'images'])->latest();
        if ($request->user()->role === 'realtor') {
            $query->where('realtor_id', $request->user()->id);
        }

        return Inertia::render('Manage/Properties/Index', ['properties' => $query->paginate(15)]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Manage/Properties/Form', $this->formData($request));
    }

    public function store(PropertyRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();
            $amenities = $data['amenities'] ?? [];
            unset($data['amenities'], $data['images']);
            $data['slug'] = $data['slug'] ?: Str::slug($data['title']).'-'.Str::lower(Str::random(5));
            $data['realtor_id'] = $request->user()->role === 'realtor' ? $request->user()->id : ($data['realtor_id'] ?? null);
            if ($data['status'] === 'published') {
                $data['published_at'] = now();
            }
            $property = Property::create($data);
            $property->amenities()->sync($amenities);
            $this->storeImages($request, $property);
        });

        return to_route('manage.properties.index')->with('success', 'Объект создан.');
    }

    public function edit(Request $request, Property $property): Response
    {
        $this->authorize('update', $property);
        $property->load(['amenities', 'images']);

        return Inertia::render('Manage/Properties/Form', $this->formData($request) + ['property' => $property]);
    }

    public function update(PropertyRequest $request, Property $property): RedirectResponse
    {
        $this->authorize('update', $property);
        DB::transaction(function () use ($request, $property) {
            $data = $request->validated();
            $amenities = $data['amenities'] ?? [];
            unset($data['amenities'], $data['images'], $data['realtor_id']);
            $data['slug'] = $data['slug'] ?: $property->slug;
            if ($data['status'] === 'published' && ! $property->published_at) {
                $data['published_at'] = now();
            }
            $property->update($data);
            $property->amenities()->sync($amenities);
            $this->storeImages($request, $property);
        });

        return to_route('manage.properties.index')->with('success', 'Объект обновлён.');
    }

    public function destroy(Request $request, Property $property): RedirectResponse
    {
        $this->authorize('delete', $property);
        $property->delete();

        return back()->with('success', 'Объект удалён.');
    }

    public function destroyImage(Request $request, Property $property, int $image): RedirectResponse
    {
        $this->authorize('update', $property);
        $record = $property->images()->findOrFail($image);
        if (str_starts_with($record->path, '/storage/')) {
            \Storage::disk('public')->delete(Str::after($record->path, '/storage/'));
        } $record->delete();

        return back()->with('success', 'Фотография удалена.');
    }

    private function formData(Request $request): array
    {
        return ['types' => PropertyType::orderBy('name')->get(), 'amenities' => Amenity::orderBy('name')->get(), 'realtors' => $request->user()->role === 'admin' ? User::where('role', 'realtor')->get() : []];
    }

    private function storeImages(Request $request, Property $property): void
    {
        foreach ($request->file('images', []) as $index => $image) {
            $path = $image->store('properties/'.$property->id, 'public');
            $property->images()->create(['path' => '/storage/'.$path, 'alt' => $property->title, 'sort_order' => $property->images()->count() + $index]);
        }
    }
}
