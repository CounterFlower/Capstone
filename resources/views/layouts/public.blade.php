<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Barangay Bagumbayan' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg: #F7FAFC;
            --surface: #FFFFFF;
            --surface-strong: #F7FAFC;
            --primary: #1A365D;
            --primary-deep: #0F2440;
            --secondary: #2B6CB0;
            --secondary-hover: #2C5282;
            --accent: #D69E2E;
            --accent-hover: #B7791F;
            --text: #2D3748;
            --muted: #718096;
            --line: rgba(45, 55, 72, 0.12);
            --shadow: 0 18px 42px rgba(26, 54, 93, 0.08);
            --success: #2B6CB0;
            --danger: #C53030;
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
                radial-gradient(circle at top left, rgba(214, 158, 46, 0.10), transparent 26%),
                radial-gradient(circle at top right, rgba(43, 108, 176, 0.10), transparent 24%),
                linear-gradient(160deg, #FFFFFF 0%, #F7FAFC 100%);
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
            color: var(--primary);
        }

        .page {
            width: min(1200px, calc(100% - 32px));
            margin: 0 auto;
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--primary);
            color: #FFFFFF;
            box-shadow: 0 4px 20px rgba(26, 54, 93, 0.15);
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
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

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-mark {
            width: 52px;
            height: 52px;
            object-fit: contain;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.06);
            padding: 4px;
            box-shadow: 0 6px 18px rgba(15, 36, 64, 0.18);
        }

        .brand-copy {
            display: grid;
            gap: 4px;
        }

        .brand strong {
            color: #FFFFFF;
            font-size: 1.15rem;
            letter-spacing: 0.02em;
        }

        .brand small {
            display: block;
            color: rgba(247, 250, 252, 0.82);
            font-size: 0.8rem;
        }

        .section-copy,
        .card p,
        .list-item,
        .metric span,
        .footer-note {
            color: var(--muted);
        }

        .nav {
            display: flex;
            gap: 18px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav a {
            color: #F7FAFC;
            font-weight: 600;
            transition: color 0.18s ease;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 18px;
            border-radius: 999px;
            font-weight: 700;
            cursor: pointer;
            transition: color 180ms ease, background-color 180ms ease, border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
        }

        .button.primary {
            color: #FFFFFF;
            background: var(--accent);
            box-shadow: 0 6px 18px rgba(214, 158, 46, 0.28);
        }

        .button.secondary {
            background: #FFFFFF;
            border: 1px solid rgba(43, 108, 176, 0.32);
            color: var(--secondary);
        }

        .site-header .button.secondary {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #FFFFFF;
        }

        .button:focus-visible {
            box-shadow: 0 8px 20px rgba(26, 54, 93, 0.18);
        }

        .button:active {
            filter: brightness(0.9);
        }

        .button:focus-visible {
            outline: 3px solid rgba(214, 158, 46, 0.65);
            outline-offset: 3px;
        }

        @media (hover: hover) {
            .nav a:hover {
                color: var(--accent);
            }

            .button.primary:hover {
                background: var(--accent-hover);
            }

            .button.secondary:hover {
                background: #EDF2F7;
                border-color: var(--secondary);
            }

            .site-header .button.secondary:hover {
                background: rgba(255, 255, 255, 0.25);
                color: #FFFFFF;
            }

        }

        @media (hover: hover) and (prefers-reduced-motion: no-preference) {
            .button.primary:hover,
            .button.secondary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(26, 54, 93, 0.18);
            }
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
            background: var(--surface);
        }

        .card,
        .notice-card,
        .metric,
        .service-card,
        .photo-card,
        .community-map-card,
        .info-box,
        .list-item,
        .intro-stat {
            transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease;
        }

        @media (hover: hover) and (prefers-reduced-motion: no-preference) {
            .card:hover,
            .notice-card:hover,
            .metric:hover,
            .service-card:hover,
            .photo-card:hover,
            .community-map-card:hover,
            .info-box:hover,
            .list-item:hover,
            .intro-stat:hover {
                transform: translateY(-3px);
                border-color: rgba(43, 108, 176, 0.28);
                box-shadow: 0 14px 30px rgba(26, 54, 93, 0.12);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .button,
            .card,
            .notice-card,
            .metric,
            .service-card,
            .photo-card,
            .community-map-card,
            .info-box,
            .list-item,
            .intro-stat {
                transition: none;
            }
        }

        .hero {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 20px;
            padding: 32px 0 22px;
            background: transparent;
            border: 0;
            box-shadow: none;
        }

        .eyebrow {
            margin-bottom: 8px;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--secondary);
            font-weight: 700;
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
            background: #E2E8F0;
        }

        .photo-copy {
            padding: 18px 18px 20px;
        }

        .photo-copy h3 {
            margin-bottom: 8px;
            color: var(--primary);
        }

        .notice-stack {
            margin-top: 16px;
        }

        .notice-item {
            padding: 14px 16px;
            border-radius: 18px;
            background: var(--surface-strong);
            border: 1px solid var(--line);
        }

        .notice-item strong {
            display: block;
            margin-bottom: 6px;
            color: var(--primary);
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
            color: var(--primary);
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
            color: var(--primary);
        }

        .metrics {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .metric strong {
            display: block;
            margin-top: 8px;
            font-size: 2rem;
            color: var(--primary);
        }

        .content-grid {
            grid-template-columns: 1.1fr 0.9fr;
        }

        .request-layout {
            align-items: start;
        }

        .form-card {
            overflow: hidden;
        }

        .card-header {
            margin-bottom: 18px;
        }

        .compact {
            margin-bottom: 6px;
            letter-spacing: 0.08em;
        }

        .request-form {
            display: grid;
            gap: 20px;
        }

        .form-grid {
            display: grid;
            gap: 16px;
        }

        .two-col {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .three-col {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .four-col {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .single-col {
            grid-template-columns: 1fr;
        }

        .request-form label {
            display: grid;
            gap: 8px;
            color: var(--text);
            font-size: 0.84rem;
            font-weight: 700;
        }

        .request-form input,
        .request-form select {
            width: 100%;
            min-height: 46px;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #FFFFFF;
            color: var(--text);
            font: inherit;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .request-form input:focus,
        .request-form select:focus {
            outline: 0;
            border-color: var(--secondary);
            box-shadow: 0 0 0 4px rgba(43, 108, 176, 0.14);
        }

        .status-box {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 12px;
            font-weight: 700;
        }

        .status-box.success {
            background: rgba(43, 108, 176, 0.1);
            color: var(--primary);
            border-left: 4px solid var(--secondary);
        }

        .status-box.error {
            background: rgba(197, 48, 48, 0.1);
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        .submit-row {
            display: flex;
            justify-content: flex-start;
            margin-top: 8px;
        }

        .side-card {
            display: grid;
            gap: 18px;
        }

        .process-list {
            display: grid;
            gap: 12px;
        }

        .process-step {
            padding: 14px 16px;
            border-radius: 16px;
            background: var(--surface-strong);
            border: 1px solid var(--line);
        }

        .info-box {
            padding: 18px 18px 10px;
            border-radius: 18px;
            background: rgba(214, 158, 46, 0.08);
            border: 1px solid rgba(214, 158, 46, 0.22);
        }

        .info-box ul {
            margin: 0;
            padding-left: 18px;
            color: var(--muted);
            line-height: 1.8;
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
            border-bottom: 1px solid var(--line);
            color: var(--muted);
        }

        .table th {
            color: var(--primary);
            font-weight: 700;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            background: rgba(43, 108, 176, 0.12);
            color: var(--secondary);
        }

        .info-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .site-footer {
            background: var(--primary);
            color: #F7FAFC;
            padding: 28px 0 32px;
            margin-top: 48px;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        .footer-inner {
            width: min(1200px, calc(100% - 32px));
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .footer-inner .footer-note {
            color: rgba(247, 250, 252, 0.8);
            font-size: 0.88rem;
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
            .section-head,
            .footer-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-inner {
                gap: 14px;
                padding: 12px 0;
            }

            .brand {
                align-items: flex-start;
            }

            .brand small {
                line-height: 1.4;
            }

            .nav {
                width: 100%;
                justify-content: flex-start;
                gap: 8px 14px;
            }

            .nav a {
                min-height: 44px;
                display: inline-flex;
                align-items: center;
            }

            .nav .button {
                padding: 10px 14px;
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

            .two-col,
            .three-col,
            .four-col {
                grid-template-columns: 1fr;
            }

            .table {
                display: block;
                max-width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table th,
            .table td {
                white-space: nowrap;
            }

            .request-form input,
            .request-form select,
            .request-form button,
            .button {
                min-height: 44px;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <div class="brand">
                <img src="{{ asset('images/bagumbayan_logo.png') }}" alt="Barangay Bagumbayan Logo" class="brand-mark">
                <div class="brand-copy">
                    <strong>Barangay Bagumbayan</strong>
                    <small>Daraga, Albay Barangay Management System Prototype</small>
                </div>
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

    <footer class="site-footer">
        <div class="footer-inner">
            <p class="footer-note">&copy; {{ date('Y') }} Barangay Bagumbayan, Daraga, Albay &bull; Local Governance Portal</p>
            <p class="footer-note">Municipal Navy &bull; Mayon Sky Blue &bull; Sun Gold Palette</p>
        </div>
    </footer>
</body>
</html>
