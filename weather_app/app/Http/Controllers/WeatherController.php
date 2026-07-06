<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\WeatherData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    private string $pythonApiUrl = 'http://127.0.0.1:5000';

    public function __construct()
    {
        $this->pythonApiUrl = rtrim((string) config('services.weather_api.url', 'http://127.0.0.1:5000'), '/');
    }

    public function dashboard()
    {
        $apiStatus = $this->apiStatus();

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
            'locationSummary',
            'apiStatus'
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
            $response = Http::timeout(15)->post("{$this->pythonApiUrl}/predict", [
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
        } catch (ConnectionException $e) {
            Log::error('Prediction connection error: ' . $e->getMessage());
            return redirect()->back()->with('error', $this->pythonApiUnavailableMessage());
        } catch (\Exception $e) {
            Log::error('Prediction error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'API connection failed: ' . $e->getMessage());
        }
    }

    public function fetchLiveWeather(Location $location)
    {
        try {
            // Prefer coordinates when both are available to ensure precise matching.
            $hasLat = $location->latitude !== null && $location->latitude !== '';
            $hasLon = $location->longitude !== null && $location->longitude !== '';

            $query = [
                'location' => $location->name,
                'country' => $location->country,
            ];

            if ($hasLat && $hasLon) {
                $query['latitude'] = (float) $location->latitude;
                $query['longitude'] = (float) $location->longitude;
            }

            $response = Http::timeout(20)->get("{$this->pythonApiUrl}/fetch-weather", $query);


            if ($response->successful()) {
                $data = $response->json();

                WeatherData::create([
                    'location_id' => $location->id,
                    'temperature' => $data['temperature'],
                    'predicted_temperature' => $data['predicted_temperature'] ?? null,
                    'humidity' => $data['humidity'],
                    'pressure' => $data['pressure'],
                    'wind_speed' => $data['wind_speed'],
                    'precipitation' => $data['precipitation'],
                    'cloud_cover' => $data['cloud_cover'],
                    'month' => $data['month'],
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                    'source' => $data['source'] ?? 'Open-Meteo',
                    'observed_at' => isset($data['observed_at']) ? Carbon::parse($data['observed_at']) : null,
                    'is_prediction' => false,
                    'recorded_at' => now(),
                ]);

                $temperature = number_format((float) $data['temperature'], 1);
                $source = $data['source'] ?? 'weather API';

                $resolvedLat = $data['latitude'] ?? $location->latitude;
                $resolvedLon = $data['longitude'] ?? $location->longitude;

                return redirect()->back()->with('success', "Current weather fetched from {$source}: {$temperature}C (lat {$resolvedLat}, lon {$resolvedLon}).");

            }

            $message = $response->json('error') ?: 'Failed to fetch current weather';

            return redirect()->back()->with('error', $message);
        } catch (ConnectionException $e) {
            Log::error('Fetch weather connection error: ' . $e->getMessage());
            return redirect()->back()->with('error', $this->pythonApiUnavailableMessage());
        } catch (\Exception $e) {
            Log::error('Fetch weather error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'API connection failed: ' . $e->getMessage());
        }
    }

    private function pythonApiUnavailableMessage(): string
    {
        return "Weather API is not running. Start the Python service at {$this->pythonApiUrl} and try again.";
    }

    private function apiStatus(): array
    {
        try {
            $response = Http::timeout(3)->get("{$this->pythonApiUrl}/health");

            if ($response->successful()) {
                return [
                    'online' => true,
                    'label' => 'Weather API online',
                    'detail' => 'Predictions are connected at ' . $this->pythonApiUrl,
                ];
            }
        } catch (\Throwable $e) {
            Log::debug('Weather API health check failed: ' . $e->getMessage());
        }

        return [
            'online' => false,
            'label' => 'Weather API offline',
            'detail' => 'Start the Python service at ' . $this->pythonApiUrl,
        ];
    }
}
