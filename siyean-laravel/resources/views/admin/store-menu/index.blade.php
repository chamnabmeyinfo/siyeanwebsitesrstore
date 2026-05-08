@extends('layouts.admin')

@section('title', 'Store Menu')

@section('content')
    <div class="page-header">
        <h1>Store Menu</h1>
    </div>

    <div class="card">
        <h3 style="margin-top:0">Add a menu link</h3>
        <form method="POST" action="{{ route('admin.store-menu.store') }}">
            @csrf
            <div class="grid-3">
                <div class="field">
                    <label>Label</label>
                    <input type="text" name="label" required>
                </div>
                <div class="field">
                    <label>Href (URL or path)</label>
                    <input type="text" name="href" required placeholder="/store or https://...">
                </div>
                <div class="field">
                    <label style="margin-top:1.5rem;">
                        <input type="checkbox" name="is_active" value="1" checked> Active
                    </label>
                </div>
            </div>
            <button type="submit" class="btn">Add link</button>
        </form>
    </div>

    <div class="card">
        <h3 style="margin-top:0">Menu items</h3>
        <p class="text-muted" style="font-size:0.85rem;">Use the up/down arrows to reorder, then click Save menu.</p>
        <form method="POST" action="{{ route('admin.store-menu.save') }}" id="menu-form">
            @csrf
            <input type="hidden" name="menu_order" id="menu-order" value="{{ $items->pluck('id')->implode(',') }}">

            <table id="menu-table">
                <thead>
                    <tr>
                        <th style="width:80px">Order</th>
                        <th>Label</th>
                        <th>Href</th>
                        <th>Active</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr data-id="{{ $item->id }}">
                            <td>
                                <button type="button" onclick="moveRow(this, -1)" class="btn btn-sm btn-secondary">↑</button>
                                <button type="button" onclick="moveRow(this, 1)" class="btn btn-sm btn-secondary">↓</button>
                            </td>
                            <td><input type="text" name="items[{{ $item->id }}][label]" value="{{ $item->label }}" required></td>
                            <td><input type="text" name="items[{{ $item->id }}][href]" value="{{ $item->href }}" required></td>
                            <td><input type="checkbox" name="items[{{ $item->id }}][is_active]" value="1" {{ $item->is_active ? 'checked' : '' }}></td>
                            <td>
                                <form method="POST" action="{{ route('admin.store-menu.destroy', $item) }}" style="display:inline;" onsubmit="return confirm('Delete this menu item?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted" style="padding:1rem; text-align:center;">No menu items yet.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top:1rem;">
                <button type="submit" class="btn">Save menu</button>
            </div>
        </form>
    </div>

    <script>
        function moveRow(btn, dir) {
            const row = btn.closest('tr');
            const sibling = dir === -1 ? row.previousElementSibling : row.nextElementSibling;
            if (!sibling) return;
            row.parentNode.insertBefore(dir === -1 ? row : sibling, dir === -1 ? sibling : row);
            updateOrder();
        }
        function updateOrder() {
            const ids = [...document.querySelectorAll('#menu-table tbody tr[data-id]')].map(r => r.dataset.id);
            document.getElementById('menu-order').value = ids.join(',');
        }
    </script>
@endsection
