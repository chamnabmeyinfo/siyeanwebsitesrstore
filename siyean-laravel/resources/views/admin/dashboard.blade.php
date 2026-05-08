@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <h1>Dashboard</h1>
    </div>

    <div class="grid-stats">
        <div class="stat">
            <div class="label">Total Revenue</div>
            <div class="value">${{ number_format($totalRevenue, 2) }}</div>
        </div>
        <div class="stat">
            <div class="label">Units Sold</div>
            <div class="value">{{ number_format($totalSold) }}</div>
        </div>
        <div class="stat">
            <div class="label">Sales Recorded</div>
            <div class="value">{{ number_format($salesCount) }}</div>
        </div>
        <div class="stat">
            <div class="label">Stock On Hand</div>
            <div class="value">{{ number_format($stockOnHand) }}</div>
        </div>
        <div class="stat">
            <div class="label">SKUs</div>
            <div class="value">{{ number_format($skuCount) }}</div>
        </div>
        <div class="stat">
            <div class="label">Pending Bookings</div>
            <div class="value">{{ number_format($pendingBookings) }}</div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <h3 style="margin-top:0">Low Stock</h3>
            @if ($lowStock->isEmpty())
                <p class="text-muted">No products yet.</p>
            @else
                <table>
                    <thead><tr><th>SKU</th><th>Model</th><th class="text-right">On Hand</th></tr></thead>
                    <tbody>
                        @foreach ($lowStock as $p)
                            <tr>
                                <td>{{ $p->sku }}</td>
                                <td>{{ $p->model }} · {{ $p->color }} · {{ $p->storage_capacity }}GB</td>
                                <td class="text-right">{{ $p->quantity_on_hand }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="card">
            <h3 style="margin-top:0">Recent Sales</h3>
            @if ($recentSales->isEmpty())
                <p class="text-muted">No sales yet.</p>
            @else
                <table>
                    <thead><tr><th>When</th><th>Item</th><th>Qty</th><th class="text-right">Total</th></tr></thead>
                    <tbody>
                        @foreach ($recentSales as $s)
                            <tr>
                                <td>{{ $s->sold_at?->format('M j, H:i') }}</td>
                                <td>{{ $s->product?->sku }} · {{ $s->customer?->name }}</td>
                                <td>{{ $s->quantity }}</td>
                                <td class="text-right">${{ number_format($s->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
