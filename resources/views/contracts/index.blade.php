<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Contracts</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 900px; margin: 2rem auto; color: #1a1a1a; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .btn { background: #1A2B4A; color: #fff; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 0.65rem 0.75rem; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        th { color: #666; font-weight: 600; }
        tr:hover { background: #FAFAFA; }
        a.row-link { color: #1A2B4A; text-decoration: none; font-weight: 500; }
        .status { display: inline-block; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.75rem; background: #EEF1F6; }
        .empty { padding: 3rem; text-align: center; color: #888; }
        .pagination { margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Contracts</h1>
        <a class="btn" href="{{ route('contracts.create') }}">+ New Contract</a>
    </div>

    @if ($contracts->isEmpty())
        <div class="empty">No contracts yet. Create your first one to get started.</div>
    @else
        <table>
            <thead>
                <tr><th>Title</th><th>Type</th><th>Unit</th><th>Status</th><th>Expiry</th></tr>
            </thead>
            <tbody>
                @foreach ($contracts as $contract)
                    <tr>
                        <td><a class="row-link" href="{{ route('contracts.show', $contract) }}">{{ $contract->title }}</a></td>
                        <td>{{ $contract->contractType->name ?? '—' }}</td>
                        <td>{{ $contract->organizationUnit->name ?? '—' }}</td>
                        <td><span class="status">{{ $contract->status }}</span></td>
                        <td>{{ $contract->expiry_date?->format('M j, Y') ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination">{{ $contracts->links() }}</div>
    @endif
</body>
</html>
