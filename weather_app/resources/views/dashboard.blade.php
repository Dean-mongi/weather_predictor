@extends('layouts.app')

@section('title', 'Dashboard - Weather Predictor')

@section('content')
<div class="grid grid-2">
    <div class="card">
        <h2>📍 Locations Overview</h2>
        <table>
            <thead>
                <tr>
                    <th>Location</th>
                    <th>Country</th>
                    <th>Records</th>
                    <th>Latest Temp</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locations as $location)
                <tr>
                    <td><strong>{{ $location->name }}</strong></td>
                    <td>{{ $location->country }}</td>
                    <td>{{ $location->weather_data_count }}</td>
                    <td>
                        @if($location->latestWeather())
                            {{ $location->latestWeather()->temperature }}C
                        @else
                            <span style="color:#9ca3af">No data</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('weather.location', $location) }}" class="btn btn-primary" style="padding:0.4rem 0.8rem;font-size:0.85rem;">View</a>
                        <a href="{{ route('weather.fetch', $location) }}" class="btn btn-success" style="padding:0.4rem 0.8rem;font-size:0.85rem;">Fetch Live</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:#9ca3af;">No locations added yet. <a href="{{ route('locations.index') }}">Add one</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>🔮 Recent Predictions</h2>
        <table>
            <thead>
                <tr>
                    <th>Location</th>
                    <th>Temp</th>
                    <th>Humidity</th>
                    <th>Pressure</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentPredictions as $prediction)
                <tr>
                    <td>{{ $prediction->location->name }}</td>
                    <td><strong>{{ $prediction->temperature }}C</strong></td>
                    <td>{{ $prediction->humidity }}%</td>
                    <td>{{ $prediction->pressure }} hPa</td>
                    <td>{{ $prediction->recorded_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:#9ca3af;">No predictions yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h2>🧪 Manual Prediction</h2>
    <form action="{{ route('weather.predict') }}" method="POST">
        @csrf
        <div class="grid">
            <div class="form-group">
                <label for="location_id">Location</label>
                <select name="location_id" id="location_id" required>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="month">Month (1-12)</label>
                <input type="number" name="month" id="month" min="1" max="12" value="{{ date('n') }}" required>
            </div>
            <div class="form-group">
                <label for="humidity">Humidity (%)</label>
                <input type="number" name="humidity" id="humidity" min="0" max="100" step="0.1" value="60" required>
            </div>
            <div class="form-group">
                <label for="pressure">Pressure (hPa)</label>
                <input type="number" name="pressure" id="pressure" min="900" max="1100" step="0.1" value="1013" required>
            </div>
            <div class="form-group">
                <label for="wind_speed">Wind Speed (km/h)</label>
                <input type="number" name="wind_speed" id="wind_speed" min="0" max="200" step="0.1" value="10" required>
            </div>
            <div class="form-group">
                <label for="precipitation">Precipitation (mm)</label>
                <input type="number" name="precipitation" id="precipitation" min="0" step="0.1" value="0" required>
            </div>
            <div class="form-group">
                <label for="cloud_cover">Cloud Cover (%)</label>
                <input type="number" name="cloud_cover" id="cloud_cover" min="0" max="100" step="0.1" value="30" required>
            </div>
            <div class="form-group" style="display:flex;align-items:flex-end;">
                <button type="submit" class="btn btn-primary" style="width:100%;">Predict Temperature</button>
            </div>
        </div>
    </form>
</div>
@endsection
