<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        $property = Property::query()->inRandomOrder()->first();
        $client = User::where('role', 'client')->inRandomOrder()->first();

        return ['user_id' => $client?->id, 'property_id' => $property?->id, 'realtor_id' => $property?->realtor_id, 'name' => $client?->name ?? fake()->name(), 'phone' => '+1 555 '.fake()->numerify('### ## ##'), 'email' => $client?->email, 'message' => 'Хочу уточнить детали и договориться о просмотре.', 'status' => fake()->randomElement(['new', 'in_progress', 'meeting', 'completed', 'cancelled'])];
    }
}
