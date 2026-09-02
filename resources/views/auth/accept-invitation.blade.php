<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Join {{ $invitation->organization->name }} on Kandarasi</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #1A2B4A; color: #fff;
               display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: #fff; color: #1a1a1a; padding: 2.5rem; border-radius: 12px; width: 100%; max-width: 380px; }
        input { width: 100%; padding: 0.6rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 0.7rem; border: none; border-radius: 6px; background: #C0392B; color: #fff; font-weight: 600; cursor: pointer; }
        .error { color: #C0392B; font-size: 0.85rem; margin: -0.75rem 0 1rem; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Join {{ $invitation->organization->name }}</h1>
        <p>You've been invited as <strong>{{ $invitation->role }}</strong>. Set your name and password to get started.</p>

        <form method="POST" action="{{ route('invitations.accept.store', $invitation->token) }}">
            @csrf
            <input type="text" name="name" placeholder="Your name" required>
            @error('name') <div class="error">{{ $message }}</div> @enderror

            <input type="password" name="password" placeholder="Password" required>
            @error('password') <div class="error">{{ $message }}</div> @enderror

            <input type="password" name="password_confirmation" placeholder="Confirm password" required>

            <button type="submit">Join workspace</button>
        </form>
    </div>
</body>
</html>
