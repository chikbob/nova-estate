<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['property_type_id', 'realtor_id', 'title', 'slug', 'description', 'translations', 'operation', 'price', 'address', 'district', 'rooms', 'area', 'floor', 'total_floors', 'latitude', 'longitude', 'status', 'is_featured', 'published_at'])]
class Property extends Model
{
    use HasFactory;

    protected $appends = ['cover_url'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'area' => 'decimal:2', 'translations' => 'array', 'is_featured' => 'boolean', 'published_at' => 'datetime'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    public function realtor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'realtor_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function favoredBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    protected function coverUrl(): Attribute
    {
        return Attribute::get(fn () => $this->images->first()?->path ?? '/images/property-placeholder.svg');
    }
}
