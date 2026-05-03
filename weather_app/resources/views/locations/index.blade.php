@extends('layouts.app')

@section('title', 'Locations - Weather Predictor')

@section('content')
@php
    $locationsWithCoordinates = $locations->filter(fn ($location) => $location->latitude !== null && $location->longitude !== null)->count();
    $trackedRecords = $locations->sum('weather_data_count');
@endphp

<div class="page-header">
    <div class="hero-panel">
        <div class="eyebrow">Location Directory</div>
        <h1 class="page-title">Manage your forecast coverage</h1>
        <p class="page-subtitle">
            Add the cities you want to monitor, keep coordinates organized, and jump from each location into detailed weather history.
        </p>
        <div class="hero-actions">
            <a href="#add-location" class="btn btn-primary">Add Location</a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="hero-meta">
        <div class="hero-stat">
            <span>Total Locations</span>
            <strong>{{ $locations->count() }}</strong>
            <small>Tracked places currently available in the system</small>
        </div>
        <div class="hero-stat">
            <span>Mapped Coordinates</span>
            <strong>{{ $locationsWithCoordinates }}</strong>
            <small>Locations with latitude and longitude saved</small>
        </div>
        <div class="hero-stat">
            <span>Weather Records</span>
            <strong>{{ $trackedRecords }}</strong>
            <small>Saved history across all locations</small>
        </div>
        <div class="hero-stat">
            <span>Coverage Health</span>
            <strong>{{ $locations->count() ? 'Ready' : 'Needs setup' }}</strong>
            <small>{{ $locations->count() ? 'You can fetch live weather and predictions now' : 'Add your first city to begin' }}</small>
        </div>
    </div>
</div>

<div class="split-band">
    <div class="card" id="add-location">
        <div class="section-heading">
            <div>
                <h2>Add New Location</h2>
                <p>Create another location entry for your weather prediction workflow.</p>
            </div>
        </div>
        <form action="{{ route('locations.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">City Name</label>
                    <input type="text" name="name" id="name" placeholder="Nairobi" required>
                </div>
                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" name="country" id="country" placeholder="Kenya">
                </div>
                <div class="form-group">
                    <label for="latitude">Latitude</label>
                    <input type="number" name="latitude" id="latitude" step="any" min="-90" max="90" placeholder="-1.286389">
                </div>
                <div class="form-group">
                    <label for="longitude">Longitude</label>
                    <input type="number" name="longitude" id="longitude" step="any" min="-180" max="180" placeholder="36.817223">
                </div>
            </div>
            <div class="hero-actions" style="margin-top:1.1rem;">
                <button type="submit" class="btn btn-primary">Add Location</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="section-heading">
            <div>
                <h2>Coverage Notes</h2>
                <p>A quick guide to keeping location records consistent and useful.</p>
            </div>
        </div>
        <div class="grid grid-3">
            <div class="summary-tile">
                <span>Use clear names</span>
                <strong style="font-size:1.2rem;">Readable city labels</strong>
                <p>Short, familiar names make charts and tables easier to scan.</p>
            </div>
            <div class="summary-tile">
                <span>Add coordinates</span>
                <strong style="font-size:1.2rem;">Better location context</strong>
                <p>Latitude and longitude help you keep similarly named cities distinct.</p>
            </div>
            <div class="summary-tile">
                <span>Review history</span>
                <strong style="font-size:1.2rem;">Keep records alive</strong>
                <p>Each detailed page becomes more useful as live and predicted data accumulates.</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="section-heading">
        <div>
            <h2>All Locations</h2>
            <p>Browse every saved city, inspect coordinates, and jump into location-level weather history.</p>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Country</th>
                    <th>Coordinates</th>
                    <th>Records</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locations as $location)
                <tr>
                    <td>
                        <div class="stack">
                            <strong>{{ $location->name }}</strong>
                            <span>Tracked forecast location</span>
                        </div>
                    </td>
                    <td>{{ $location->country ?: 'Not set' }}</td>
                    <td class="muted">
                        {{ $location->latitude !== null ? $location->latitude . ', ' . $location->longitude : 'Not available' }}
                    </td>
                    <td><span class="metric"><strong>{{ $location->weather_data_count }}</strong> <span class="muted">records</span></span></td>
                    <td>
                        <div class="actions-row">
                            <a href="{{ route('weather.location', $location) }}" class="btn btn-secondary btn-sm">Details</a>
                            <form action="{{ route('locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Delete this location?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="muted" style="text-align:center;">No locations yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
