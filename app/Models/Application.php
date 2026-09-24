<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'property_id', 'realtor_id', 'name', 'phone', 'email', 'message', 'status', 'appointment_at'];

    protected function casts(): array
    {
        return ['appointment_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function realtor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'realtor_id');
    }
}
