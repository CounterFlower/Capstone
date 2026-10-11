<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <style>
        :root {
            --text: #2D3748;
            --muted: #718096;
            --line: rgba(45, 55, 72, 0.12);
            --surface: #FFFFFF;
            --hero: #1A365D;
            --hero-soft: #2B6CB0;
            --accent: #D69E2E;
            --accent-hover: #B7791F;
            --secondary: #2B6CB0;
            --shadow: 0 20px 48px rgba(26, 54, 93, 0.14);
            --danger: #C53030;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
            font-family: "Trebuchet MS", "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(214, 158, 46, 0.12), transparent 26%),
                radial-gradient(circle at bottom right, rgba(43, 108, 176, 0.12), transparent 28%),
                linear-gradient(160deg, #FFFFFF 0%, #F7FAFC 100%);
        }

        h1, h2, p {
            margin: 0;
        }

        h1, h2 {
            font-family: Georgia, "Times New Roman", serif;
        }

        .login-shell {
            width: min(980px, 100%);
            display: grid;
            grid-template-columns: 1fr 420px;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: var(--shadow);
            border: 1px solid var(--line);
            background: var(--surface);
        }

        .intro {
            padding: 38px;
            color: #F7FAFC;
            background: linear-gradient(145deg, var(--hero) 0%, #0F2440 100%);
        }

        .intro h1 {
            color: #FFFFFF;
        }

        .intro p {
            margin-top: 14px;
            line-height: 1.8;
            color: rgba(247, 250, 252, 0.82);
        }

        .panel {
            padding: 38px;
            background: #FFFFFF;
        }

        .panel h2 {
            color: var(--hero);
        }

        .eyebrow {
            margin-bottom: 8px;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--accent);
            font-weight: 700;
        }

        .subtext,
        label {
            color: var(--muted);
        }

        form {
            display: grid;
            gap: 16px;
            margin-top: 24px;
        }

        label {
            display: grid;
            gap: 8px;
            font-weight: 700;
            color: var(--text);
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: #FFFFFF;
            font: inherit;
            color: var(--text);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        input:focus {
            outline: 0;
            border-color: var(--secondary);
            box-shadow: 0 0 0 4px rgba(43, 108, 176, 0.14);
        }

        button,
        .back-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 13px 18px;
            border-radius: 14px;
            border: 0;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        button {
            color: #FFFFFF;
            background: var(--accent);
            box-shadow: 0 4px 14px rgba(214, 158, 46, 0.28);
        }

        button:hover {
            background: var(--accent-hover);
        }

        .back-link {
            margin-top: 12px;
            background: #F7FAFC;
            border: 1px solid var(--line);
            color: var(--secondary);
            text-decoration: none;
        }

        .back-link:hover {
            background: #EDF2F7;
            color: var(--hero);
        }

        .error-box {
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(197, 48, 48, 0.1);
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        @media (max-width: 920px) {
            .login-shell {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="login-shell">
        <section class="intro">
            <p class="eyebrow">Administrative Access</p>
            <h1>Barangay Management Admin Login</h1>
            <p>Use your database-backed admin credentials to access the barangay dashboard.</p>
        </section>

        <section class="panel">
            <p class="eyebrow">Login</p>
            <h2>Enter administrator credentials</h2>

            @if (session('status'))
                <div class="error-box" style="margin-top: 18px; background: rgba(42, 124, 76, 0.12); color: #1f583a;">{{ session('status') }}</div>
            @endif

            @if ($errors->has('login'))
                <div class="error-box" style="margin-top: 18px;">{{ $errors->first('login') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf
                <label>
                    Username
                    <input type="text" name="username" value="{{ old('username') }}" required>
                </label>

                <label>
                    Password
                    <input type="password" name="password" required>
                </label>

                <button type="submit">Login to Dashboard</button>
            </form>

            <div style="margin-top: 14px; display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                <a class="back-link" href="{{ route('admin.register') }}">Create Admin Account</a>
                <a class="back-link" href="{{ route('home') }}">Back to Public Site</a>
            </div>
        </section>
    </div>
</body>
</html>
