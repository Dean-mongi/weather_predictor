@extends('layouts.app')

@section('title', 'Dashboard - Weather Predictor')

@section('content')
@php
    $latestTemperatures = $locationSummary->whereNotNull('temperature');
    $warmestLocation = $latestTemperatures->sortByDesc('temperature')->first();
    $averagePredictionTemp = $predictionTrend->avg('temperature');
    $totalRecords = $locations->sum('weather_data_count');
@endphp

<div class="page-header">
    <div class="hero-panel">
        <div class="eyebrow">Forecast Command Center</div>
        <h1 class="page-title">Weather prediction at a glance</h1>
        <p class="page-subtitle">
            Track recent model outputs, compare locations, and keep your manual prediction workflow close to the data that matters.
        </p>
        <div class="hero-actions">
            <a href="{{ route('locations.index') }}" class="btn btn-secondary">Manage Locations</a>
            <a href="#manual-prediction" class="btn btn-primary">Create Prediction</a>
        </div>
    </div>

    <div class="hero-meta">
        <div class="hero-stat">
            <span>Coverage</span>
            <strong>{{ $locations->count() }} locations</strong>
            <small>{{ $totalRecords }} weather records saved across the system</small>
        </div>
        <div class="hero-stat">
            <span>Warmest Spot</span>
            <strong>{{ $warmestLocation ? $warmestLocation['name'] : 'No data' }}</strong>
            <small>{{ $warmestLocation ? number_format($warmestLocation['temperature'], 1) . 'C latest reading' : 'Add data to compare locations' }}</small>
        </div>
        <div class="hero-stat">
            <span>Prediction Average</span>
            <strong>{{ $averagePredictionTemp !== null ? number_format($averagePredictionTemp, 1) . 'C' : 'No data' }}</strong>
            <small>Average temperature from the most recent saved predictions</small>
        </div>
        <div class="hero-stat">
            <span>Activity</span>
            <strong>{{ $recentPredictions->count() }} recent runs</strong>
            <small>Latest predictions shown below in the live dashboard feed</small>
        </div>
    </div>
</div>

<div class="summary-strip">
    <div class="summary-tile">
        <span>Total Locations</span>
        <strong>{{ $locations->count() }}</strong>
        <p>Every tracked city available for forecasts and live fetches.</p>
    </div>
    <div class="summary-tile">
        <span>Recent Predictions</span>
        <strong>{{ $recentPredictions->count() }}</strong>
        <p>Most recent model outputs stored for review.</p>
    </div>
    <div class="summary-tile">
        <span>Average Recent Temp</span>
        <strong>{{ $averagePredictionTemp !== null ? number_format($averagePredictionTemp, 1) . 'C' : 'No data' }}</strong>
        <p>Helps you spot the general forecast direction quickly.</p>
    </div>
    <div class="summary-tile">
        <span>Saved Records</span>
        <strong>{{ $totalRecords }}</strong>
        <p>Total weather history currently available for comparisons.</p>
    </div>
</div>

<div class="chart-grid">
    <div class="card chart-card">
        <div class="section-heading">
            <div>
                <h2>Prediction Temperature Trend</h2>
                <p>See how the model output changes over time across the latest saved prediction runs.</p>
            </div>
        </div>
        <div class="chart-panel">
            @if($predictionTrend->isNotEmpty())
                <canvas id="predictionTrendChart"></canvas>
            @else
                <div class="empty-chart">Make a prediction to see the temperature trend.</div>
            @endif
        </div>
    </div>

    <div class="card chart-card">
        <div class="section-heading">
            <div>
                <h2>Latest Temperature by Location</h2>
                <p>Compare the most recent stored reading for each tracked city.</p>
            </div>
        </div>
        <div class="chart-panel chart-panel-sm">
            @if($latestTemperatures->isNotEmpty())
                <canvas id="locationTemperatureChart"></canvas>
            @else
                <div class="empty-chart">Fetch weather or add a prediction for a location.</div>
            @endif
        </div>
    </div>
</div>

<div class="card chart-card">
    <div class="section-heading">
        <div>
            <h2>Prediction Conditions</h2>
            <p>Humidity, cloud cover, and wind speed from recent model inputs, all in one place.</p>
        </div>
    </div>
    <div class="chart-panel">
        @if($predictionTrend->isNotEmpty())
            <canvas id="conditionsChart"></canvas>
        @else
            <div class="empty-chart">Humidity, cloud cover, and wind graphs will appear here.</div>
        @endif
    </div>
</div>

<div class="grid grid-2">
    <div class="card">
        <div class="section-heading">
            <div>
                <h2>Locations Overview</h2>
                <p>Quick access to every location, including record volume and current stored temperature.</p>
            </div>
        </div>
        <div class="table-wrap">
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
                        <td>
                            <div class="stack">
                                <strong>{{ $location->name }}</strong>
                                <span>View history and fetch live weather</span>
                            </div>
                        </td>
                        <td>{{ $location->country ?: 'Not set' }}</td>
                        <td><span class="metric"><strong>{{ $location->weather_data_count }}</strong> <span class="muted">records</span></span></td>
                        <td>
                            @if($location->latestWeather())
                                <span class="metric"><strong>{{ number_format($location->latestWeather()->temperature, 1) }}C</strong></span>
                            @else
                                <span class="muted">No data</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions-row">
                                <a href="{{ route('weather.location', $location) }}" class="btn btn-primary btn-sm">View</a>
                                <a href="{{ route('weather.fetch', $location) }}" class="btn btn-success btn-sm">Fetch Live</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="muted" style="text-align:center;">No locations added yet. <a href="{{ route('locations.index') }}" class="btn-link">Add one here</a>.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="section-heading">
            <div>
                <h2>Recent Predictions</h2>
                <p>The latest model results saved to the system, ordered by time.</p>
            </div>
        </div>
        <div class="table-wrap">
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
                        <td>
                            <div class="stack">
                                <strong>{{ $prediction->location->name }}</strong>
                                <span>Predicted reading</span>
                            </div>
                        </td>
                        <td><strong>{{ number_format($prediction->temperature, 1) }}C</strong></td>
                        <td>{{ number_format($prediction->humidity, 1) }}%</td>
                        <td>{{ number_format($prediction->pressure, 1) }} hPa</td>
                        <td class="muted">{{ $prediction->recorded_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="muted" style="text-align:center;">No predictions yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card" id="manual-prediction">
    <div class="section-heading">
        <div>
            <h2>Manual Prediction</h2>
            <p>Enter the current weather conditions and let the model estimate the temperature for your selected location.</p>
        </div>
    </div>
    <form action="{{ route('weather.predict') }}" method="POST">
        @csrf
        <div class="form-grid">
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
            <div class="form-group" style="display:flex;align-items:end;">
                <button type="submit" class="btn btn-primary" style="width:100%;">Predict Temperature</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const predictionTrend = @json($predictionTrend);
    const locationSummary = @json($locationSummary);

    const chartColors = {
        temperature: '#d45a3d',
        humidity: '#1976a2',
        wind: '#1b8f6a',
        cloud: '#f29f58',
        grid: '#dfeaed',
        text: '#48626d'
    };

    Chart.defaults.color = chartColors.text;
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";

    if (predictionTrend.length) {
        new Chart(document.getElementById('predictionTrendChart'), {
            type: 'line',
            data: {
                labels: predictionTrend.map(item => `${item.label} - ${item.location}`),
                datasets: [{
                    label: 'Predicted Temperature (C)',
                    data: predictionTrend.map(item => item.temperature),
                    borderColor: chartColors.temperature,
                    backgroundColor: 'rgba(212, 90, 61, 0.12)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 6 } },
                    y: { grid: { color: chartColors.grid }, title: { display: true, text: 'Temperature (C)' } }
                }
            }
        });

        new Chart(document.getElementById('conditionsChart'), {
            type: 'line',
            data: {
                labels: predictionTrend.map(item => item.label),
                datasets: [
                    {
                        label: 'Humidity (%)',
                        data: predictionTrend.map(item => item.humidity),
                        borderColor: chartColors.humidity,
                        backgroundColor: 'rgba(25, 118, 162, 0.08)',
                        tension: 0.35
                    },
                    {
                        label: 'Cloud Cover (%)',
                        data: predictionTrend.map(item => item.cloud_cover),
                        borderColor: chartColors.cloud,
                        backgroundColor: 'rgba(242, 159, 88, 0.08)',
                        tension: 0.35
                    },
                    {
                        label: 'Wind (km/h)',
                        data: predictionTrend.map(item => item.wind_speed),
                        borderColor: chartColors.wind,
                        backgroundColor: 'rgba(27, 143, 106, 0.08)',
                        tension: 0.35
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
                    y: { grid: { color: chartColors.grid }, beginAtZero: true }
                }
            }
        });
    }

    const locationsWithTemperature = locationSummary.filter(item => item.temperature !== null);

    if (locationsWithTemperature.length) {
        new Chart(document.getElementById('locationTemperatureChart'), {
            type: 'bar',
            data: {
                labels: locationsWithTemperature.map(item => item.name),
                datasets: [{
                    label: 'Latest Temp (C)',
                    data: locationsWithTemperature.map(item => item.temperature),
                    backgroundColor: ['#0f7c82', '#1b8f6a', '#f29f58', '#d45a3d', '#1976a2', '#7aa6b5'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: chartColors.grid }, title: { display: true, text: 'Celsius' } }
                }
            }
        });
    }
</script>
@endsection
