<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>Party Directory</title>
<style>
body { font-family: system-ui, sans-serif; max-width: 640px; margin: 2rem auto; color: #1a1a1a; }
form.add { display: grid; grid-template-columns: 2fr 1fr 2fr 1fr; gap: 0.5rem; margin: 1rem 0 1.5rem; align-items: start; }
input, select { padding: 0.5rem; border: 1px solid #ccc; border-radius: 6px; }
button { padding: 0.5rem 1rem; background: #1A2B4A; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
table { width: 100%; border-collapse: collapse; }
td { text-align: left; padding: 0.5rem 0.25rem; border-bottom: 1px solid #eee; font-size: 0.9rem; }
form.del button { background: none; border: none; color: #C0392B; cursor: pointer; font-size: 0.85rem; }
.error { color: #C0392B; font-size: 0.85rem; margin-bottom: 0.5rem; }
.status { color: #00A886; font-size: 0.85rem; margin-bottom: 0.5rem; }
</style></head>
<body>
    <h1>Party Directory</h1>
    <p style="color:#666; font-size:0.9rem;">Vendors, clients, and counterparties you deal with repeatedly — select these directly when drafting a contract instead of retyping details.</p>
    @if (session('status'))<div class="status">{{ session('status') }}</div>@endif
    @error('name')<div class="error">{{ $message }}</div>@enderror

    <form class="add" method="POST" action="{{ route('settings.parties.store') }}">
        @csrf
        <input type="text" name="name" placeholder="Name" required>
        <select name="type">
            <option value="organization">Organization</option>
            <option value="individual">Individual</option>
            <option value="vendor">Vendor</option>
            <option value="client">Client</option>
        </select>
        <input type="email" name="contact_email" placeholder="Contact email">
        <button type="submit">Add</button>
    </form>

    <table>
        @foreach ($parties as $party)
            <tr>
                <td>{{ $party->name }}</td>
                <td>{{ $party->type }}</td>
                <td>{{ $party->contact_email }}</td>
                <td>
                    <form class="del" method="POST" action="{{ route('settings.parties.destroy', $party->id) }}" onsubmit="return confirm('Remove this party?')">
                        @csrf @method('DELETE')
                        <button type="submit">Remove</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
</body>
</html>
