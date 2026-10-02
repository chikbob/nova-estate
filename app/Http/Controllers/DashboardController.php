<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $stats = match ($user->role) {
            'admin' => ['stats.users' => User::count(), 'stats.properties' => Property::count(), 'stats.moderation' => Property::where('status', 'moderation')->count(), 'stats.applications' => Application::count()],
            'realtor' => ['stats.myProperties' => $user->properties()->count(), 'stats.published' => $user->properties()->where('status', 'published')->count(), 'stats.newApplications' => $user->assignedApplications()->where('status', 'new')->count(), 'stats.completed' => $user->assignedApplications()->where('status', 'completed')->count()],
            default => ['stats.favorites' => $user->favorites()->count(), 'stats.myApplications' => $user->applications()->count(), 'stats.inProgress' => $user->applications()->whereIn('status', ['in_progress', 'meeting'])->count()],
        };

        return Inertia::render('Dashboard/Index', ['stats' => $stats]);
    }
}
