<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\WeatherData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    private string $pythonApiUrl = 'http://127.0.0.1:5000';

    public function dashboard()
    {
        $locations = Location::with('weatherData')
            ->withCount('weatherData')
            ->get();

        $recentPredictions = WeatherData::with('location')
            ->where('is_prediction', true)
            ->latest('recorded_at')
            ->take(10)
            ->get();

        $predictionTrend = $recentPredictions
            ->sortBy('recorded_at')
            ->values()
            ->map(fn (WeatherData $prediction) => [
                'label' => $prediction->recorded_at->format('M d, H:i'),
                'location' => $prediction->location?->name ?? 'Unknown',
                'temperature' => (float) $prediction->temperature,
                'humidity' => (float) $prediction->humidity,
                'pressure' => (float) $prediction->pressure,
                'wind_speed' => (float) $prediction->wind_speed,
                'precipitation' => (float) $prediction->precipitation,
                'cloud_cover' => (float) $prediction->cloud_cover,
            ]);

        $locationSummary = $locations->map(function (Location $location) {
            $latestWeather = $location->latestWeather();

            return [
                'name' => $location->name,
                'records' => $location->weather_data_count,
                'temperature' => $latestWeather ? (float) $latestWeather->temperature : null,
            ];
        });

        return view('dashboard', compact(
            'locations',
            'recentPredictions',
            'predictionTrend',
            'locationSummary'
        ));
    }

    public function locationWeather(Location $location)
    {
        $weatherData = WeatherData::where('location_id', $location->id)
            ->latest('recorded_at')
            ->take(30)
            ->get();

        $historyChart = $weatherData
            ->sortBy('recorded_at')
            ->values()
            ->map(fn (WeatherData $data) => [
                'label' => $data->recorded_at->format('M d, H:i'),
                'temperature' => (float) $data->temperature,
                'humidity' => (float) $data->humidity,
                'pressure' => (float) $data->pressure,
                'wind_speed' => (float) $data->wind_speed,
                'precipitation' => (float) $data->precipitation,
                'cloud_cover' => (float) $data->cloud_cover,
                'type' => $data->is_prediction ? 'Prediction' : 'Live',
            ]);

        return view('location_weather', compact('location', 'weatherData', 'historyChart'));
    }

    public function predict(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'humidity' => 'required|numeric|min:0|max:100',
            'pressure' => 'required|numeric|min:900|max:1100',
            'wind_speed' => 'required|numeric|min:0|max:200',
            'precipitation' => 'required|numeric|min:0',
            'cloud_cover' => 'required|numeric|min:0|max:100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        try {
            $response = Http::post("{$this->pythonApiUrl}/predict", [
                'humidity' => (float) $validated['humidity'],
                'pressure' => (float) $validated['pressure'],
                'wind_speed' => (float) $validated['wind_speed'],
                'precipitation' => (float) $validated['precipitation'],
                'cloud_cover' => (float) $validated['cloud_cover'],
                'month' => (int) $validated['month'],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $predictedTemp = $data['predicted_temperature'];

                $weatherData = WeatherData::create([
                    'location_id' => $validated['location_id'],
                    'temperature' => $predictedTemp,
                    'humidity' => $validated['humidity'],
                    'pressure' => $validated['pressure'],
                    'wind_speed' => $validated['wind_speed'],
                    'precipitation' => $validated['precipitation'],
                    'cloud_cover' => $validated['cloud_cover'],
                    'month' => $validated['month'],
                    'is_prediction' => true,
                    'recorded_at' => now(),
                ]);

                return redirect()->back()->with('success', "Predicted temperature: {$predictedTemp}C");
            }

            return redirect()->back()->with('error', 'Failed to get prediction from API');
        } catch (\Exception $e) {
            Log::error('Prediction error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'API connection failed: ' . $e->getMessage());
        }
    }

    public function fetchLiveWeather(Location $location)
    {
        try {
            $response = Http::get("{$this->pythonApiUrl}/fetch-weather", [
                'location' => $location->name,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                WeatherData::create([
                    'location_id' => $location->id,
                    'temperature' => $data['predicted_temperature'],
                    'humidity' => $data['humidity'],
                    'pressure' => $data['pressure'],
                    'wind_speed' => $data['wind_speed'],
                    'precipitation' => $data['precipitation'],
                    'cloud_cover' => $data['cloud_cover'],
                    'month' => $data['month'],
                    'is_prediction' => true,
                    'recorded_at' => now(),
                ]);

                return redirect()->back()->with('success', 'Live weather fetched and predicted successfully!');
            }

            return redirect()->back()->with('error', 'Failed to fetch live weather');
        } catch (\Exception $e) {
            Log::error('Fetch weather error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'API connection failed');
        }
    }
}
