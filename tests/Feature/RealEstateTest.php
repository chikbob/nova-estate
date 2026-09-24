<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RealEstateTest extends TestCase
{
    use RefreshDatabase;

    private PropertyType $type;

    protected function setUp(): void
    {
        parent::setUp();
        $this->type = PropertyType::create(['name' => 'Квартира', 'slug' => 'apartment']);
    }

    public function test_catalog_only_displays_published_properties(): void
    {
        $published = Property::factory()->create(['property_type_id' => $this->type->id, 'status' => 'published']);
        $draft = Property::factory()->create(['property_type_id' => $this->type->id, 'status' => 'draft']);

        $this->get('/properties')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Properties/Index')
            ->has('properties.data', 1)
            ->where('properties.data.0.id', $published->id));
    }

    public function test_catalog_includes_database_translations_for_properties(): void
    {
        $property = Property::factory()->create(['property_type_id' => $this->type->id, 'status' => 'published']);

        $this->get('/properties')->assertInertia(fn (Assert $page) => $page
            ->where('properties.data.0.id', $property->id)
            ->where('properties.data.0.translations.en.title', $property->translations['en']['title'])
            ->where('properties.data.0.translations.uk.district', $property->translations['uk']['district']));
    }

    public function test_administrator_can_filter_and_sort_client_applications(): void
    {
        $administrator = User::factory()->create(['role' => 'admin']);
        $firstRealtor = User::factory()->create(['role' => 'realtor']);
        $secondRealtor = User::factory()->create(['role' => 'realtor']);
        $matchingApplication = Application::factory()->create([
            'name' => 'Specific Client',
            'status' => 'in_progress',
            'realtor_id' => $firstRealtor->id,
            'created_at' => '2026-09-20 10:00:00',
        ]);
        Application::factory()->create([
            'name' => 'Other Client',
            'status' => 'new',
            'realtor_id' => $secondRealtor->id,
            'created_at' => '2026-09-21 10:00:00',
        ]);

        $this->actingAs($administrator)
            ->get(route('manage.applications.index', [
                'search' => 'Specific',
                'status' => 'in_progress',
                'realtor_id' => $firstRealtor->id,
                'date_from' => '2026-09-20',
                'date_to' => '2026-09-20',
                'sort' => 'oldest',
            ]))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard/Applications')
                ->has('applications.data', 1)
                ->where('applications.data.0.id', $matchingApplication->id)
                ->where('filters.status', 'in_progress'));
    }

    public function test_client_can_add_property_to_favorites(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $property = Property::factory()->create(['property_type_id' => $this->type->id]);

        $this->actingAs($client)->post(route('favorites.store', $property))->assertRedirect();
        $this->assertDatabaseHas('favorites', ['user_id' => $client->id, 'property_id' => $property->id]);
    }

    public function test_guest_is_redirected_from_favorites(): void
    {
        $property = Property::factory()->create(['property_type_id' => $this->type->id]);
        $this->post(route('favorites.store', $property))->assertRedirect(route('login'));
    }

    public function test_client_cannot_access_property_management(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $this->actingAs($client)->get(route('manage.properties.index'))->assertForbidden();
    }

    public function test_realtor_can_create_own_property(): void
    {
        $realtor = User::factory()->create(['role' => 'realtor']);
        $payload = [
            'title' => 'Новая квартира у парка', 'slug' => 'new-park-apartment',
            'description' => str_repeat('Просторная квартира с удобной планировкой. ', 3),
            'property_type_id' => $this->type->id, 'operation' => 'sale', 'price' => 125000,
            'address' => 'Авеню Мира, 10', 'district' => 'Зелёный квартал', 'rooms' => 3,
            'area' => 86.5, 'floor' => 4, 'total_floors' => 12, 'status' => 'moderation',
            'is_featured' => false, 'amenities' => [],
        ];

        $this->actingAs($realtor)->post(route('manage.properties.store'), $payload)->assertRedirect(route('manage.properties.index'));
        $this->assertDatabaseHas('properties', ['slug' => 'new-park-apartment', 'realtor_id' => $realtor->id]);
    }

    public function test_visitor_can_send_property_application(): void
    {
        $property = Property::factory()->create(['property_type_id' => $this->type->id]);
        $this->post(route('applications.store', $property), ['name' => 'Тестовый клиент', 'phone' => '+1 555 111 22 33', 'email' => 'lead@example.test', 'message' => 'Хочу посмотреть объект.'])->assertRedirect();
        $this->assertDatabaseHas('applications', ['property_id' => $property->id, 'status' => 'new']);
    }

    public function test_administrator_can_add_an_amenity_to_references(): void
    {
        $administrator = User::factory()->create(['role' => 'admin']);

        $this->actingAs($administrator)
            ->post(route('admin.amenities.store'), ['name' => 'Бассейн'])
            ->assertRedirect();

        $this->assertDatabaseHas('amenities', ['name' => 'Бассейн']);
    }

    public function test_client_cannot_access_administrative_references(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)
            ->get(route('admin.references.index'))
            ->assertForbidden();
    }
}
