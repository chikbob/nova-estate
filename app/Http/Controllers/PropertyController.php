<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'], 'operation' => ['nullable', 'in:sale,rent'],
            'type' => ['nullable', 'string'], 'price_min' => ['nullable', 'numeric', 'min:0'], 'price_max' => ['nullable', 'numeric', 'min:0'],
            'rooms' => ['nullable', 'integer', 'min:0'], 'area_min' => ['nullable', 'numeric', 'min:0'], 'district' => ['nullable', 'string'],
            'sort' => ['nullable', 'in:newest,price_asc,price_desc,area_desc'],
        ]);
        $query = Property::with(['type', 'images'])->where('status', 'published')
            ->when($request->search, fn ($q, $v) => $q->where(fn ($sub) => $sub->where('title', 'like', "%{$v}%")->orWhere('address', 'like', "%{$v}%")))
            ->when($request->operation, fn ($q, $v) => $q->where('operation', $v))
            ->when($request->type, fn ($q, $v) => $q->whereHas('type', fn ($t) => $t->where('slug', $v)))
            ->when($request->price_min, fn ($q, $v) => $q->where('price', '>=', $v))
            ->when($request->price_max, fn ($q, $v) => $q->where('price', '<=', $v))
            ->when($request->rooms !== null, fn ($q) => $q->where('rooms', '>=', $request->integer('rooms')))
            ->when($request->area_min, fn ($q, $v) => $q->where('area', '>=', $v))
            ->when($request->district, fn ($q, $v) => $q->where('district', $v));
        match ($request->input('sort')) {
            'price_asc' => $query->orderBy('price'), 'price_desc' => $query->orderByDesc('price'),
            'area_desc' => $query->orderByDesc('area'), default => $query->latest('published_at'),
        };

        return Inertia::render('Properties/Index', [
            'properties' => $query->paginate(9)->withQueryString(), 'filters' => $filters,
            'types' => PropertyType::orderBy('name')->get(),
            'districts' => Property::where('status', 'published')
                ->select(['district', 'translations'])
                ->orderBy('district')
                ->get()
                ->unique('district')
                ->values()
                ->map(fn (Property $property): array => [
                    'value' => $property->district,
                    'translations' => collect($property->translations ?? [])->map(
                        fn (array $translation): array => ['name' => $translation['district'] ?? $property->district]
                    )->all(),
                ]),
            'favoriteIds' => $request->user()?->favorites()->pluck('properties.id') ?? [],
        ]);
    }

    public function show(Request $request, Property $property): Response
    {
        abort_if($property->status !== 'published' && ! ($request->user()?->hasRole('admin', 'realtor')), 404);
        $property->load(['type', 'images', 'amenities', 'realtor']);

        return Inertia::render('Properties/Show', [
            'property' => $property,
            'similar' => Property::with(['type', 'images'])->where('status', 'published')->where('id', '!=', $property->id)->where('property_type_id', $property->property_type_id)->take(3)->get(),
            'isFavorite' => $request->user()?->favorites()->whereKey($property->id)->exists() ?? false,
        ]);
    }
}
