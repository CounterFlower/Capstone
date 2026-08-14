<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Barangay Bagumbayan' }}</title>
    <style>
        :root {
            --bg: #edf3ee;
            --surface: rgba(255, 255, 255, 0.88);
            --surface-strong: #ffffff;
            --hero: #173729;
            --hero-soft: #2d5b46;
            --text: #163024;
            --muted: #61726b;
            --line: rgba(22, 48, 36, 0.1);
            --accent: #d6a03e;
            --success: #2e7d56;
            --shadow: 0 18px 42px rgba(22, 48, 36, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Trebuchet MS", "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(214, 160, 62, 0.16), transparent 24%),
                linear-gradient(160deg, #f5f0de 0%, #edf4ed 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        h1, h2, h3, h4, p {
            margin: 0;
        }

        h1, h2, h3, h4 {
            font-family: Georgia, "Times New Roman", serif;
        }

        .page {
            width: min(1200px, calc(100% - 32px));
            margin: 0 auto;
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(14px);
            background: rgba(245, 240, 222, 0.84);
            border-bottom: 1px solid rgba(22, 48, 36, 0.08);
        }

        .header-inner {
            width: min(1200px, calc(100% - 32px));
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 16px 0;
        }

        .brand small,
        .nav a,
        .section-copy,
        .card p,
        .list-item,
        .metric span,
        .footer-note {
            color: var(--muted);
        }

        .brand small {
            display: block;
            margin-top: 4px;
        }

        .nav {
            display: flex;
            gap: 18px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav a {
            font-weight: 700;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 18px;
            border-radius: 999px;
            font-weight: 700;
        }

        .button.primary {
            color: #fffdf5;
            background: linear-gradient(120deg, var(--hero-soft), var(--hero));
        }

        .button.secondary {
            background: rgba(255, 255, 255, 0.64);
            border: 1px solid rgba(22, 48, 36, 0.1);
        }

        .hero,
        .card,
        .notice-card,
        .metric,
        .service-card,
        .photo-card {
            border: 1px solid var(--line);
            border-radius: 24px;
            box-shadow: var(--shadow);
        }

        .hero {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 20px;
            padding: 32px 0 22px;
        }

        .eyebrow {
            margin-bottom: 8px;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--success);
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 22px;
        }

        .notice-card {
            padding: 24px;
            background: var(--surface);
        }

        .notice-stack,
        .service-grid,
        .metrics,
        .content-grid,
        .info-grid,
        .photo-grid {
            display: grid;
            gap: 18px;
        }

        .photo-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .photo-card {
            overflow: hidden;
            background: var(--surface);
        }

        .photo-card img {
            display: block;
            width: 100%;
            height: 230px;
            object-fit: cover;
            background: #dde7e0;
        }

        .photo-copy {
            padding: 18px 18px 20px;
        }

        .photo-copy h3 {
            margin-bottom: 8px;
        }

        .notice-stack {
            margin-top: 16px;
        }

        .notice-item {
            padding: 14px 16px;
            border-radius: 18px;
            background: var(--surface-strong);
            border: 1px solid rgba(22, 48, 36, 0.08);
        }

        .notice-item strong {
            display: block;
            margin-bottom: 6px;
        }

        .section {
            padding: 12px 0 22px;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 16px;
            margin-bottom: 18px;
        }

        .section-head h2 {
            font-size: 2rem;
        }

        .service-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .service-card,
        .card,
        .metric {
            padding: 22px;
            background: var(--surface);
        }

        .service-card h3,
        .card h3 {
            margin-bottom: 12px;
        }

        .metrics {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .metric strong {
            display: block;
            margin-top: 8px;
            font-size: 2rem;
        }

        .content-grid {
            grid-template-columns: 1.1fr 0.9fr;
        }

        .list-item + .list-item {
            margin-top: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid rgba(22, 48, 36, 0.08);
            color: var(--muted);
        }

        .table th {
            color: var(--text);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            background: rgba(46, 125, 86, 0.12);
            color: var(--success);
        }

        .info-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        footer {
            padding: 24px 0 36px;
        }

        @media (max-width: 1080px) {
            .hero,
            .service-grid,
            .metrics,
            .content-grid,
            .info-grid,
            .photo-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .header-inner,
            .section-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .page {
                width: min(100% - 20px, 1200px);
            }

            .hero,
            .notice-card,
            .service-card,
            .card,
            .metric,
            .photo-card {
                padding: 20px;
                border-radius: 20px;
            }

            .photo-card {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <div class="brand">
                <strong>Barangay Bagumbayan</strong>
                <small>Daraga, Albay Barangay Management System Prototype</small>
            </div>

            <nav class="nav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('public.incidents') }}">Incident Reporting</a>
                <a href="{{ route('public.documents') }}">Document Requests</a>
                <a href="{{ route('public.events') }}">Event Registration</a>
                @if (session('is_admin'))
                    <a class="button secondary" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                @else
                    <a class="button primary" href="{{ route('admin.login') }}">Admin Login</a>
                @endif
            </nav>
        </div>
    </header>

    <main class="page">
        @yield('content')
    </main>

    <footer class="page">
        <p class="footer-note">Prototype only. Forms and dashboards are static layouts with no database write operations.</p>
    </footer>
</body>
</html>
