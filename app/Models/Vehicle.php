<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory;

    public const STATUSES = ['moving', 'idle', 'stopped', 'maintenance'];

    protected $fillable = [
        'name', 'plate', 'driver', 'status',
        'lat', 'lng', 'speed', 'fuel', 'heading', 'last_tick_at',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'speed' => 'integer',
        'fuel' => 'integer',
        'heading' => 'integer',
        'last_tick_at' => 'datetime',
    ];

    public function readings(): HasMany
    {
        return $this->hasMany(Reading::class);
    }
}
