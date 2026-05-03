<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Weather Predictor')</title>
    <style>
        :root {
            --bg: #eef4f7;
            --bg-accent: #dce9ef;
            --surface: rgba(255, 255, 255, 0.9);
            --surface-strong: #ffffff;
            --surface-muted: #f6fafb;
            --border: #d7e5ea;
            --border-strong: #b7ccd6;
            --text: #18313a;
            --text-muted: #58707b;
            --heading: #10242c;
            --primary: #0f7c82;
            --primary-dark: #0b5960;
            --secondary: #f2994a;
            --success: #15936f;
            --danger: #d1495b;
            --shadow: 0 18px 40px rgba(15, 39, 48, 0.08);
            --radius: 18px;
            --radius-sm: 10px;
            --max-width: 1240px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(15, 124, 130, 0.18), transparent 28%),
                radial-gradient(circle at top right, rgba(242, 153, 74, 0.12), transparent 26%),
                linear-gradient(180deg, #f6fbfc 0%, var(--bg) 42%, #edf3f6 100%);
            color: var(--text);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.5;
            min-height: 100vh;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .shell {
            min-height: 100vh;
            position: relative;
        }

        .navbar {
            backdrop-filter: blur(18px);
            background: rgba(9, 32, 38, 0.86);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            color: #f5fbfc;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .nav-inner {
            align-items: center;
            display: flex;
            gap: 1.5rem;
            justify-content: space-between;
            margin: 0 auto;
            max-width: var(--max-width);
            padding: 1rem 1.25rem;
        }

        .brand {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .brand strong {
            color: #ffffff;
            font-size: 1.1rem;
            letter-spacing: 0.02em;
        }

        .brand span {
            color: rgba(245, 251, 252, 0.72);
            font-size: 0.82rem;
        }

        .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
        }

        .nav-links a {
            border: 1px solid transparent;
            border-radius: 999px;
            color: rgba(245, 251, 252, 0.88);
            padding: 0.58rem 0.9rem;
            transition: 0.2s ease;
        }

        .nav-links a:hover,
        .nav-links a.active {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.12);
            color: #ffffff;
        }

        .container {
            margin: 0 auto;
            max-width: var(--max-width);
            padding: 2rem 1.25rem 3rem;
        }

        .page-header {
            align-items: end;
            display: grid;
            gap: 1rem;
            grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
            margin-bottom: 1.5rem;
        }

        .hero-panel {
            background:
                linear-gradient(135deg, rgba(15, 124, 130, 0.94), rgba(11, 89, 96, 0.88)),
                linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0));
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            box-shadow: var(--shadow);
            color: #f8feff;
            overflow: hidden;
            padding: 1.8rem;
            position: relative;
        }

        .hero-panel::after {
            background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.22), transparent 42%);
            content: "";
            inset: 0;
            pointer-events: none;
            position: absolute;
        }

        .hero-panel > * {
            position: relative;
            z-index: 1;
        }

        .eyebrow {
            color: rgba(248, 254, 255, 0.82);
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            margin-bottom: 0.7rem;
            text-transform: uppercase;
        }

        .page-title {
            color: #ffffff;
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1.05;
            margin-bottom: 0.75rem;
        }

        .page-subtitle {
            color: rgba(248, 254, 255, 0.88);
            font-size: 1rem;
            max-width: 48rem;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
            margin-top: 1.3rem;
        }

        .hero-meta {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .hero-stat {
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.56);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 1rem 1.1rem;
        }

        .hero-stat span {
            color: var(--text-muted);
            display: block;
            font-size: 0.8rem;
            margin-bottom: 0.35rem;
            text-transform: uppercase;
        }

        .hero-stat strong {
            color: var(--heading);
            display: block;
            font-size: 1.6rem;
            line-height: 1.1;
        }

        .hero-stat small {
            color: var(--text-muted);
            display: block;
            margin-top: 0.35rem;
        }

        .card {
            background: var(--surface);
            border: 1px solid rgba(255, 255, 255, 0.58);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
            padding: 1.5rem;
        }

        .section-heading {
            align-items: start;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .section-heading h2 {
            color: var(--heading);
            font-size: 1.2rem;
        }

        .section-heading p {
            color: var(--text-muted);
            font-size: 0.92rem;
            max-width: 38rem;
        }

        .grid {
            display: grid;
            gap: 1.25rem;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .grid-2 {
            grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
        }

        .grid-3 {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .summary-strip {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            margin-bottom: 1.5rem;
        }

        .summary-tile {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 252, 253, 0.94));
            border: 1px solid var(--border);
            border-radius: 16px;
            min-height: 122px;
            padding: 1rem;
        }

        .summary-tile span {
            color: var(--text-muted);
            display: block;
            font-size: 0.82rem;
            margin-bottom: 0.45rem;
            text-transform: uppercase;
        }

        .summary-tile strong {
            color: var(--heading);
            display: block;
            font-size: 1.7rem;
            line-height: 1.1;
        }

        .summary-tile p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .chart-grid {
            display: grid;
            gap: 1.25rem;
            grid-template-columns: minmax(0, 1.8fr) minmax(280px, 1fr);
        }

        .chart-card {
            min-height: 360px;
        }

        .chart-panel {
            height: 300px;
            position: relative;
            width: 100%;
        }

        .chart-panel.chart-panel-sm {
            height: 260px;
        }

        .empty-chart {
            align-items: center;
            color: var(--text-muted);
            display: flex;
            height: 100%;
            justify-content: center;
            padding: 1rem;
            text-align: center;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border-bottom: 1px solid #e4edf0;
            padding: 0.95rem 0.85rem;
            text-align: left;
            vertical-align: middle;
        }

        th {
            color: var(--text-muted);
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        tbody tr:hover {
            background: rgba(15, 124, 130, 0.04);
        }

        .metric {
            align-items: center;
            display: inline-flex;
            gap: 0.45rem;
        }

        .metric strong {
            color: var(--heading);
        }

        .muted {
            color: var(--text-muted);
        }

        .stack {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .stack strong {
            color: var(--heading);
        }

        .stack span {
            color: var(--text-muted);
            font-size: 0.88rem;
        }

        .btn {
            align-items: center;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            display: inline-flex;
            font-size: 0.94rem;
            font-weight: 600;
            gap: 0.45rem;
            justify-content: center;
            min-height: 42px;
            padding: 0.78rem 1.2rem;
            transition: 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #ffffff;
        }

        .btn-success {
            background: linear-gradient(135deg, #1eaa7f, var(--success));
            color: #ffffff;
        }

        .btn-secondary {
            background: #edf4f6;
            border: 1px solid var(--border);
            color: var(--heading);
        }

        .btn-danger {
            background: rgba(209, 73, 91, 0.12);
            border: 1px solid rgba(209, 73, 91, 0.2);
            color: #9f2336;
        }

        .btn-link {
            color: var(--primary-dark);
            padding: 0;
        }

        .btn-sm {
            font-size: 0.84rem;
            min-height: 36px;
            padding: 0.55rem 0.9rem;
        }

        .actions-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
        }

        .alert {
            border-radius: 14px;
            margin-bottom: 1rem;
            padding: 0.95rem 1rem;
        }

        .alert-success {
            background: rgba(21, 147, 111, 0.12);
            border: 1px solid rgba(21, 147, 111, 0.18);
            color: #0d6a50;
        }

        .alert-error {
            background: rgba(209, 73, 91, 0.12);
            border: 1px solid rgba(209, 73, 91, 0.2);
            color: #8c2433;
        }

        .form-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .form-group {
            margin-bottom: 0.2rem;
        }

        .form-group label {
            color: var(--heading);
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.45rem;
        }

        .form-group input,
        .form-group select {
            background: #fbfdfe;
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--heading);
            font-size: 0.95rem;
            min-height: 46px;
            padding: 0.75rem 0.9rem;
            width: 100%;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(15, 124, 130, 0.12);
            outline: none;
        }

        .weather-card {
            background:
                linear-gradient(135deg, rgba(15, 124, 130, 0.08), rgba(242, 153, 74, 0.08)),
                #ffffff;
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 1.8rem;
            text-align: center;
        }

        .weather-card .temp {
            color: var(--heading);
            font-size: clamp(3rem, 8vw, 4.6rem);
            font-weight: 700;
            line-height: 1;
            margin: 0.5rem 0;
        }

        .weather-card .location {
            color: var(--text-muted);
            font-size: 1rem;
        }

        .stats {
            display: grid;
            gap: 0.9rem;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            margin-top: 1.5rem;
        }

        .stat {
            background: rgba(255, 255, 255, 0.78);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 0.9rem 0.8rem;
        }

        .stat-value {
            color: var(--heading);
            font-size: 1.28rem;
            font-weight: 700;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.82rem;
            margin-top: 0.25rem;
        }

        .badge {
            border-radius: 999px;
            display: inline-block;
            font-size: 0.82rem;
            font-weight: 700;
            padding: 0.34rem 0.78rem;
        }

        .badge-prediction {
            background: rgba(15, 124, 130, 0.12);
            color: var(--primary-dark);
        }

        .badge-live {
            background: rgba(21, 147, 111, 0.12);
            color: #0c6e53;
        }

        .info-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
            margin-top: 1rem;
        }

        .info-pill {
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid var(--border);
            border-radius: 999px;
            color: var(--text-muted);
            font-size: 0.84rem;
            padding: 0.45rem 0.8rem;
        }

        .split-band {
            display: grid;
            gap: 1.25rem;
            grid-template-columns: minmax(0, 1.15fr) minmax(300px, 0.85fr);
        }

        footer {
            color: var(--text-muted);
            font-size: 0.9rem;
            padding: 1rem 1.25rem 2.5rem;
            text-align: center;
        }

        @media (max-width: 960px) {
            .page-header,
            .chart-grid,
            .split-band {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .nav-inner {
                align-items: start;
                flex-direction: column;
            }

            .hero-meta {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 1.3rem 1rem 2.5rem;
            }

            .hero-panel,
            .card {
                padding: 1.2rem;
            }

            .page-title {
                font-size: 2rem;
            }

            th,
            td {
                padding: 0.8rem 0.7rem;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <nav class="navbar">
            <div class="nav-inner">
                <a href="{{ route('dashboard') }}" class="brand">
                    <strong>Weather Predictor</strong>
                    <span>Forecast dashboards, live conditions, and prediction history</span>
                </a>
                <div class="nav-links">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('locations.index') }}" class="{{ request()->routeIs('locations.*') ? 'active' : '' }}">Locations</a>
                </div>
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
            <p>Weather Predictor powered by Laravel and your Python forecasting model.</p>
        </footer>
    </div>

    @yield('scripts')
</body>
</html>
