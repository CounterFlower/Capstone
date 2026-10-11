<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #F7FAFC;
            --sidebar: #1A365D;
            --sidebar-soft: #0F2440;
            --surface: #FFFFFF;
            --surface-strong: #F7FAFC;
            --primary: #1A365D;
            --secondary: #2B6CB0;
            --secondary-hover: #2C5282;
            --accent: #D69E2E;
            --accent-hover: #B7791F;
            --text: #2D3748;
            --muted: #718096;
            --line: rgba(45, 55, 72, 0.12);
            --success: #2B6CB0;
            --danger: #C53030;
            --warn: #D69E2E;
            --shadow: 0 18px 44px rgba(26, 54, 93, 0.08);
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

        .shell {
            display: grid;
            grid-template-columns: 290px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            padding: 28px 22px;
            background: linear-gradient(180deg, var(--sidebar) 0%, var(--sidebar-soft) 100%);
            color: #F7FAFC;
        }

        .brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 12px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .brand-mark {
            width: 100%;
            max-width: 100%;
            height: auto;
            display: block;
            object-fit: contain;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.04);
            padding: 10px;
            box-shadow: 0 8px 24px rgba(15, 36, 64, 0.16);
        }

        .brand-copy {
            display: grid;
            gap: 4px;
            width: 100%;
        }

        .sidebar h2 {
            margin: 0;
            color: #FFFFFF;
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            line-height: 1.2;
        }

        .sidebar p {
            margin: 0;
            line-height: 1.6;
            color: rgba(247, 250, 252, 0.78);
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
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: rgba(247, 250, 252, 0.88);
            text-align: left;
            font: inherit;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .side-link small {
            display: block;
            margin-top: 5px;
            color: inherit;
            opacity: 0.76;
        }

        .side-link.active {
            background: var(--secondary);
            border-color: var(--secondary);
            color: #FFFFFF;
            box-shadow: 0 4px 14px rgba(43, 108, 176, 0.35);
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
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.06);
            color: #F7FAFC;
            font: inherit;
            text-align: left;
            cursor: pointer;
            transition: color 180ms ease, background-color 180ms ease, border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
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

        .topbar p,
        .card p,
        .table td,
        .subtext {
            color: var(--muted);
        }

        .eyebrow {
            margin-bottom: 8px;
            font-size: 0.78rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--secondary);
            font-weight: 700;
        }

        .pill {
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(43, 108, 176, 0.12);
            color: var(--secondary);
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
            transition: all 0.2s ease;
        }

        .tab:hover {
            border-color: var(--secondary);
            background: #F7FAFC;
        }

        .tab small {
            display: block;
            margin-top: 6px;
            color: var(--muted);
            font-weight: 400;
        }

        .tab.active {
            background: #FFFFFF;
            border-color: var(--secondary);
            border-top: 3px solid var(--secondary);
            box-shadow: 0 4px 16px rgba(43, 108, 176, 0.16);
            color: var(--primary);
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

        .resident-table-card {
            grid-column: 1 / -1;
        }

        .card,
        .stat {
            padding: 24px;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 24px;
            box-shadow: var(--shadow);
            transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease;
        }

        .stat span {
            display: block;
            color: var(--muted);
        }

        .stat strong {
            display: block;
            margin-top: 8px;
            font-size: 2rem;
            color: var(--primary);
        }

        .card h2,
        .card h3 {
            margin-bottom: 16px;
            color: var(--primary);
        }

        .admin-form {
            display: grid;
            gap: 18px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .form-field {
            display: grid;
            gap: 7px;
            color: var(--text);
            font-size: 0.86rem;
            font-weight: 700;
        }

        .form-field input,
        .form-field textarea {
            width: 100%;
            min-height: 46px;
            padding: 11px 13px;
            border: 1px solid var(--line);
            border-radius: 12px;
            outline: 0;
            background: #FFFFFF;
            color: var(--text);
            font: inherit;
            font-weight: 400;
            transition: border-color 160ms ease, box-shadow 160ms ease, background 160ms ease;
        }

        .form-field textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-field input::placeholder,
        .form-field textarea::placeholder {
            color: #A0AEC0;
        }

        .form-field input:focus,
        .form-field textarea:focus {
            border-color: var(--secondary);
            background: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(43, 108, 176, 0.14);
        }

        .form-section-label {
            display: block;
            margin: 2px 0 -4px;
            color: var(--secondary);
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .form-submit {
            justify-self: start;
            padding: 12px 18px;
            border: 0;
            border-radius: 12px;
            background: var(--accent);
            color: #FFFFFF;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(214, 158, 46, 0.28);
            transition: background 160ms ease, transform 160ms ease;
        }

        .content button:not(:disabled),
        .content a.button {
            transition: color 180ms ease, background-color 180ms ease, border-color 180ms ease, box-shadow 180ms ease, filter 180ms ease, transform 180ms ease;
        }

        .content button:focus-visible,
        .content a.button:focus-visible,
        .side-actions a:focus-visible,
        .side-actions button:focus-visible {
            outline: 3px solid rgba(214, 158, 46, 0.75);
            outline-offset: 3px;
        }

        .content button:active,
        .content a.button:active {
            transform: translateY(0);
            filter: brightness(0.9);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .resident-table {
            table-layout: fixed;
            font-size: 0.86rem;
        }

        .resident-table th,
        .resident-table td {
            padding: 10px 8px;
            overflow-wrap: anywhere;
        }

        .resident-table th {
            color: var(--primary);
            font-weight: 700;
            border-bottom: 2px solid var(--line);
        }

        .resident-table th:nth-child(1) {
            width: 14%;
        }

        .resident-table th:nth-child(2),
        .resident-table th:nth-child(3) {
            width: 8%;
        }

        .resident-table th:nth-child(4) {
            width: 10%;
        }

        .resident-table th:nth-child(5) {
            width: 11%;
        }

        .resident-table th:nth-child(6) {
            width: 10%;
        }

        .resident-table th:nth-child(7) {
            width: 8%;
        }

        .resident-table th:nth-child(8) {
            width: 10%;
        }

        .resident-table th:nth-child(9) {
            width: 10%;
        }

        .resident-table th:nth-child(10),
        .resident-table th:nth-child(11) {
            width: 9%;
        }

        .resident-table .form-submit {
            padding: 8px 10px;
            font-size: 0.78rem;
        }

        .resident-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .resident-actions form {
            margin: 0;
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

        .list {
            display: grid;
            gap: 12px;
        }

        .list-item,
        .bar {
            padding: 14px 16px;
            border-radius: 16px;
            background: var(--surface-strong);
            border: 1px solid var(--line);
            transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease;
        }

        @media (hover: hover) {
            .side-link:hover {
                background: rgba(255, 255, 255, 0.12);
                color: #FFFFFF;
            }

            .side-actions a:hover,
            .side-actions button:hover {
                background: rgba(255, 255, 255, 0.14);
                color: #FFFFFF;
            }

            .form-submit:hover {
                background: var(--accent-hover);
                transform: translateY(-1px);
            }
        }

        @media (hover: hover) and (prefers-reduced-motion: no-preference) {
            .card:hover,
            .stat:hover,
            .list-item:hover {
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
            .side-actions a,
            .side-actions button,
            .card,
            .stat,
            .list-item,
            .content button,
            .content a.button {
                transition: none;
            }
        }

        .list-item strong {
            display: block;
            margin-bottom: 6px;
            color: var(--primary);
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
            background: rgba(43, 108, 176, 0.12);
            color: var(--secondary);
        }

        .badge.warn {
            background: rgba(214, 158, 46, 0.15);
            color: #B7791F;
        }

        .badge.alert {
            background: rgba(197, 48, 48, 0.12);
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
            background: rgba(45, 55, 72, 0.08);
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(120deg, var(--accent), var(--secondary));
        }

        @media (max-width: 1180px) {
            .tabs,
            .stats,
            .grid,
            .panels,
            .shell {
                grid-template-columns: 1fr;
            }

            .tabs {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .content,
            .sidebar {
                padding: 16px;
            }

            .topbar {
                flex-direction: column;
            }

            .brand-mark {
                width: min(100%, 220px);
            }

            .side-links {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px;
            }

            .side-link {
                height: 100%;
                padding: 12px;
                font-size: 0.92rem;
            }

            .side-actions {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .side-actions form {
                margin: 0;
            }

            .tabs {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .card,
            .stat,
            .tab {
                padding: 16px;
                border-radius: 20px;
            }

            .table-wrap {
                max-width: 100%;
                -webkit-overflow-scrolling: touch;
            }

            .resident-table {
                min-width: 900px;
            }

            .content .form-submit,
            .content button:not(:disabled),
            .content a.button,
            .side-actions a,
            .side-actions button {
                min-height: 44px;
            }
        }
    </style>
</head>
<body data-active-tab="{{ $activeTab ?? 'overview' }}">
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">
                <img src="{{ asset('images/bagumbayan_logo.png') }}" alt="Barangay Bagumbayan Logo" class="brand-mark"><br>
                <div class="brand-copy">
                    <h2>Barangay Bagumbayan</h2>
                </div>
            </div>

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
        const activeTab = document.body.dataset.activeTab || 'overview';

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
            activateTab(activeTab);
        }

        document.querySelectorAll('input[name="active_tab"]').forEach((input) => {
            input.value = activeTab;
        });

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('input[name="active_tab"]').forEach((input) => {
                    input.value = tab.dataset.tabTarget;
                });
            });
        });

        document.querySelectorAll('.resident-edit').forEach((button) => {
            button.addEventListener('click', () => {
                document.querySelector('#edit-resident-id').value = button.dataset.residentId;
                document.querySelector('#edit-first-name').value = button.dataset.firstName;
                document.querySelector('#edit-middle-name').value = button.dataset.middleName;
                document.querySelector('#edit-last-name').value = button.dataset.lastName;
                document.querySelector('#edit-date-of-birth').value = button.dataset.dateOfBirth;
                document.querySelector('#edit-place-of-birth').value = button.dataset.placeOfBirth;
                document.querySelector('#edit-gender').value = button.dataset.gender;
                document.querySelector('#edit-civil-status').value = button.dataset.civilStatus;
                document.querySelector('#edit-contact-number').value = button.dataset.contactNumber;
                document.querySelector('#edit-household-id').value = button.dataset.householdId;
                document.querySelector('#edit-house-number').value = button.dataset.houseNumber;
                document.querySelector('#edit-zone-purok').value = button.dataset.zonePurok;
                document.querySelector('#resident-details-form').scrollIntoView({ behavior: 'smooth', block: 'center' });
                document.querySelector('#edit-first-name').focus();
            });
        });
    </script>
</body>
</html>
