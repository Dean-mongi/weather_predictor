<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherData extends Model
{
    use HasFactory;

    protected $table = 'weather_data';

    protected $fillable = [
        'location_id',
        'temperature',
        'humidity',
        'pressure',
        'wind_speed',
        'precipitation',
        'cloud_cover',
        'month',
        'is_prediction',
        'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'is_prediction' => 'boolean',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
