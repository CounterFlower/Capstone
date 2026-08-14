<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Barangay Dashboard' }}</title>
    <style>
        :root {
            --bg: #edf3ec;
            --surface: rgba(255, 255, 255, 0.84);
            --surface-strong: #ffffff;
            --sidebar: #16372a;
            --sidebar-soft: #224b3b;
            --text: #173226;
            --muted: #64766f;
            --line: rgba(23, 50, 38, 0.1);
            --accent: #d59a34;
            --success: #2d7c54;
            --danger: #b45142;
            --shadow: 0 18px 42px rgba(23, 50, 38, 0.12);
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
                radial-gradient(circle at top left, rgba(213, 154, 52, 0.15), transparent 24%),
                linear-gradient(160deg, #f4f0dd 0%, #e9f2ea 100%);
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

        .app-shell {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            padding: 28px 22px;
            background: linear-gradient(180deg, var(--sidebar) 0%, var(--sidebar-soft) 100%);
            color: #f7f6ef;
        }

        .brand {
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .brand p {
            margin-top: 6px;
            color: rgba(247, 246, 239, 0.72);
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
            color: rgba(247, 246, 239, 0.82);
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .nav-link.active {
            color: #13261d;
            background: #f7f0de;
            border-color: rgba(213, 154, 52, 0.3);
        }

        .nav-link small {
            display: block;
            margin-top: 5px;
            color: inherit;
            opacity: 0.74;
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

        .eyebrow {
            margin-bottom: 8px;
            font-size: 0.78rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--success);
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
            background: rgba(45, 124, 84, 0.12);
            color: var(--success);
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
            color: var(--text);
        }

        .card {
            padding: 24px;
        }

        .card h3 {
            margin-bottom: 16px;
            font-size: 1.4rem;
        }

        .card p,
        .card li,
        .table td,
        .table th {
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
            border: 1px solid rgba(23, 50, 38, 0.08);
        }

        .list-item strong,
        .queue-item strong,
        .notice-item strong {
            display: block;
            margin-bottom: 6px;
            color: var(--text);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid rgba(23, 50, 38, 0.08);
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
            background: rgba(213, 154, 52, 0.16);
            color: #8f6316;
        }

        .badge.good {
            background: rgba(45, 124, 84, 0.13);
            color: var(--success);
        }

        .badge.alert {
            background: rgba(180, 81, 66, 0.14);
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
                padding: 20px;
            }

            .topbar {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <h2>Bagumbayan</h2>
                <p>Barangay operations prototype focused on records, services, analytics, and case monitoring.</p>
            </div>

            <nav>
                <a class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}" href="{{ route('dashboard.index') }}">
                    Prototype Hub
                    <small>Overview of all five layouts</small>
                </a>
                <a class="nav-link {{ request()->routeIs('dashboard.admin') ? 'active' : '' }}" href="{{ route('dashboard.admin') }}">
                    Administrative Dashboard
                    <small>2.1.4 Analytics reporting and decision support</small>
                </a>
                <a class="nav-link {{ request()->routeIs('dashboard.documents') ? 'active' : '' }}" href="{{ route('dashboard.documents') }}">
                    Document Request
                    <small>2.1.1 Manual, paper-based workflows</small>
                </a>
                <a class="nav-link {{ request()->routeIs('dashboard.residents') ? 'active' : '' }}" href="{{ route('dashboard.residents') }}">
                    Resident Profiles
                    <small>2.1.2 Centralized records management</small>
                </a>
                <a class="nav-link {{ request()->routeIs('dashboard.incidents') ? 'active' : '' }}" href="{{ route('dashboard.incidents') }}">
                    Incident Monitoring
                    <small>2.1.5 Incident reporting and case monitoring</small>
                </a>
                <a class="nav-link {{ request()->routeIs('dashboard.announcements') ? 'active' : '' }}" href="{{ route('dashboard.announcements') }}">
                    Announcements Board
                    <small>2.1.3 Communication and public services</small>
                </a>
            </nav>
        </aside>

        <main class="content">
            @yield('content')
        </main>
    </div>
</body>
</html>
