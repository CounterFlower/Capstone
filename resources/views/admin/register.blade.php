<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Admin</title>
    <style>
        :root {
            --text: #173024;
            --muted: #6b7c74;
            --line: rgba(23, 48, 36, 0.1);
            --surface: rgba(255, 255, 255, 0.9);
            --hero: #173729;
            --hero-soft: #2d5b46;
            --shadow: 0 20px 48px rgba(23, 48, 36, 0.16);
            --danger: #b55343;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
            font-family: "Trebuchet MS", "Segoe UI", sans-serif;
            color: var(--text);
            background: radial-gradient(circle at top left, rgba(213, 159, 57, 0.16), transparent 24%),
                linear-gradient(160deg, #f5efdd 0%, #edf3ef 100%);
        }

        .login-shell {
            width: min(980px, 100%);
            display: grid;
            grid-template-columns: 1fr 440px;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: var(--shadow);
            border: 1px solid var(--line);
            background: var(--surface);
        }

        .intro {
            padding: 38px;
            color: #f6f2e7;
            background: linear-gradient(145deg, var(--hero) 0%, var(--hero-soft) 100%);
        }

        .panel {
            padding: 38px;
        }

        h1, h2, p {
            margin: 0;
        }

        .eyebrow {
            margin-bottom: 8px;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #d59f39;
        }

        .intro p {
            margin-top: 14px;
            line-height: 1.8;
            color: rgba(246, 242, 231, 0.82);
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
            color: var(--muted);
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid rgba(23, 48, 36, 0.12);
            background: rgba(255, 255, 255, 0.92);
            font: inherit;
        }

        button, .back-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 13px 18px;
            border-radius: 14px;
            border: 0;
            font: inherit;
            font-weight: 700;
            text-decoration: none;
        }

        button {
            color: #fffdf5;
            background: linear-gradient(120deg, var(--hero-soft), var(--hero));
            cursor: pointer;
        }

        .back-link {
            background: rgba(23, 48, 36, 0.06);
            color: var(--text);
        }

        .error-box {
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(181, 83, 67, 0.12);
            color: var(--danger);
        }

        @media (max-width: 920px) {
            .login-shell { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="login-shell">
        <section class="intro">
            <p class="eyebrow">Administrative Access</p>
            <h1>Create an Admin Account</h1>
            <p>Register a new administrator record in the live system_user table so the barangay dashboard can authenticate against the database.</p>
        </section>

        <section class="panel">
            <p class="eyebrow">Registration</p>
            <h2>New administrator profile</h2>

            @if ($errors->any())
                <div class="error-box" style="margin-top: 18px;">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.register.submit') }}">
                @csrf

                <label>
                    Username
                    <input type="text" name="username" value="{{ old('username') }}" required>
                </label>

                <label>
                    Full Name
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required>
                </label>

                <label>
                    Password
                    <input type="password" name="password" required>
                </label>

                <label>
                    Confirm Password
                    <input type="password" name="password_confirmation" required>
                </label>

                <button type="submit">Create Admin Account</button>
            </form>

            <div style="margin-top: 14px; display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                <a class="back-link" href="{{ route('admin.login') }}">Back to Login</a>
                <a class="back-link" href="{{ route('home') }}">Back to Public Site</a>
            </div>
        </section>
    </div>
</body>
</html>
