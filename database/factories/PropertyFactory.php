<?php

namespace Database\Factories;

use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PropertyFactory extends Factory
{
    public function definition(): array
    {
        $titleVariants = fake()->randomElement([
            ['en' => 'Bright apartment with panoramic views', 'ru' => 'Светлая квартира с панорамным видом', 'uk' => 'Світла квартира з панорамним краєвидом'],
            ['en' => 'Townhouse by the green park', 'ru' => 'Таунхаус у зелёного парка', 'uk' => 'Таунхаус біля зеленого парку'],
            ['en' => 'Penthouse in the business district', 'ru' => 'Пентхаус в деловом центре', 'uk' => 'Пентхаус у діловому центрі'],
            ['en' => 'Family home with a terrace', 'ru' => 'Семейный дом с террасой', 'uk' => 'Сімейний будинок із терасою'],
            ['en' => 'Loft in the historic quarter', 'ru' => 'Лофт в историческом квартале', 'uk' => 'Лофт в історичному кварталі'],
            ['en' => 'Turnkey office space', 'ru' => 'Офис с готовой отделкой', 'uk' => 'Офіс із готовим оздобленням'],
        ]);
        $districtVariants = fake()->randomElement([
            ['en' => 'North Shore', 'ru' => 'Северный берег', 'uk' => 'Північний берег'],
            ['en' => 'Nova Centre', 'ru' => 'Нова Центр', 'uk' => 'Нова Центр'],
            ['en' => 'Old Port', 'ru' => 'Старый порт', 'uk' => 'Старий порт'],
            ['en' => 'Green Quarter', 'ru' => 'Зелёный квартал', 'uk' => 'Зелений квартал'],
            ['en' => 'Arts District', 'ru' => 'Арт-район', 'uk' => 'Артрайон'],
        ]);
        $number = fake()->unique()->numberBetween(10, 999);
        $title = $titleVariants['ru'].' №'.$number;
        $operation = fake()->randomElement(['sale', 'rent']);
        $addressNumber = fake()->numberBetween(1, 240);
        $translations = collect(['en', 'ru', 'uk'])->mapWithKeys(fn (string $locale): array => [$locale => [
            'title' => $titleVariants[$locale].' №'.$number,
            'description' => match ($locale) {
                'ru' => 'Продуманное пространство с современным интерьером, естественным светом и удобной планировкой. Объект готов к просмотру.',
                'uk' => 'Продуманий простір із сучасним інтер’єром, природним світлом і зручним плануванням. Об’єкт готовий до перегляду.',
                default => 'A thoughtfully designed space with contemporary interiors, natural light and a practical layout. Ready for a private viewing.',
            },
            'district' => $districtVariants[$locale],
            'address' => match ($locale) {
                'ru' => "Проспект Мира, {$addressNumber}",
                'uk' => "Проспект Миру, {$addressNumber}",
                default => "{$addressNumber} Peace Avenue",
            },
        ]])->all();

        return [
            'property_type_id' => PropertyType::query()->inRandomOrder()->value('id') ?? PropertyType::factory(),
            'realtor_id' => User::where('role', 'realtor')->inRandomOrder()->value('id'),
            'title' => $title, 'slug' => Str::slug($title).'-'.Str::lower(Str::random(4)),
            'description' => $translations['ru']['description'], 'translations' => $translations, 'operation' => $operation,
            'price' => $operation === 'rent' ? fake()->numberBetween(850, 6500) : fake()->numberBetween(90000, 780000),
            'address' => $translations['ru']['address'], 'district' => $districtVariants['ru'],
            'rooms' => fake()->numberBetween(1, 6), 'area' => fake()->randomFloat(1, 32, 310),
            'floor' => fake()->numberBetween(1, 18), 'total_floors' => fake()->numberBetween(5, 24),
            'latitude' => 55.75 + fake()->randomFloat(5, -0.1, 0.1), 'longitude' => 37.61 + fake()->randomFloat(5, -0.1, 0.1),
            'status' => 'published', 'is_featured' => fake()->boolean(35), 'published_at' => fake()->dateTimeBetween('-8 months'),
        ];
    }
}
