@extends('layouts.app')

@section('title', 'Weather Map - Weather Predictor')

@section('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
    #weather-map { height: min(68vh, 620px); min-height: 390px; border-radius: 12px; z-index: 1; }
    .map-tools { display:flex; flex-wrap:wrap; gap:.7rem; align-items:end; margin-bottom:1rem; }
    .map-tools .form-group { flex:1 1 220px; margin:0; }
    .map-selection { display:grid; gap:.5rem; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); margin-top:1rem; }
    .map-selection .form-group { margin:0; }
    .map-hint { color:var(--text-muted); margin:.75rem 0 1rem; }
    .leaflet-popup-content-wrapper,.leaflet-popup-tip { background:#20272c; color:#e7eef0; }
    .leaflet-popup-content a { color:#49b6c2; text-decoration:underline; }
    @media(max-width:600px) { #weather-map { min-height:340px; } }
</style>
@endsection

@section('content')
<div class="page-header">
    <div class="hero-panel">
        <div class="eyebrow">Explore Weather Anywhere</div>
        <h1 class="page-title">Pick a point. Get its weather prediction.</h1>
        <p class="page-subtitle">Search for a place, click the map, or use your device location. We fetch current conditions, run the model prediction, and save the result to that location’s history.</p>
        <div class="hero-actions">
            <button type="button" class="btn btn-primary" id="locate-me">◎ Use my current location</button>
            <a href="{{ route('locations.index') }}" class="btn btn-secondary">Manage saved locations</a>
        </div>
    </div>
    <div class="hero-meta">
        <div class="hero-stat"><span>Saved map locations</span><strong>{{ $locations->count() }}</strong><small>Choose a marker to open its weather history.</small></div>
        <div class="hero-stat"><span>How location works</span><strong>Browser GPS</strong><small>Current location needs your permission and a secure context (localhost works).</small></div>
    </div>
</div>

<div class="card">
    <div class="map-tools">
        <div class="form-group">
            <label for="place-search">Find a city or place</label>
            <input id="place-search" type="search" placeholder="e.g. Nairobi, Kenya">
        </div>
        <button class="btn btn-secondary" type="button" id="search-place">Search place</button>
        <span class="muted" id="map-status" role="status">Click anywhere on the map to select a forecast point.</span>
    </div>
    <div id="weather-map" aria-label="Interactive weather map"></div>

    <form action="{{ route('weather.map.predict') }}" method="POST" id="map-prediction-form">
        @csrf
        <div class="map-selection">
            <div class="form-group">
                <label for="selected-name">Place name</label>
                <input id="selected-name" name="name" value="{{ old('name') }}" maxlength="255" required placeholder="Selected location">
            </div>
            <div class="form-group">
                <label for="selected-country">Country</label>
                <input id="selected-country" name="country" value="{{ old('country') }}" maxlength="255" placeholder="Optional">
            </div>
            <div class="form-group">
                <label for="selected-latitude">Latitude</label>
                <input id="selected-latitude" name="latitude" value="{{ old('latitude') }}" type="number" step="any" min="-90" max="90" required>
            </div>
            <div class="form-group">
                <label for="selected-longitude">Longitude</label>
                <input id="selected-longitude" name="longitude" value="{{ old('longitude') }}" type="number" step="any" min="-180" max="180" required>
            </div>
        </div>
        <p class="map-hint">Selecting a map point automatically fills its coordinates. Place names are looked up to help label your saved history.</p>
        <button type="submit" class="btn btn-success">Fetch conditions and predict</button>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    const savedLocations = @json($savedLocations);
    const map = L.map('weather-map').setView([-1.2864, 36.8172], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    let selectedMarker = null;
    const status = document.getElementById('map-status');
    const latInput = document.getElementById('selected-latitude');
    const lonInput = document.getElementById('selected-longitude');
    const nameInput = document.getElementById('selected-name');
    const countryInput = document.getElementById('selected-country');

    savedLocations.forEach(location => {
        L.marker([location.latitude, location.longitude]).addTo(map)
            .bindPopup(`<strong>${escapeHtml(location.name)}</strong><br>${escapeHtml(location.country || '')}<br>${location.count} saved records<br><a href="${location.url}">Open weather history</a>`);
    });

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
    }

    async function setPoint(lat, lon, reverseLookup = true) {
        latInput.value = Number(lat).toFixed(6);
        lonInput.value = Number(lon).toFixed(6);
        if (selectedMarker) map.removeLayer(selectedMarker);
        selectedMarker = L.marker([lat, lon], {draggable:true}).addTo(map).bindPopup('Forecast point').openPopup();
        selectedMarker.on('dragend', event => {
            const point = event.target.getLatLng();
            setPoint(point.lat, point.lng);
        });
        if (!nameInput.value || nameInput.value === 'Selected location') nameInput.value = `Pinned location (${Number(lat).toFixed(3)}, ${Number(lon).toFixed(3)})`;
        status.textContent = `Selected ${Number(lat).toFixed(5)}, ${Number(lon).toFixed(5)}.`;
        if (!reverseLookup) return;
        status.textContent = 'Selected point. Looking up its place name…';
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lon)}`);
            if (!response.ok) throw new Error('Place lookup unavailable');
            const result = await response.json();
            const address = result.address || {};
            const place = address.city || address.town || address.village || address.suburb || address.county || result.name;
            if (place) nameInput.value = place;
            if (address.country) countryInput.value = address.country;
            status.textContent = place ? `Selected ${place}${address.country ? ', ' + address.country : ''}.` : `Selected ${Number(lat).toFixed(5)}, ${Number(lon).toFixed(5)}.`;
        } catch (error) {
            status.textContent = `Selected ${Number(lat).toFixed(5)}, ${Number(lon).toFixed(5)}. You can enter a place name below.`;
        }
    }

    map.on('click', event => setPoint(event.latlng.lat, event.latlng.lng));

    document.getElementById('locate-me').addEventListener('click', () => {
        if (!navigator.geolocation) { status.textContent = 'This browser does not support location services.'; return; }
        status.textContent = 'Requesting your device location…';
        navigator.geolocation.getCurrentPosition(position => {
            const {latitude, longitude} = position.coords;
            map.setView([latitude, longitude], 13);
            setPoint(latitude, longitude);
        }, error => {
            status.textContent = error.code === 1 ? 'Location permission was denied. Search or click the map instead.' : 'Could not read your location. Search or click the map instead.';
        }, {enableHighAccuracy:true, timeout:15000, maximumAge:30000});
    });

    document.getElementById('search-place').addEventListener('click', async () => {
        const query = document.getElementById('place-search').value.trim();
        if (!query) { status.textContent = 'Enter a city or place to search.'; return; }
        status.textContent = 'Searching for that place…';
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=${encodeURIComponent(query)}`);
            const results = await response.json();
            if (!results.length) { status.textContent = 'No matching place found.'; return; }
            const result = results[0];
            const lat = Number(result.lat), lon = Number(result.lon);
            map.setView([lat, lon], 12);
            await setPoint(lat, lon, false);
            nameInput.value = result.name || query;
            if (result.address && result.address.country) countryInput.value = result.address.country;
            status.textContent = `Selected ${nameInput.value}. Adjust the pin for a more precise point.`;
        } catch (error) {
            status.textContent = 'Place search failed. Click the map to select a point.';
        }
    });

    document.getElementById('place-search').addEventListener('keydown', event => {
        if (event.key === 'Enter') { event.preventDefault(); document.getElementById('search-place').click(); }
    });

    document.getElementById('map-prediction-form').addEventListener('submit', event => {
        if (!latInput.value || !lonInput.value) { event.preventDefault(); status.textContent = 'Select a point on the map first.'; }
    });

    @if(old('latitude') !== null && old('longitude') !== null)
        map.setView([{{ (float) old('latitude') }}, {{ (float) old('longitude') }}], 12);
        setPoint({{ (float) old('latitude') }}, {{ (float) old('longitude') }}, false);
    @endif
</script>
@endsection
