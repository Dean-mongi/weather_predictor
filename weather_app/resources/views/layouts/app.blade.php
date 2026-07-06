<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Weather Predictor')</title>
    <style>
        :root {
            --bg: #111518;
            --bg-accent: #20282c;
            --surface: rgba(25, 31, 35, 0.92);
            --surface-strong: #20272c;
            --surface-muted: #151a1e;
            --border: rgba(255, 255, 255, 0.08);
            --border-strong: rgba(255, 255, 255, 0.16);
            --text: #e7eef0;
            --text-muted: #9baaae;
            --heading: #f8fbfc;
            --primary: #49b6c2;
            --primary-dark: #1d7f8b;
            --secondary: #f2a65a;
            --success: #40c58d;
            --danger: #ff6b7a;
            --shadow: 0 22px 70px rgba(0, 0, 0, 0.28);
            --radius: 8px;
            --radius-sm: 8px;
            --max-width: 1240px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background:
                linear-gradient(135deg, rgba(73, 182, 194, 0.12) 0 1px, transparent 1px 28px),
                linear-gradient(180deg, #22272b 0%, var(--bg) 48%, #0d1013 100%);
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
            background: rgba(14, 17, 19, 0.88);
            border-bottom: 1px solid var(--border);
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
            padding: 0.85rem 1.25rem;
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
            border-radius: var(--radius-sm);
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
            padding: 1.3rem 1.25rem 3rem;
        }

        .page-header {
            align-items: end;
            display: grid;
            gap: 1rem;
            grid-template-columns: minmax(0, 1.35fr) minmax(320px, 0.9fr);
            margin-bottom: 1.5rem;
        }

        .hero-panel {
            background:
                linear-gradient(135deg, rgba(30, 42, 47, 0.96), rgba(13, 18, 21, 0.94)),
                linear-gradient(90deg, rgba(73, 182, 194, 0.18), rgba(242, 166, 90, 0.12));
            border: 1px solid var(--border-strong);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            color: #f8feff;
            overflow: hidden;
            padding: clamp(1rem, 2.4vw, 1.6rem);
            position: relative;
        }

        .hero-panel::after {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.14), transparent 46%);
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
            color: var(--secondary);
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            margin-bottom: 0.7rem;
            text-transform: uppercase;
        }

        .page-title {
            color: #ffffff;
            font-size: clamp(1.9rem, 3.2vw, 2.8rem);
            line-height: 1.08;
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

        .dashboard-hero-media {
            display: grid;
            gap: 0.8rem;
            grid-template-columns: minmax(0, 1.25fr) minmax(120px, 0.75fr);
            margin-top: 1.5rem;
            max-width: 660px;
        }

        .weather-photo {
            background-position: center;
            background-size: cover;
            border: 1px solid var(--border-strong);
            border-radius: var(--radius);
            min-height: 118px;
            overflow: hidden;
            position: relative;
        }

        .weather-photo::after {
            background: linear-gradient(180deg, transparent, rgba(8, 31, 38, 0.28));
            content: "";
            inset: 0;
            position: absolute;
        }

        .weather-photo-main {
            background-image: url("https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80");
            min-height: 248px;
            display: flex;
        }

        .dashboard-reference {
            position: absolute;
            inset: 10px;
            border-radius: var(--radius);
            background: url("/images/dashboard-reference.jpeg") center / cover no-repeat;
            opacity: 0.22;
            border: 1px solid rgba(255, 255, 255, 0.18);
            pointer-events: none;
            mix-blend-mode: screen;
        }


        .weather-photo-stack {
            display: grid;
            gap: 0.8rem;
        }

        .weather-photo-rain {
            background-image: url("https://images.unsplash.com/photo-1519692933481-e162a57d6721?auto=format&fit=crop&w=800&q=80");
        }

        .weather-photo-sun {
            background-image: url("https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=800&q=80");
        }

        .hero-meta {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .hero-stat {
            background: linear-gradient(180deg, rgba(35, 43, 48, 0.94), rgba(23, 28, 32, 0.94));
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1rem 1.1rem;
            min-height: 116px;
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
            font-size: clamp(1.1rem, 2.3vw, 1.45rem);
            line-height: 1.1;
            overflow-wrap: anywhere;
        }

        .hero-stat small {
            color: var(--text-muted);
            display: block;
            margin-top: 0.35rem;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
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
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 360px), 1fr));
        }

        .grid-3 {
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 220px), 1fr));
        }

        .summary-strip {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            margin-bottom: 1.5rem;
        }

        .summary-tile {
            background: linear-gradient(180deg, rgba(35, 43, 48, 0.96), rgba(21, 26, 30, 0.96));
            border: 1px solid var(--border);
            border-radius: var(--radius);
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
            grid-template-columns: minmax(0, 1.65fr) minmax(300px, 1fr);
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
            scrollbar-color: var(--border-strong) transparent;
        }

        table {
            border-collapse: collapse;
            min-width: 680px;
            width: 100%;
        }

        th,
        td {
            border-bottom: 1px solid var(--border);
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
            background: rgba(73, 182, 194, 0.08);
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
            border-radius: var(--radius-sm);
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
            background: rgba(255, 255, 255, 0.08);
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
            background: rgba(10, 13, 15, 0.72);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
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
                linear-gradient(135deg, rgba(73, 182, 194, 0.14), rgba(242, 166, 90, 0.12)),
                var(--surface-strong);
            border: 1px solid var(--border);
            border-radius: var(--radius);
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
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border);
            border-radius: var(--radius);
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
            border-radius: var(--radius-sm);
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
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-muted);
            font-size: 0.84rem;
            padding: 0.45rem 0.8rem;
        }

        .split-band {
            display: grid;
            gap: 1.25rem;
            grid-template-columns: minmax(0, 1.15fr) minmax(min(100%, 300px), 0.85fr);
        }

        footer {
            color: var(--text-muted);
            font-size: 0.9rem;
            padding: 1rem 1.25rem 2.5rem;
            text-align: center;
        }

        .api-status {
            border-color: rgba(73, 182, 194, 0.34);
        }

        .api-status.is-online {
            background: linear-gradient(180deg, rgba(23, 75, 58, 0.88), rgba(20, 37, 35, 0.94));
        }

        .api-status.is-offline {
            background: linear-gradient(180deg, rgba(96, 47, 43, 0.9), rgba(38, 25, 26, 0.94));
            border-color: rgba(255, 107, 122, 0.36);
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

            .dashboard-hero-media {
                grid-template-columns: 1fr;
            }

            .weather-photo-main {
                min-height: 190px;
            }

            .weather-photo-stack {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .container {
                padding: 1rem 0.8rem 2.5rem;
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

        @media (max-width: 520px) {
            .brand span {
                display: none;
            }

            .nav-links {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                width: 100%;
            }

            .nav-links a,
            .hero-actions .btn,
            .actions-row .btn,
            .actions-row form,
            .actions-row form .btn {
                width: 100%;
            }

            .hero-actions,
            .actions-row {
                display: grid;
                grid-template-columns: 1fr;
                width: 100%;
            }

            .weather-photo-stack {
                grid-template-columns: 1fr;
            }

            .chart-card {
                min-height: 320px;
            }

            .chart-panel,
            .chart-panel.chart-panel-sm {
                height: 240px;
            }

            .summary-strip,
            .form-grid,
            .stats {
                grid-template-columns: 1fr;
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
