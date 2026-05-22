# TODO - Weather Predictor

## Completed
- [x] Copied `resources/views/2 dashboard.jpeg` into `weather_app/public/images/dashboard-reference.jpeg` for use as a reference asset.


## Remaining
- [ ] Update dashboard UI (`weather_app/resources/views/dashboard.blade.php`) to match the reference layout more closely (hero media + KPI arrangement + spacing).



- [x] Add “precise location” proof UI (resolved coordinates + source) on dashboard/location pages.


- [x] Fix coordinates handling in `WeatherController@fetchLiveWeather()` so that when lat+lon exist, it always uses them (and falls back cleanly when they are incomplete).

- [ ] (If needed) Adjust Python `geocode_location`/`fetch_current_weather` to return resolved coordinates consistently.
- [ ] Validate by running Laravel + Python services and testing Fetch Live for locations with and without coordinates.

