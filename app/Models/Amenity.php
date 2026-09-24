<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    protected $fillable = ['name', 'translations', 'icon'];

    protected function casts(): array
    {
        return ['translations' => 'array'];
    }

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class);
    }
}
