<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>Contract Types</title>
<style>
body { font-family: system-ui, sans-serif; max-width: 560px; margin: 2rem auto; color: #1a1a1a; }
form.add { display: flex; gap: 0.5rem; margin: 1rem 0 1.5rem; }
input { flex: 1; padding: 0.5rem; border: 1px solid #ccc; border-radius: 6px; }
button { padding: 0.5rem 1rem; background: #1A2B4A; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
table { width: 100%; border-collapse: collapse; }
td, th { text-align: left; padding: 0.5rem 0.25rem; border-bottom: 1px solid #eee; font-size: 0.9rem; }
form.del button { background: none; border: none; color: #C0392B; cursor: pointer; font-size: 0.85rem; }
.error { color: #C0392B; font-size: 0.85rem; margin-bottom: 0.5rem; }
.status { color: #00A886; font-size: 0.85rem; margin-bottom: 0.5rem; }
</style></head>
<body>
    <h1>Contract Types</h1>
    @if (session('status'))<div class="status">{{ session('status') }}</div>@endif
    @error('name')<div class="error">{{ $message }}</div>@enderror

    <form class="add" method="POST" action="{{ route('settings.contract-types.store') }}">
        @csrf
        <input type="text" name="name" placeholder="e.g. Service Agreement" required>
        <button type="submit">Add</button>
    </form>

    <table>
        @foreach ($types as $type)
            <tr>
                <td>{{ $type->name }}</td>
                <td>{{ $type->contracts_count }} contract(s)</td>
                <td>
                    <form class="del" method="POST" action="{{ route('settings.contract-types.destroy', $type->id) }}" onsubmit="return confirm('Remove this type?')">
                        @csrf @method('DELETE')
                        <button type="submit">Remove</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
</body>
</html>
