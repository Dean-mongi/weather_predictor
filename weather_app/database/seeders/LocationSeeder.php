<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'New York', 'country' => 'USA', 'latitude' => 40.7128, 'longitude' => -74.0060],
            ['name' => 'London', 'country' => 'UK', 'latitude' => 51.5074, 'longitude' => -0.1278],
            ['name' => 'Tokyo', 'country' => 'Japan', 'latitude' => 35.6762, 'longitude' => 139.6503],
            ['name' => 'Sydney', 'country' => 'Australia', 'latitude' => -33.8688, 'longitude' => 151.2093],
            ['name' => 'Dubai', 'country' => 'UAE', 'latitude' => 25.2048, 'longitude' => 55.2708],
            ['name' => 'Paris', 'country' => 'France', 'latitude' => 48.8566, 'longitude' => 2.3522],
            ['name' => 'Mumbai', 'country' => 'India', 'latitude' => 19.0760, 'longitude' => 72.8777],
            ['name' => 'Cairo', 'country' => 'Egypt', 'latitude' => 30.0444, 'longitude' => 31.2357],
            ['name' => 'Rio de Janeiro', 'country' => 'Brazil', 'latitude' => -22.9068, 'longitude' => -43.1729],
            ['name' => 'Moscow', 'country' => 'Russia', 'latitude' => 55.7558, 'longitude' => 37.6173],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
