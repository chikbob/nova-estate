<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FavoriteController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Dashboard/Favorites', ['properties' => $request->user()->favorites()->with(['type', 'images'])->paginate(12)]);
    }

    public function store(Request $request, Property $property): RedirectResponse
    {
        $request->user()->favorites()->syncWithoutDetaching($property);

        return back()->with('success', 'Объект добавлен в избранное.');
    }

    public function destroy(Request $request, Property $property): RedirectResponse
    {
        $request->user()->favorites()->detach($property);

        return back()->with('success', 'Объект удалён из избранного.');
    }
}
