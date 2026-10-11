<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Barangay Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #F7FAFC;
            --surface: #FFFFFF;
            --surface-strong: #F7FAFC;
            --sidebar: #1A365D;
            --sidebar-soft: #0F2440;
            --primary: #1A365D;
            --secondary: #2B6CB0;
            --secondary-hover: #2C5282;
            --text: #2D3748;
            --muted: #718096;
            --line: rgba(45, 55, 72, 0.12);
            --accent: #D69E2E;
            --accent-hover: #B7791F;
            --success: #2B6CB0;
            --danger: #C53030;
            --shadow: 0 18px 42px rgba(26, 54, 93, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Trebuchet MS", "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(214, 158, 46, 0.10), transparent 24%),
                radial-gradient(circle at top right, rgba(43, 108, 176, 0.10), transparent 22%),
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

        .app-shell {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            padding: 28px 22px;
            background: linear-gradient(180deg, var(--sidebar) 0%, var(--sidebar-soft) 100%);
            color: #F7FAFC;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
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

        .brand h2 {
            color: #FFFFFF;
        }

        .brand p {
            margin-top: 0;
            color: rgba(247, 250, 252, 0.78);
            line-height: 1.6;
        }

        .sidebar nav {
            display: grid;
            gap: 10px;
            margin-top: 22px;
        }

        .nav-link {
            padding: 14px 16px;
            border-radius: 16px;
            color: rgba(247, 250, 252, 0.86);
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            display: block;
            transition: all 0.2s ease;
        }

        .nav-link.active {
            color: #FFFFFF;
            background: var(--secondary);
            border-color: var(--secondary);
            box-shadow: 0 4px 14px rgba(43, 108, 176, 0.35);
        }

        .nav-link small {
            display: block;
            margin-top: 5px;
            color: inherit;
            opacity: 0.76;
        }

        .content {
            padding: 28px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: start;
            margin-bottom: 24px;
        }

        .topbar h1 {
            color: var(--primary);
        }

        .eyebrow {
            margin-bottom: 8px;
            font-size: 0.78rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--secondary);
            font-weight: 700;
        }

        .topbar p {
            color: var(--muted);
            margin-top: 8px;
            line-height: 1.7;
            max-width: 70ch;
        }

        .status-pill {
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(43, 108, 176, 0.12);
            color: var(--secondary);
            font-weight: 700;
            white-space: nowrap;
        }

        .stats,
        .split,
        .panel-grid {
            display: grid;
            gap: 18px;
        }

        .stats {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 22px;
        }

        .split {
            grid-template-columns: 1.2fr 0.8fr;
        }

        .panel-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-top: 18px;
        }

        .card,
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 24px;
            box-shadow: var(--shadow);
            transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease;
        }

        .stat-card {
            padding: 20px;
        }

        .stat-card span {
            display: block;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .stat-card strong {
            display: block;
            margin-top: 10px;
            font-size: 2rem;
            color: var(--primary);
        }

        .card {
            padding: 24px;
        }

        .card h3 {
            margin-bottom: 16px;
            font-size: 1.4rem;
            color: var(--primary);
        }

        .card p,
        .card li,
        .table td {
            color: var(--muted);
        }

        .list {
            display: grid;
            gap: 12px;
        }

        .list-item,
        .queue-item,
        .notice-item {
            padding: 14px 16px;
            border-radius: 16px;
            background: var(--surface-strong);
            border: 1px solid var(--line);
            transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease;
        }

        .content button:not(:disabled),
        .content a.button {
            transition: color 180ms ease, background-color 180ms ease, border-color 180ms ease, box-shadow 180ms ease, filter 180ms ease, transform 180ms ease;
        }

        .content button:focus-visible,
        .content a.button:focus-visible {
            outline: 3px solid rgba(214, 158, 46, 0.7);
            outline-offset: 3px;
        }

        .content button:active,
        .content a.button:active {
            transform: translateY(0);
            filter: brightness(0.9);
        }

        @media (hover: hover) {
            .nav-link:hover {
                background: rgba(255, 255, 255, 0.12);
                color: #FFFFFF;
            }
        }

        @media (hover: hover) and (prefers-reduced-motion: no-preference) {
            .card:hover,
            .stat-card:hover,
            .list-item:hover,
            .queue-item:hover,
            .notice-item:hover {
                transform: translateY(-3px);
                border-color: rgba(43, 108, 176, 0.28);
                box-shadow: 0 14px 30px rgba(26, 54, 93, 0.12);
            }
        }

        @media (hover: hover) {
            .content button:not(:disabled):hover,
            .content a.button:hover {
                filter: brightness(0.92) saturate(1.12);
                box-shadow: 0 6px 16px rgba(26, 54, 93, 0.18);
            }
        }

        @media (hover: hover) and (prefers-reduced-motion: no-preference) {
            .content button:not(:disabled):hover,
            .content a.button:hover {
                transform: translateY(-2px);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .card,
            .stat-card,
            .list-item,
            .queue-item,
            .notice-item,
            .content button,
            .content a.button {
                transition: none;
            }
        }

        .list-item strong,
        .queue-item strong,
        .notice-item strong {
            display: block;
            margin-bottom: 6px;
            color: var(--primary);
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
        }

        .badge.warn {
            background: rgba(214, 158, 46, 0.15);
            color: #B7791F;
        }

        .badge.good {
            background: rgba(43, 108, 176, 0.12);
            color: var(--secondary);
        }

        .badge.alert {
            background: rgba(197, 48, 48, 0.12);
            color: var(--danger);
        }

        @media (max-width: 1080px) {
            .app-shell,
            .stats,
            .split,
            .panel-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .content,
            .sidebar {
                padding: 16px;
            }

            .topbar {
                flex-direction: column;
            }

            .sidebar nav {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px;
            }

            .sidebar nav form {
                grid-column: 1 / -1;
            }

            .nav-link {
                height: 100%;
                padding: 12px;
                font-size: 0.92rem;
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

            .content button:not(:disabled),
            .content a.button {
                min-height: 44px;
            }
        }
    </style>
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <img src="{{ asset('images/bagumbayan_logo.png') }}" alt="Barangay Bagumbayan Logo" class="brand-mark">
                <div class="brand-copy">
                    <h2>Bagumbayan</h2>
                    <p>Barangay operations prototype focused on records, services, analytics, and case monitoring.</p>
                </div>
            </div>

            @php
                $currentTab = request()->query('tab', $activeTab ?? 'overview');
            @endphp

            <nav>
                <a class="nav-link {{ $currentTab === 'overview' ? 'active' : '' }}" href="{{ route('admin.dashboard', ['tab' => 'overview']) }}">
                    Overview
                    <small>System summary and status</small>
                </a>
                <a class="nav-link {{ $currentTab === 'cases' ? 'active' : '' }}" href="{{ route('admin.dashboard', ['tab' => 'cases']) }}">
                    Incident Monitoring
                    <small>Incident reporting and case monitoring</small>
                </a>
                <a class="nav-link {{ $currentTab === 'documents' ? 'active' : '' }}" href="{{ route('admin.dashboard', ['tab' => 'documents']) }}">
                    Document Requests
                    <small>Clearances and certifications</small>
                </a>
                <a class="nav-link {{ $currentTab === 'residents' ? 'active' : '' }}" href="{{ route('admin.dashboard', ['tab' => 'residents']) }}">
                    Resident Profiles
                    <small>Centralized records management</small>
                </a>
                <a class="nav-link {{ $currentTab === 'events' ? 'active' : '' }}" href="{{ route('admin.dashboard', ['tab' => 'events']) }}">
                    Events & Announcements
                    <small>Community schedules and notices</small>
                </a>
                
                <form method="POST" action="{{ route('admin.logout') }}" style="margin-top: 16px;">
                    @csrf
                    <button type="submit" style="width: 100%; text-align: left; background: transparent; border: 0; padding: 0; color: inherit; font: inherit; cursor: pointer;">
                        <span class="nav-link" style="color: #fca5a5; border-color: rgba(239, 68, 68, 0.2);">
                            Log Out
                            <small style="color: rgba(252, 165, 165, 0.75);">End administrative session</small>
                        </span>
                    </button>
                </form>
            </nav>
        </aside>

        <main class="content">
            @yield('content')
        </main>
    </div>
    <script>
(function() {
    const INACTIVITY_LIMIT_MS = 2 * 60 * 1000; // 2 minutes in milliseconds
    let timeoutId;

    const logoutUrl = "{{ route('admin.login') }}";

    function performAutoLogout() {
        // Create an automated logout request or redirect with notice
        window.location.href = logoutUrl + '?reason=inactivity';
    }

    function resetTimer() {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(performAutoLogout, INACTIVITY_LIMIT_MS);
    }

    // User interaction events that reset the 2-minute timer
    const activityEvents = ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'click'];
    activityEvents.forEach(function(event) {
        window.addEventListener(event, resetTimer, { passive: true });
    });

    // Start timer on page load
    resetTimer();
})();
</script>
</body>
</html>