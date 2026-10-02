<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyImageStorage
{
    public function upload(UploadedFile $image, int $propertyId): string
    {
        if (! $this->usesSupabase()) {
            return '/storage/'.$image->store('properties/'.$propertyId, 'public');
        }

        $path = 'properties/'.$propertyId.'/'.Str::uuid().'.'.$image->extension();

        Http::withToken(config('services.supabase.storage_key'))
            ->withHeaders(['apikey' => config('services.supabase.storage_key')])
            ->withBody(file_get_contents($image->getRealPath()), $image->getMimeType())
            ->post($this->objectUrl($path))
            ->throw();

        return $this->publicUrl($path);
    }

    public function delete(string $url): void
    {
        if (str_starts_with($url, '/storage/')) {
            Storage::disk('public')->delete(Str::after($url, '/storage/'));

            return;
        }

        if (! $this->usesSupabase()) {
            return;
        }

        $prefix = $this->publicUrl('');
        if (! str_starts_with($url, $prefix)) {
            return;
        }

        Http::withToken(config('services.supabase.storage_key'))
            ->withHeaders(['apikey' => config('services.supabase.storage_key')])
            ->delete($this->objectUrl(Str::after($url, $prefix)))
            ->throw();
    }

    private function usesSupabase(): bool
    {
        return filled(config('services.supabase.url'))
            && filled(config('services.supabase.storage_key'))
            && filled(config('services.supabase.storage_bucket'));
    }

    private function objectUrl(string $path): string
    {
        return rtrim(config('services.supabase.url'), '/').'/storage/v1/object/'.config('services.supabase.storage_bucket').'/'.$path;
    }

    private function publicUrl(string $path): string
    {
        return rtrim(config('services.supabase.url'), '/').'/storage/v1/object/public/'.config('services.supabase.storage_bucket').'/'.$path;
    }
}
