<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\PropertyType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ReferenceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/References', [
            'types' => PropertyType::withCount('properties')->orderBy('name')->get(),
            'amenities' => Amenity::withCount('properties')->orderBy('name')->get(),
        ]);
    }

    public function storeType(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100', 'unique:property_types,name']]);
        PropertyType::create(['name' => $data['name'], 'slug' => Str::slug($data['name'])]);

        return back()->with('success', 'Тип недвижимости добавлен.');
    }

    public function destroyType(PropertyType $propertyType): RedirectResponse
    {
        abort_if($propertyType->properties()->exists(), 422, 'Нельзя удалить тип, который используется в объявлениях.');
        $propertyType->delete();

        return back()->with('success', 'Тип недвижимости удалён.');
    }

    public function storeAmenity(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100', 'unique:amenities,name']]);
        Amenity::create($data);

        return back()->with('success', 'Удобство добавлено.');
    }

    public function destroyAmenity(Amenity $amenity): RedirectResponse
    {
        $amenity->delete();

        return back()->with('success', 'Удобство удалено.');
    }
}
