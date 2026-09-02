<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Team — {{ $organization->name }}</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 600px; margin: 3rem auto; color: #1a1a1a; }
        .seats { color: #666; margin-bottom: 1.5rem; }
        input, select { padding: 0.5rem; margin-right: 0.5rem; }
        button { padding: 0.5rem 1rem; background: #1A2B4A; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
        .error { color: #C0392B; margin-bottom: 1rem; }
        .status { color: #00A886; margin-bottom: 1rem; }
        ul { padding-left: 1.2rem; }
    </style>
</head>
<body>
    <h1>Invite a teammate</h1>
    <p class="seats">
        {{ $subscription?->seats()->count() ?? 0 }} / {{ $subscription?->seat_limit ?? 0 }} seats used
    </p>

    @if (session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif
    @error('email')
        <div class="error">{{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('invitations.store') }}">
        @csrf
        <input type="email" name="email" placeholder="teammate@example.com" required>
        <select name="role">
            <option value="member">Member</option>
            <option value="approver">Approver</option>
            <option value="auditor">Auditor</option>
            <option value="org_admin">Org Admin</option>
        </select>
        <button type="submit">Send invite</button>
    </form>

    <h2>Pending invitations</h2>
    <ul>
        @forelse ($invitations as $invitation)
            <li>{{ $invitation->email }} — {{ $invitation->role }} (expires {{ $invitation->expires_at->format('M j, Y') }})</li>
        @empty
            <li>None yet.</li>
        @endforelse
    </ul>
</body>
</html>
