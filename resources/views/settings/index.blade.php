<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>Settings</title>
<style>
body { font-family: system-ui, sans-serif; max-width: 480px; margin: 3rem auto; color: #1a1a1a; }
a.item { display: block; padding: 1rem; border: 1px solid #eee; border-radius: 8px; margin-bottom: 0.75rem; text-decoration: none; color: #1a1a1a; }
a.item:hover { background: #FAFAFA; }
a.item .t { font-weight: 600; }
a.item .d { font-size: 0.85rem; color: #888; margin-top: 0.2rem; }
</style></head>
<body>
    <h1>Settings</h1>
    <a class="item" href="{{ route('settings.contract-types.index') }}">
        <div class="t">Contract Types</div>
        <div class="d">MOU, Services, SLA, and whatever else your org uses</div>
    </a>
    <a class="item" href="{{ route('settings.units.index') }}">
        <div class="t">Departments / Units</div>
        <div class="d">HR, ICT, Finance, Procurement, or your own structure</div>
    </a>
    <a class="item" href="{{ route('settings.parties.index') }}">
        <div class="t">Party Directory</div>
        <div class="d">Vendors, clients, and counterparties you reuse across contracts</div>
    </a>
</body>
</html>
