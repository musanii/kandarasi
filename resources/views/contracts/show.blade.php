<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $contract->title }}</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 720px; margin: 2rem auto; color: #1a1a1a; }
        .back { color: #666; text-decoration: none; font-size: 0.85rem; }
        h1 { margin: 0.5rem 0 0.25rem; }
        .status { display: inline-block; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem; background: #EEF1F6; margin-bottom: 1.5rem; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem 2rem; margin-bottom: 2rem; font-size: 0.9rem; }
        .meta .label { color: #888; font-size: 0.75rem; text-transform: uppercase; }
        section { margin-top: 2rem; }
        section h2 { font-size: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.4rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 0.5rem; }
        td, th { text-align: left; padding: 0.5rem 0; font-size: 0.9rem; border-bottom: 1px solid #f2f2f2; }
        .empty { color: #999; font-size: 0.9rem; margin-top: 0.5rem; }
        .step { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f2f2f2; font-size: 0.9rem; }
    </style>
</head>
<body>
    <a class="back" href="{{ route('contracts.index') }}">&larr; All contracts</a>
    <h1>{{ $contract->title }}</h1>
    <span class="status">{{ $contract->status }}</span>

    <div class="meta">
        <div><div class="label">Type</div>{{ $contract->contractType->name ?? '—' }}</div>
        <div><div class="label">Department</div>{{ $contract->organizationUnit->name ?? '—' }}</div>
        <div><div class="label">Value</div>{{ $contract->value ? number_format($contract->value, 2) . ' ' . $contract->currency : '—' }}</div>
        <div><div class="label">Expiry</div>{{ $contract->expiry_date?->format('M j, Y') ?? '—' }}</div>
    </div>

    @if ($contract->description)
        <section><p>{{ $contract->description }}</p></section>
    @endif

    <section>
        <h2>Parties</h2>
        @if ($contract->parties->isEmpty())
            <div class="empty">No parties added.</div>
        @else
            <table>
                @foreach ($contract->parties as $party)
                    <tr><td>{{ $party->name }}</td><td>{{ $party->role }}</td><td>{{ $party->contact_email }}</td></tr>
                @endforeach
            </table>
        @endif
    </section>

    <section>
        <h2>Approval workflow</h2>
        @php($process = $contract->workflowProcess->first())
        @if (! $process)
            <div class="empty">No workflow started for this contract yet.</div>
        @else
            @foreach ($process->steps as $step)
                <div class="step">
                    <span>{{ $step->step_order }}. {{ $step->name }}</span>
                    <span>{{ $step->status }}</span>
                </div>
            @endforeach
        @endif
    </section>

    <section>
        <h2>Case log</h2>
        @if ($contract->caseLogs->isEmpty())
            <div class="empty">No issues logged.</div>
        @else
            @foreach ($contract->caseLogs as $log)
                <div class="step"><span>{{ $log->issue }}</span><span>{{ $log->status }}</span></div>
            @endforeach
        @endif
    </section>
</body>
</html>
