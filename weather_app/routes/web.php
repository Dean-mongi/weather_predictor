<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\LocationController;

Route::get('/', [WeatherController::class, 'dashboard'])->name('dashboard');

Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');

Route::get('/weather/{location}', [WeatherController::class, 'locationWeather'])->name('weather.location');
Route::post('/predict', [WeatherController::class, 'predict'])->name('weather.predict');
Route::get('/fetch-weather/{location}', [WeatherController::class, 'fetchLiveWeather'])->name('weather.fetch');
