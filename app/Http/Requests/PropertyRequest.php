<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('realtor', 'admin') ?? false;
    }

    public function rules(): array
    {
        $property = $this->route('property');

        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:190', Rule::unique('properties')->ignore($property?->id)],
            'description' => ['required', 'string', 'min:40'],
            'property_type_id' => ['required', 'exists:property_types,id'],
            'realtor_id' => ['nullable', 'exists:users,id'],
            'operation' => ['required', Rule::in(['sale', 'rent'])],
            'price' => ['required', 'numeric', 'min:1'],
            'address' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:120'],
            'rooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'area' => ['required', 'numeric', 'min:1'],
            'floor' => ['nullable', 'integer', 'min:0'],
            'total_floors' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', Rule::in(['draft', 'moderation', 'published', 'sold', 'rented'])],
            'is_featured' => ['boolean'],
            'amenities' => ['array'], 'amenities.*' => ['exists:amenities,id'],
            'images' => ['array', 'max:10'], 'images.*' => ['image', 'max:5120'],
        ];
    }
}
