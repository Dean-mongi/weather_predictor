@extends('layouts.app')

@section('title', $location->name . ' Weather')

@section('content')
<div class="card">
    <div class="weather-card">
        <div class="location">{{ $location->name }}, {{ $location->country }}</div>
        @if($location->latestWeather())
            <div class="temp">{{ $location->latestWeather()->temperature }}C</div>
            <div class="stats">
                <div class="stat">
                    <div class="stat-value">{{ $location->latestWeather()->humidity }}%</div>
                    <div class="stat-label">Humidity</div>
                </div>
                <div class="stat">
                    <div class="stat-value">{{ $location->latestWeather()->pressure }} hPa</div>
                    <div class="stat-label">Pressure</div>
                </div>
                <div class="stat">
                    <div class="stat-value">{{ $location->latestWeather()->wind_speed }} km/h</div>
                    <div class="stat-label">Wind</div>
                </div>
                <div class="stat">
                    <div class="stat-value">{{ $location->latestWeather()->precipitation }} mm</div>
                    <div class="stat-label">Rain</div>
                </div>
            </div>
            <p style="margin-top:1rem;color:#6b7280;">
                Last updated: {{ $location->latestWeather()->recorded_at->diffForHumans() }}
                <span class="badge {{ $location->latestWeather()->is_prediction ? 'badge-prediction' : 'badge-live' }}">
                    {{ $location->latestWeather()->is_prediction ? 'Prediction' : 'Live' }}
                </span>
            </p>
        @else
            <p style="color:#9ca3af;margin-top:1rem;">No weather data available</p>
        @endif
        <a href="{{ route('weather.fetch', $location) }}" class="btn btn-success" style="margin-top:1rem;">Fetch Live Weather</a>
    </div>
</div>

<div class="card">
    <h2>📊 Weather History (Last 30 Records)</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Temp</th>
                <th>Humidity</th>
                <th>Pressure</th>
                <th>Wind</th>
                <th>Precipitation</th>
                <th>Cloud Cover</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            @forelse($weatherData as $data)
            <tr>
                <td>{{ $data->recorded_at->format('M d, Y H:i') }}</td>
                <td><strong>{{ $data->temperature }}C</strong></td>
                <td>{{ $data->humidity }}%</td>
                <td>{{ $data->pressure }}</td>
                <td>{{ $data->wind_speed }} km/h</td>
                <td>{{ $data->precipitation }} mm</td>
                <td>{{ $data->cloud_cover }}%</td>
                <td>
                    <span class="badge {{ $data->is_prediction ? 'badge-prediction' : 'badge-live' }}">
                        {{ $data->is_prediction ? 'Prediction' : 'Live' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;color:#9ca3af;">No weather records</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
