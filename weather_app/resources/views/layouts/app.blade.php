<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Weather Predictor')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #f0f4f8; color: #333; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1rem 2rem; color: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar h1 { font-size: 1.5rem; display: inline-block; }
        .nav-links { float: right; margin-top: 0.2rem; }
        .nav-links a { color: white; text-decoration: none; margin-left: 1.5rem; opacity: 0.9; transition: opacity 0.3s; }
        .nav-links a:hover { opacity: 1; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 10px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 2px 15px rgba(0,0,0,0.08); }
        .card h2 { color: #667eea; margin-bottom: 1rem; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.5rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; }
        .grid-2 { grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); }
        .btn { display: inline-block; padding: 0.7rem 1.2rem; border-radius: 6px; text-decoration: none; font-weight: 500; cursor: pointer; border: none; font-size: 0.95rem; transition: transform 0.2s, box-shadow 0.2s; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-success { background: #10b981; color: white; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-secondary { background: #6b7280; color: white; }
        .alert { padding: 1rem; border-radius: 6px; margin-bottom: 1rem; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: 600; color: #4b5563; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: 500; color: #4b5563; }
        .form-group input, .form-group select { width: 100%; padding: 0.6rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.95rem; }
        .weather-card { text-align: center; padding: 2rem; }
        .weather-card .temp { font-size: 3rem; font-weight: bold; color: #667eea; }
        .weather-card .location { font-size: 1.2rem; color: #6b7280; margin-top: 0.5rem; }
        .stats { display: flex; justify-content: space-around; margin-top: 1rem; }
        .stat { text-align: center; }
        .stat-value { font-size: 1.3rem; font-weight: bold; color: #374151; }
        .stat-label { font-size: 0.85rem; color: #6b7280; }
        .badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.85rem; font-weight: 500; }
        .badge-prediction { background: #dbeafe; color: #1e40af; }
        .badge-live { background: #d1fae5; color: #065f46; }
        footer { text-align: center; padding: 2rem; color: #9ca3af; font-size: 0.9rem; margin-top: 2rem; }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>🌤️ Weather Predictor</h1>
        <div class="nav-links">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('locations.index') }}">Locations</a>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    <footer>
        <p>Powered by Python ML Model & Laravel | Weather Prediction System</p>
    </footer>
</body>
</html>
