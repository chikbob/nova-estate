<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ApplicationController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['new', 'in_progress', 'meeting', 'completed', 'cancelled'])],
            'realtor_id' => ['nullable', 'integer', 'exists:users,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'status'])],
        ]);
        $query = $request->user()->role === 'realtor' ? $request->user()->assignedApplications() : $request->user()->applications();
        if ($request->user()->role === 'admin') {
            $query = Application::query();
        }

        if ($request->user()->role !== 'client') {
            $query
                ->when($request->string('search')->toString(), function ($query, string $search): void {
                    $query->where(function ($query) use ($search): void {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhereHas('property', fn ($propertyQuery) => $propertyQuery->where('title', 'like', "%{$search}%"));
                    });
                })
                ->when($request->string('status')->toString(), fn ($query, string $status) => $query->where('status', $status))
                ->when(
                    $request->user()->role === 'admin' && $request->integer('realtor_id'),
                    fn ($query) => $query->where('realtor_id', $request->integer('realtor_id'))
                )
                ->when($request->date('date_from'), fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                ->when($request->date('date_to'), fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
        }

        match ($request->string('sort')->toString()) {
            'oldest' => $query->oldest(),
            'status' => $query->orderBy('status')->latest('created_at'),
            default => $query->latest(),
        };

        return Inertia::render('Dashboard/Applications', [
            'applications' => $query->with(['property.type', 'realtor', 'user'])->paginate(15)->withQueryString(),
            'realtors' => $request->user()->role === 'admin'
                ? User::where('role', 'realtor')->where('is_blocked', false)->orderBy('name')->get()
                : [],
            'filters' => $filters,
        ]);
    }

    public function store(Request $request, ?Property $property = null): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'phone' => ['required', 'string', 'max:30'], 'email' => ['nullable', 'email'], 'message' => ['nullable', 'string', 'max:2000']]);
        Application::create($data + ['user_id' => $request->user()?->id, 'property_id' => $property?->id, 'realtor_id' => $property?->realtor_id, 'status' => 'new']);

        return back()->with('success', 'Заявка отправлена. Риелтор свяжется с вами.');
    }

    public function update(Request $request, Application $application): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin' || ($request->user()->role === 'realtor' && $application->realtor_id === $request->user()->id), 403);
        $data = $request->validate(['status' => ['required', Rule::in(['new', 'in_progress', 'meeting', 'completed', 'cancelled'])], 'realtor_id' => ['nullable', 'exists:users,id'], 'appointment_at' => ['nullable', 'date']]);
        $application->update($data);

        return back()->with('success', 'Статус заявки обновлён.');
    }
}
