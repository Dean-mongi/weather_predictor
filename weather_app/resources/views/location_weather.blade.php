@extends('layouts.app')

@section('title', $location->name . ' Weather')

@section('content')
@php
    $latest = $location->latestWeather();
    $recordCount = $weatherData->count();
    $predictionCount = $weatherData->where('is_prediction', true)->count();
    $liveCount = $recordCount - $predictionCount;
@endphp

<div class="page-header">
    <div class="hero-panel">
        <div class="eyebrow">Location Weather Detail</div>
        <h1 class="page-title">{{ $location->name }}</h1>
        <p class="page-subtitle">
            Review the latest conditions, inspect historical trends, and compare live and predicted records for this location.
        </p>
        <div class="hero-actions">
            <a href="{{ route('weather.fetch', $location) }}" class="btn btn-success">Fetch Live Weather</a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="hero-meta">
        <div class="hero-stat">
            <span>Country</span>
            <strong>{{ $location->country ?: 'Not set' }}</strong>
            <small>{{ $location->latitude !== null ? $location->latitude . ', ' . $location->longitude : 'Coordinates not available' }}</small>
        </div>
        <div class="hero-stat">
            <span>Latest Reading</span>
            <strong>{{ $latest ? number_format($latest->temperature, 1) . 'C' : 'No data' }}</strong>
            <small>{{ $latest ? $latest->recorded_at->diffForHumans() : 'Fetch weather to start tracking' }}</small>
        </div>
        <div class="hero-stat">
            <span>Records in View</span>
            <strong>{{ $recordCount }}</strong>
            <small>{{ $predictionCount }} predicted and {{ $liveCount }} live records</small>
        </div>
        <div class="hero-stat">
            <span>Record Type</span>
            <strong>{{ $latest ? ($latest->is_prediction ? 'Prediction' : 'Live') : 'Unavailable' }}</strong>
            <small>Type of the latest saved weather entry</small>
        </div>
    </div>
</div>

<div class="card">
    <div class="weather-card">
        <div class="location">{{ $location->name }}, {{ $location->country ?: 'Country not set' }}</div>
        @if($latest)
            <div class="temp">{{ number_format($latest->temperature, 1) }}C</div>
            <div class="stats">
                <div class="stat">
                    <div class="stat-value">{{ number_format($latest->humidity, 1) }}%</div>
                    <div class="stat-label">Humidity</div>
                </div>
                <div class="stat">
                    <div class="stat-value">{{ number_format($latest->pressure, 1) }} hPa</div>
                    <div class="stat-label">Pressure</div>
                </div>
                <div class="stat">
                    <div class="stat-value">{{ number_format($latest->wind_speed, 1) }} km/h</div>
                    <div class="stat-label">Wind</div>
                </div>
                <div class="stat">
                    <div class="stat-value">{{ number_format($latest->precipitation, 1) }} mm</div>
                    <div class="stat-label">Rain</div>
                </div>
            </div>
            <div class="info-pills">
                <span class="info-pill">Updated {{ $latest->recorded_at->format('M d, Y H:i') }}</span>
                <span class="badge {{ $latest->is_prediction ? 'badge-prediction' : 'badge-live' }}">
                    {{ $latest->is_prediction ? 'Prediction' : 'Live' }}
                </span>
            </div>
        @else
            <p class="muted" style="margin-top:1rem;">No weather data available yet for this location.</p>
        @endif
    </div>
</div>

<div class="chart-grid">
    <div class="card chart-card">
        <div class="section-heading">
            <div>
                <h2>{{ $location->name }} Weather Graphs</h2>
                <p>Temperature and pressure history for the last saved records.</p>
            </div>
        </div>
        <div class="chart-panel">
            @if($historyChart->isNotEmpty())
                <canvas id="locationHistoryChart"></canvas>
            @else
                <div class="empty-chart">Fetch weather or make a prediction to build this graph.</div>
            @endif
        </div>
    </div>

    <div class="card chart-card">
        <div class="section-heading">
            <div>
                <h2>Conditions Snapshot</h2>
                <p>Humidity, cloud cover, and precipitation across recent records.</p>
            </div>
        </div>
        <div class="chart-panel chart-panel-sm">
            @if($historyChart->isNotEmpty())
                <canvas id="locationConditionsChart"></canvas>
            @else
                <div class="empty-chart">Condition graphs will appear when records are available.</div>
            @endif
        </div>
    </div>
</div>

<div class="card">
    <div class="section-heading">
        <div>
            <h2>Weather History</h2>
            <p>Last 30 records saved for this location, including both live fetches and prediction results.</p>
        </div>
    </div>
    <div class="table-wrap">
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
                    <td>
                        <div class="stack">
                            <strong>{{ $data->recorded_at->format('M d, Y') }}</strong>
                            <span>{{ $data->recorded_at->format('H:i') }}</span>
                        </div>
                    </td>
                    <td><strong>{{ number_format($data->temperature, 1) }}C</strong></td>
                    <td>{{ number_format($data->humidity, 1) }}%</td>
                    <td>{{ number_format($data->pressure, 1) }} hPa</td>
                    <td>{{ number_format($data->wind_speed, 1) }} km/h</td>
                    <td>{{ number_format($data->precipitation, 1) }} mm</td>
                    <td>{{ number_format($data->cloud_cover, 1) }}%</td>
                    <td>
                        <span class="badge {{ $data->is_prediction ? 'badge-prediction' : 'badge-live' }}">
                            {{ $data->is_prediction ? 'Prediction' : 'Live' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="muted" style="text-align:center;">No weather records.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const historyChart = @json($historyChart);

    const chartColors = {
        temperature: '#d45a3d',
        humidity: '#1976a2',
        pressure: '#7aa6b5',
        precipitation: '#0f7c82',
        cloud: '#f29f58',
        grid: '#dfeaed',
        text: '#48626d'
    };

    Chart.defaults.color = chartColors.text;
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";

    if (historyChart.length) {
        new Chart(document.getElementById('locationHistoryChart'), {
            type: 'line',
            data: {
                labels: historyChart.map(item => item.label),
                datasets: [
                    {
                        label: 'Temperature (C)',
                        data: historyChart.map(item => item.temperature),
                        borderColor: chartColors.temperature,
                        backgroundColor: 'rgba(212, 90, 61, 0.12)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        yAxisID: 'temperature'
                    },
                    {
                        label: 'Pressure (hPa)',
                        data: historyChart.map(item => item.pressure),
                        borderColor: chartColors.pressure,
                        backgroundColor: 'rgba(122, 166, 181, 0.08)',
                        borderDash: [6, 4],
                        tension: 0.35,
                        yAxisID: 'pressure'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
                    temperature: { type: 'linear', position: 'left', grid: { color: chartColors.grid }, title: { display: true, text: 'Celsius' } },
                    pressure: { type: 'linear', position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'hPa' } }
                }
            }
        });

        new Chart(document.getElementById('locationConditionsChart'), {
            type: 'bar',
            data: {
                labels: historyChart.map(item => item.label),
                datasets: [
                    {
                        label: 'Humidity (%)',
                        data: historyChart.map(item => item.humidity),
                        backgroundColor: 'rgba(25, 118, 162, 0.75)',
                        borderRadius: 6
                    },
                    {
                        label: 'Cloud Cover (%)',
                        data: historyChart.map(item => item.cloud_cover),
                        backgroundColor: 'rgba(242, 159, 88, 0.78)',
                        borderRadius: 6
                    },
                    {
                        label: 'Precipitation (mm)',
                        data: historyChart.map(item => item.precipitation),
                        backgroundColor: 'rgba(15, 124, 130, 0.72)',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
                    y: { beginAtZero: true, grid: { color: chartColors.grid } }
                }
            }
        });
    }
</script>
@endsection
