<?php

namespace App\Http\Controllers;

use App\Models\ContactRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactRequestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'phone' => ['required', 'string', 'max:30'], 'email' => ['nullable', 'email'], 'subject' => ['nullable', 'string', 'max:150'], 'message' => ['nullable', 'string', 'max:2000']]);
        ContactRequest::create($data);

        return back()->with('success', 'Спасибо! Мы свяжемся с вами в ближайшее время.');
    }
}
