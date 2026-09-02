<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New Contract</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 640px; margin: 2rem auto; color: #1a1a1a; }
        label { display: block; font-size: 0.85rem; margin: 1rem 0 0.3rem; color: #333; }
        input, select, textarea { width: 100%; padding: 0.55rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-family: inherit; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .party-row { display: grid; grid-template-columns: 2fr 1fr 2fr; gap: 0.5rem; margin-top: 0.5rem; align-items: start; }
        .party-row .or { grid-column: 1 / -1; font-size: 0.75rem; color: #999; margin: 0.15rem 0; }
        button.add-party { margin-top: 0.75rem; background: none; border: 1px dashed #999; padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer; font-size: 0.85rem; }
        button.submit { margin-top: 2rem; width: 100%; padding: 0.7rem; background: #1A2B4A; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .error { color: #C0392B; font-size: 0.8rem; margin-top: 0.25rem; }
        .empty-hint { font-size: 0.8rem; color: #888; margin-top: 0.25rem; }
        .empty-hint a { color: #1A2B4A; }
    </style>
</head>
<body>
    <h1>New Contract</h1>

    <form method="POST" action="{{ route('contracts.store') }}">
        @csrf

        <label>Title</label>
        <input type="text" name="title" value="{{ old('title') }}" required>
        @error('title') <div class="error">{{ $message }}</div> @enderror

        <label>Description</label>
        <textarea name="description" rows="3">{{ old('description') }}</textarea>

        <div class="row">
            <div>
                <label>Type</label>
                @if ($types->isEmpty())
                    <div class="empty-hint">No contract types yet — <a href="{{ route('settings.contract-types.index') }}">add one in Settings</a> first.</div>
                @else
                    <select name="contract_type_id" required>
                        <option value="">Select…</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}" @selected(old('contract_type_id') === $type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                @endif
                @error('contract_type_id') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label>Department / Unit</label>
                <select name="organization_unit_id">
                    <option value="">—</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" @selected(old('organization_unit_id') === $unit->id)>{{ $unit->name }}</option>
                    @endforeach
                </select>
                @if ($units->isEmpty())
                    <div class="empty-hint"><a href="{{ route('settings.units.index') }}">Add departments/units in Settings</a></div>
                @endif
            </div>
        </div>

        <div class="row">
            <div>
                <label>Value</label>
                <input type="number" step="0.01" name="value" value="{{ old('value') }}">
            </div>
            <div>
                <label>Currency</label>
                <select name="currency">
                    @foreach ($currencies as $code => $label)
                        <option value="{{ $code }}" @selected(old('currency', 'KES') === $code)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div>
                <label>Effective date</label>
                <input type="date" name="effective_date" value="{{ old('effective_date') }}">
            </div>
            <div>
                <label>Expiry date</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date') }}">
                @error('expiry_date') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <label style="margin-top:1.5rem;">Parties</label>
        <div id="parties-container">
            <div class="party-row">
                <select name="parties[0][party_id]">
                    <option value="">— New party (type below) —</option>
                    @foreach ($parties as $party)
                        <option value="{{ $party->id }}">{{ $party->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="parties[0][role]" placeholder="Role">
                <input type="text" name="parties[0][name]" placeholder="Party name (if new)">
            </div>
        </div>
        <button type="button" class="add-party" onclick="addPartyRow()">+ Add another party</button>
        <div class="empty-hint">Pick an existing party from your directory, or type a new name to add one just for this contract. <a href="{{ route('settings.parties.index') }}">Manage your party directory</a>.</div>

        <button type="submit" class="submit">Create Contract</button>
    </form>

    <script>
        let partyIndex = 1;
        const partyOptions = document.querySelector('#parties-container select').innerHTML;
        function addPartyRow() {
            const container = document.getElementById('parties-container');
            const row = document.createElement('div');
            row.className = 'party-row';
            row.innerHTML = `
                <select name="parties[${partyIndex}][party_id]">${partyOptions}</select>
                <input type="text" name="parties[${partyIndex}][role]" placeholder="Role">
                <input type="text" name="parties[${partyIndex}][name]" placeholder="Party name (if new)">
            `;
            container.appendChild(row);
            partyIndex++;
        }
    </script>
</body>
</html>
