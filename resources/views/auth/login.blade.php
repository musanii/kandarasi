<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $organization->name }} — Kandarasi</title>
    <style>
        :root {
            --primary-color: {{ $branding->primary_color ?? '#1A2B4A' }};
            --secondary-color: {{ $branding->secondary_color ?? '#C0392B' }};
            --accent-color: {{ $branding->accent_color ?? '#00A886' }};
        }
        body {
            font-family: system-ui, sans-serif;
            background: var(--primary-color);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .card {
            background: #fff;
            color: #1a1a1a;
            padding: 2.5rem;
            border-radius: 12px;
            width: 100%;
            max-width: 380px;
        }
        .card h1 { margin: 0 0 1.5rem; font-size: 1.25rem; }
        .card img.logo { max-height: 40px; margin-bottom: 1rem; }
        input {
            width: 100%; padding: 0.6rem; margin-bottom: 1rem;
            border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box;
        }
        button {
            width: 100%; padding: 0.7rem; border: none; border-radius: 6px;
            background: var(--secondary-color); color: #fff; font-weight: 600;
            cursor: pointer;
        }
        .error { color: var(--secondary-color); font-size: 0.85rem; margin: -0.75rem 0 1rem; }
    </style>
</head>
<body>
    <div class="card">
        @if ($branding->logo_url ?? null)
            <img class="logo" src="{{ $branding->logo_url }}" alt="{{ $organization->name }} logo">
        @endif
        <h1>Sign in to {{ $organization->name }}</h1>

        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign in</button>
        </form>
    </div>
</body>
</html>
