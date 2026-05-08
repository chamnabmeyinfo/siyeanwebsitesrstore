@extends('layouts.admin')

@section('title', 'Sales')

@section('content')
    <div class="page-header">
        <h1>Sales</h1>
        <a href="{{ route('admin.sales.create') }}" class="btn">+ New sale</a>
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
    </div>

    <div class="card" style="padding:0; overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>When</th>
                    <th>SKU</th>
                    <th>Customer</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Discount</th>
                    <th>Tax</th>
                    <th>Payment</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $s)
                    <tr>
                        <td>{{ $s->sold_at?->format('Y-m-d H:i') }}</td>
                        <td>{{ $s->product?->sku }} <span class="text-muted">{{ $s->product?->model }}</span></td>
                        <td>{{ $s->customer?->name }}</td>
                        <td>{{ $s->quantity }}</td>
                        <td>${{ number_format((float) $s->unit_price, 2) }}</td>
                        <td>${{ number_format((float) $s->discount, 2) }}</td>
                        <td>{{ number_format((float) $s->tax_rate, 2) }}%</td>
                        <td>{{ $s->payment_method }}</td>
                        <td class="text-right"><strong>${{ number_format($s->total, 2) }}</strong></td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-muted" style="padding:1.5rem; text-align:center;">No sales yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1rem;">{{ $sales->links() }}</div>
@endsection
