@extends('layouts.admin')

@section('title', 'Products')

@section('content')
    <div class="page-header">
        <h1>Products</h1>
        <div class="actions-bar">
            <a href="{{ route('admin.products.export') }}" class="btn btn-secondary">Export CSV</a>
            <a href="{{ route('admin.products.import.form') }}" class="btn btn-secondary">Import CSV</a>
            <a href="{{ route('admin.products.create') }}" class="btn">+ New product</a>
        </div>
    </div>

    <div class="card" style="padding:0; overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Model</th>
                    <th>Color</th>
                    <th>Storage</th>
                    <th>Cost</th>
                    <th>List</th>
                    <th>Online</th>
                    <th>On Hand</th>
                    <th>Visible</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $p)
                    <tr>
                        <td>{{ $p->sku }}</td>
                        <td>{{ $p->model }}</td>
                        <td>{{ $p->color }}</td>
                        <td>{{ $p->storage_capacity }}GB</td>
                        <td>${{ number_format((float) $p->cost_price, 2) }}</td>
                        <td>${{ number_format((float) $p->list_price, 2) }}</td>
                        <td>{{ $p->online_price ? '$' . number_format((float) $p->online_price, 2) : '—' }}</td>
                        <td>{{ $p->quantity_on_hand }}</td>
                        <td>{{ $p->visible_online ? 'Yes' : 'No' }}</td>
                        <td class="text-right">
                            <form method="POST" action="{{ route('admin.products.adjust', $p) }}" style="display:inline-block;">
                                @csrf
                                <input type="number" name="delta" value="-1" style="width:60px; padding:0.25rem;">
                                <button type="submit" class="btn btn-sm btn-secondary">Adjust</button>
                            </form>
                            <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-sm btn-secondary">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $p) }}" style="display:inline-block;" onsubmit="return confirm('Delete {{ $p->sku }}?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-muted" style="padding:1.5rem; text-align:center;">No products yet. Add or import to get started.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
