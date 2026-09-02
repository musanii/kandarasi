<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Create your Kandarasi workspace</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #1A2B4A; color: #fff;
               display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: #fff; color: #1a1a1a; padding: 2.5rem; border-radius: 12px; width: 100%; max-width: 420px; }
        .card h1 { margin: 0 0 0.25rem; font-size: 1.25rem; }
        .card p.sub { margin: 0 0 1.5rem; color: #666; font-size: 0.9rem; }
        label { display: block; font-size: 0.85rem; margin-bottom: 0.3rem; color: #333; }
        input { width: 100%; padding: 0.6rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        .slug-preview { font-size: 0.8rem; color: #888; margin: -0.75rem 0 1rem; }
        .slug-preview strong { color: #1A2B4A; }
        button { width: 100%; padding: 0.7rem; border: none; border-radius: 6px;
                 background: #C0392B; color: #fff; font-weight: 600; cursor: pointer; }
        .error { color: #C0392B; font-size: 0.8rem; margin: -0.75rem 0 1rem; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Create your workspace</h1>
        <p class="sub">Your own Kandarasi subdomain, ready in seconds.</p>

        <form method="POST" action="{{ route('signup') }}">
            @csrf

            <label>Organization name</label>
            <input type="text" name="organization_name" id="organization_name" value="{{ old('organization_name') }}" required>
            @error('organization_name') <div class="error">{{ $message }}</div> @enderror

            <label>Subdomain</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required
                   pattern="[a-z0-9]+(-[a-z0-9]+)*">
            <div class="slug-preview">Your workspace: <strong id="slug-preview-text">yourorg</strong>.kandarasi.app</div>
            @error('slug') <div class="error">{{ $message }}</div> @enderror

            <label>Your name</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
            @error('name') <div class="error">{{ $message }}</div> @enderror

            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
            @error('email') <div class="error">{{ $message }}</div> @enderror

            <label>Password</label>
            <input type="password" name="password" required>
            @error('password') <div class="error">{{ $message }}</div> @enderror

            <label>Confirm password</label>
            <input type="password" name="password_confirmation" required>

            <button type="submit">Create workspace</button>
        </form>
    </div>

    <script>
        const slugInput = document.getElementById('slug');
        const preview = document.getElementById('slug-preview-text');
        slugInput.addEventListener('input', () => {
            preview.textContent = slugInput.value.toLowerCase().trim() || 'yourorg';
        });
    </script>
</body>
</html>
