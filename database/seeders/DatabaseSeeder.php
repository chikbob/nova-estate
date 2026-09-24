<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Application;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');
        User::factory()->create(['name' => 'Администратор NOVA', 'email' => 'admin@nova.test', 'role' => 'admin', 'password' => $password, 'phone' => '+1 555 100 00 01']);
        $realtors = collect([
            ['Алина Ветрова', 'alina@nova.test', 'Эксперт по жилой недвижимости и семейным переездам.'],
            ['Марк Левин', 'mark@nova.test', 'Специалист по инвестициям и коммерческим объектам.'],
            ['София Рэй', 'sofia@nova.test', 'Помогает найти недвижимость для жизни и долгосрочной аренды.'],
        ])->map(fn ($r) => User::factory()->create(['name' => $r[0], 'email' => $r[1], 'bio' => $r[2], 'role' => 'realtor', 'password' => $password]));
        User::factory()->create(['name' => 'Демо Клиент', 'email' => 'client@nova.test', 'role' => 'client', 'password' => $password]);
        User::factory(5)->create(['role' => 'client', 'password' => $password]);

        collect([
            ['apartment', 'Квартира', 'Apartment', 'Квартира'],
            ['house', 'Дом', 'House', 'Будинок'],
            ['townhouse', 'Таунхаус', 'Townhouse', 'Таунхаус'],
            ['commercial', 'Коммерция', 'Commercial', 'Комерційна'],
            ['land', 'Земельный участок', 'Land plot', 'Земельна ділянка'],
        ])->each(fn (array $type) => PropertyType::create([
            'slug' => $type[0],
            'name' => $type[1],
            'translations' => ['en' => ['name' => $type[2]], 'ru' => ['name' => $type[1]], 'uk' => ['name' => $type[3]]],
        ]));
        $amenities = collect([
            ['Паркинг', 'Parking', 'Паркінг'], ['Терраса', 'Terrace', 'Тераса'], ['Консьерж', 'Concierge', 'Консьєрж'],
            ['Лифт', 'Lift', 'Ліфт'], ['Мебель', 'Furnished', 'Меблі'], ['Климат-контроль', 'Climate control', 'Клімат-контроль'],
            ['Спортзал', 'Gym', 'Спортзал'], ['Охрана', 'Security', 'Охорона'],
        ])->map(fn (array $amenity) => Amenity::create([
            'name' => $amenity[0],
            'translations' => ['en' => ['name' => $amenity[1]], 'ru' => ['name' => $amenity[0]], 'uk' => ['name' => $amenity[2]]],
        ]));

        Property::factory(24)->create()->each(function (Property $property, int $index) use ($amenities, $realtors) {
            $property->update(['realtor_id' => $realtors[$index % $realtors->count()]->id]);
            foreach (range(0, 2) as $imageIndex) {
                $photoNumber = (($index + $imageIndex) % 24) + 1;
                $property->images()->create([
                    'path' => sprintf('/images/properties/estate-%02d.jpg', $photoNumber),
                    'alt' => $property->title,
                    'sort_order' => $imageIndex,
                    'is_cover' => $imageIndex === 0,
                ]);
            }
            $property->amenities()->sync($amenities->random(random_int(3, 6))->pluck('id'));
        });
        Application::factory(14)->create();
    }
}
