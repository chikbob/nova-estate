<?php

namespace Tests\Feature;

use App\Services\PropertyImageStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyImageStorageTest extends TestCase
{
    public function test_supabase_upload_returns_a_persistent_public_url(): void
    {
        config()->set('services.supabase', [
            'url' => 'https://example.supabase.co',
            'storage_key' => 'test-secret',
            'storage_bucket' => 'property-uploads',
        ]);
        Http::fake(['example.supabase.co/*' => Http::response(['Key' => 'uploaded'], 200)]);

        $url = app(PropertyImageStorage::class)->upload(UploadedFile::fake()->image('home.jpg'), 42);

        $this->assertStringStartsWith('https://example.supabase.co/storage/v1/object/public/property-uploads/properties/42/', $url);
        $this->assertStringEndsWith('.jpg', $url);
        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && str_starts_with($request->url(), 'https://example.supabase.co/storage/v1/object/property-uploads/properties/42/')
            && $request->hasHeader('Authorization', 'Bearer test-secret'));
    }

    public function test_local_upload_still_uses_the_public_disk(): void
    {
        config()->set('services.supabase.storage_key', null);
        Storage::fake('public');

        $url = app(PropertyImageStorage::class)->upload(UploadedFile::fake()->image('home.jpg'), 42);

        $this->assertStringStartsWith('/storage/properties/42/', $url);
        Storage::disk('public')->assertExists(substr($url, strlen('/storage/')));
    }
}
