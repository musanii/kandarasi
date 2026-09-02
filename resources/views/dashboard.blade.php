<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Dashboard — {{ $organization->name ?? 'Kandarasi' }}</title>
    <style>
        :root {
            --primary-color: {{ $branding->primary_color ?? '#1A2B4A' }};
            --secondary-color: {{ $branding->secondary_color ?? '#C0392B' }};
            --accent-color: {{ $branding->accent_color ?? '#00A886' }};
        }
        body { font-family: system-ui, sans-serif; margin: 0; background: #F4F5F7; color: #1a1a1a; }
        header {
            background: var(--primary-color); color: #fff; padding: 1rem 2rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        header .org { font-weight: 600; }
        main { padding: 2rem; max-width: 1100px; margin: 0 auto; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card {
            background: #fff; border-radius: 10px; padding: 1.25rem;
            border-left: 4px solid var(--accent-color);
        }
        .stat-card .n { font-size: 1.8rem; font-weight: 700; color: var(--primary-color); }
        .stat-card .label { font-size: 0.85rem; color: #666; margin-top: 0.25rem; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; }
        th, td { text-align: left; padding: 0.75rem 1rem; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        th { background: #FAFAFA; color: #555; font-weight: 600; }
        .empty { padding: 2rem; text-align: center; color: #888; background: #fff; border-radius: 10px; }
        form.logout { margin: 0; }
        form.logout button {
            background: transparent; border: 1px solid rgba(255,255,255,0.4); color: #fff;
            padding: 0.4rem 0.9rem; border-radius: 6px; cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <span class="org">{{ $organization->name ?? 'Kandarasi' }}</span>
        <nav style="display:flex; align-items:center; gap:1.25rem;">
            <a href="{{ route('contracts.index') }}" style="color:#fff; text-decoration:none; font-size:0.9rem;">Contracts</a>
            <a href="{{ route('invitations.index') }}" style="color:#fff; text-decoration:none; font-size:0.9rem;">Team</a>
            <a href="{{ route('settings.index') }}" style="color:#fff; text-decoration:none; font-size:0.9rem;">Settings</a>
            <form class="logout" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Sign out</button>
            </form>
        </nav>
    </header>

    <main>
        <div class="stats">
            <div class="stat-card"><div class="n">{{ $stats['pending_expiries'] }}</div><div class="label">Pending Expiries</div></div>
            <div class="stat-card"><div class="n">{{ $stats['contract_drafts'] }}</div><div class="label">Contract Drafts</div></div>
            <div class="stat-card"><div class="n">{{ $stats['active_contracts'] }}</div><div class="label">Active Contracts</div></div>
            <div class="stat-card"><div class="n">{{ $stats['pending_approvals'] }}</div><div class="label">Pending Approvals</div></div>
        </div>

        @if ($recentContracts->isEmpty())
            <div class="empty">No contracts yet. Once you add one, it'll show up here.</div>
        @else
            <table>
                <thead>
                    <tr><th>Title</th><th>Type</th><th>Status</th><th>Expiry</th></tr>
                </thead>
                <tbody>
                    @foreach ($recentContracts as $contract)
                        <tr>
                            <td>{{ $contract->title }}</td>
                            <td>{{ $contract->type }}</td>
                            <td>{{ $contract->status }}</td>
                            <td>{{ $contract->expiry_date?->format('M j, Y') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </main>
</body>
</html>
