<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserManagementController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Users', ['users' => User::latest()->paginate(20)]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->is($user), 422, 'Нельзя изменить собственную роль или блокировку.');
        $data = $request->validate(['role' => ['required', Rule::in(['client', 'realtor', 'admin'])], 'is_blocked' => ['required', 'boolean']]);
        $user->update($data);

        return back()->with('success', 'Пользователь обновлён.');
    }
}
