<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Dashboard' }}</title>
    <style>
        :root {
            --bg: #eef3ef;
            --sidebar: #132d22;
            --sidebar-soft: #204635;
            --surface: rgba(255, 255, 255, 0.88);
            --surface-strong: #ffffff;
            --text: #173024;
            --muted: #66766f;
            --line: rgba(23, 48, 36, 0.1);
            --accent: #d59f39;
            --success: #2d7c54;
            --danger: #b55343;
            --warn: #94671d;
            --shadow: 0 18px 44px rgba(23, 48, 36, 0.12);
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
                radial-gradient(circle at top left, rgba(213, 159, 57, 0.14), transparent 24%),
                linear-gradient(160deg, #f3efdd 0%, #edf3ef 100%);
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

        .shell {
            display: grid;
            grid-template-columns: 290px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            padding: 28px 22px;
            background: linear-gradient(180deg, var(--sidebar) 0%, var(--sidebar-soft) 100%);
            color: #f6f3ea;
        }

        .sidebar p {
            margin-top: 8px;
            line-height: 1.6;
            color: rgba(246, 243, 234, 0.76);
        }

        .side-links {
            display: grid;
            gap: 10px;
            margin-top: 24px;
        }

        .side-link {
            width: 100%;
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: rgba(246, 243, 234, 0.84);
            text-align: left;
            font: inherit;
            cursor: pointer;
        }

        .side-link small {
            display: block;
            margin-top: 5px;
            color: inherit;
            opacity: 0.72;
        }

        .side-link.active {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.18);
            color: #fffdf6;
        }

        .side-actions {
            display: grid;
            gap: 10px;
            margin-top: 18px;
        }

        .side-actions a,
        .side-actions button {
            width: 100%;
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.04);
            color: #f6f3ea;
            font: inherit;
            text-align: left;
            cursor: pointer;
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

        .topbar p,
        .card p,
        .table td,
        .table th,
        .subtext {
            color: var(--muted);
        }

        .eyebrow {
            margin-bottom: 8px;
            font-size: 0.78rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--success);
        }

        .pill {
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(45, 124, 84, 0.12);
            color: var(--success);
            font-weight: 700;
            white-space: nowrap;
        }

        .tabs,
        .stats,
        .grid,
        .panels {
            display: grid;
            gap: 18px;
        }

        .tabs {
            grid-template-columns: repeat(6, minmax(0, 1fr));
            margin-bottom: 22px;
        }

        .tab {
            padding: 16px 18px;
            border-radius: 18px;
            background: var(--surface);
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
            font-weight: 700;
            text-align: left;
            font: inherit;
            color: var(--text);
            cursor: pointer;
        }

        .tab small {
            display: block;
            margin-top: 6px;
            color: var(--muted);
            font-weight: 400;
        }

        .tab.active {
            background: linear-gradient(145deg, #f7f1df 0%, #fffef8 100%);
            border-color: rgba(213, 159, 57, 0.35);
        }

        .stats {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 22px;
        }

        .grid {
            grid-template-columns: 1.15fr 0.85fr;
            margin-bottom: 22px;
        }

        .panels {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .card,
        .stat {
            padding: 24px;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 24px;
            box-shadow: var(--shadow);
        }

        .stat span {
            display: block;
            color: var(--muted);
        }

        .stat strong {
            display: block;
            margin-top: 8px;
            font-size: 2rem;
        }

        .card h2,
        .card h3 {
            margin-bottom: 16px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid rgba(23, 48, 36, 0.08);
        }

        .list {
            display: grid;
            gap: 12px;
        }

        .list-item,
        .bar {
            padding: 14px 16px;
            border-radius: 16px;
            background: var(--surface-strong);
            border: 1px solid rgba(23, 48, 36, 0.08);
        }

        .list-item strong {
            display: block;
            margin-bottom: 6px;
            color: var(--text);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .badge.good {
            background: rgba(45, 124, 84, 0.13);
            color: var(--success);
        }

        .badge.warn {
            background: rgba(213, 159, 57, 0.16);
            color: var(--warn);
        }

        .badge.alert {
            background: rgba(181, 83, 67, 0.14);
            color: var(--danger);
        }

        .chart-row {
            margin-top: 14px;
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        .chart-row label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            color: var(--muted);
        }

        .bar-track {
            height: 12px;
            border-radius: 999px;
            background: rgba(23, 48, 36, 0.08);
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(120deg, var(--accent), var(--success));
        }

        @media (max-width: 1180px) {
            .tabs,
            .stats,
            .grid,
            .panels,
            .shell {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .content,
            .sidebar {
                padding: 20px;
            }

            .topbar {
                flex-direction: column;
            }

            .card,
            .stat,
            .tab {
                padding: 20px;
                border-radius: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <h2>Barangay Bagumbayan</h2>
            <p>Administrative prototype for resident records, request processing, incident monitoring, and reporting.</p>

            <nav class="side-links">
                <button type="button" class="side-link" data-tab-target="overview">
                    Overview
                    <small>Service load and headline metrics</small>
                </button>
                <button type="button" class="side-link" data-tab-target="cases">
                    Case Monitoring
                    <small>Track complaints and incident reports</small>
                </button>
                <button type="button" class="side-link" data-tab-target="residents">
                    Resident Profiles
                    <small>Sample resident records table</small>
                </button>
                <button type="button" class="side-link" data-tab-target="requests">
                    Document Requests
                    <small>Monitor resident document requests</small>
                </button>
                <button type="button" class="side-link" data-tab-target="events">
                    Event Sign-Ups
                    <small>Review resident activity registrations</small>
                </button>
                <button type="button" class="side-link" data-tab-target="analytics">
                    Analytics
                    <small>Sample statistical layouts</small>
                </button>
            </nav>

            <div class="side-actions">
                <a href="{{ route('home') }}">Back to Public Site</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </aside>

        <main class="content">
            @yield('content')
        </main>
    </div>

    <script>
        const tabs = document.querySelectorAll('[data-tab-target]');
        const panels = document.querySelectorAll('[data-tab-panel]');

        function activateTab(target) {
            tabs.forEach((tab) => {
                tab.classList.toggle('active', tab.dataset.tabTarget === target);
            });

            panels.forEach((panel) => {
                panel.classList.toggle('active', panel.dataset.tabPanel === target);
            });
        }

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => activateTab(tab.dataset.tabTarget));
        });

        if (tabs.length > 0) {
            activateTab(tabs[0].dataset.tabTarget);
        }
    </script>
</body>
</html>
