<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'country', 'latitude', 'longitude'];

    public function weatherData(): HasMany
    {
        return $this->hasMany(WeatherData::class);
    }

    public function latestWeather()
    {
        return $this->weatherData()->latest('recorded_at')->first();
    }
}
