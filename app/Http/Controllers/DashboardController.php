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
            'admin' => ['Пользователи' => User::count(), 'Объекты' => Property::count(), 'На модерации' => Property::where('status', 'moderation')->count(), 'Заявки' => Application::count()],
            'realtor' => ['Мои объекты' => $user->properties()->count(), 'Опубликовано' => $user->properties()->where('status', 'published')->count(), 'Новые заявки' => $user->assignedApplications()->where('status', 'new')->count(), 'Завершено' => $user->assignedApplications()->where('status', 'completed')->count()],
            default => ['Избранное' => $user->favorites()->count(), 'Мои заявки' => $user->applications()->count(), 'В работе' => $user->applications()->whereIn('status', ['in_progress', 'meeting'])->count()],
        };

        return Inertia::render('Dashboard/Index', ['stats' => $stats]);
    }
}
