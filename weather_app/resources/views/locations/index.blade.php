@extends('layouts.app')

@section('title', 'Locations - Weather Predictor')

@section('content')
<div class="grid">
    <div class="card">
        <h2>➕ Add New Location</h2>
        <form action="{{ route('locations.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">City Name</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label for="country">Country</label>
                <input type="text" name="country" id="country">
            </div>
            <div class="form-group">
                <label for="latitude">Latitude</label>
                <input type="number" name="latitude" id="latitude" step="any" min="-90" max="90">
            </div>
            <div class="form-group">
                <label for="longitude">Longitude</label>
                <input type="number" name="longitude" id="longitude" step="any" min="-180" max="180">
            </div>
            <button type="submit" class="btn btn-primary">Add Location</button>
        </form>
    </div>

    <div class="card">
        <h2>🌍 All Locations</h2>
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
                    <td><strong>{{ $location->name }}</strong></td>
                    <td>{{ $location->country ?? '-' }}</td>
                    <td>{{ $location->latitude ? $location->latitude . ', ' . $location->longitude : '-' }}</td>
                    <td>{{ $location->weather_data_count }}</td>
                    <td>
                        <form action="{{ route('locations.destroy', $location) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this location?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="padding:0.4rem 0.8rem;font-size:0.85rem;">Delete</button>
                        </form>
                        <a href="{{ route('weather.location', $location) }}" class="btn btn-secondary" style="padding:0.4rem 0.8rem;font-size:0.85rem;">Details</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:#9ca3af;">No locations yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
