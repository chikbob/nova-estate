<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Home', [
            'featured' => Property::with(['type', 'images'])->where('status', 'published')->where('is_featured', true)->latest('published_at')->take(6)->get(),
            'types' => PropertyType::withCount(['properties' => fn ($q) => $q->where('status', 'published')])->get(),
            'realtors' => User::where('role', 'realtor')->where('is_blocked', false)->withCount('properties')->take(4)->get(),
        ]);
    }
}
