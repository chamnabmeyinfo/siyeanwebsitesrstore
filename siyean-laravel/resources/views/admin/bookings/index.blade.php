@extends('layouts.admin')

@section('title', 'Bookings')

@section('content')
    <div class="page-header">
        <h1>Bookings</h1>
    </div>

    <div class="card" style="padding:0; overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Received</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Deposit</th>
                    <th>Preferred</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $b)
                    <tr>
                        <td>{{ $b->created_at?->format('M j, H:i') }}</td>
                        <td>
                            {{ $b->customer_name }}<br>
                            <span class="text-muted">{{ $b->customer_email }} · {{ $b->customer_phone }}</span>
                        </td>
                        <td>{{ $b->product?->sku }} <span class="text-muted">{{ $b->product?->model }}</span></td>
                        <td>{{ $b->quantity }}</td>
                        <td>${{ number_format((float) $b->deposit_amount, 2) }}</td>
                        <td>
                            {{ optional($b->preferred_date)->format('Y-m-d') ?? '—' }}
                            {{ $b->preferred_time ? '· ' . $b->preferred_time : '' }}
                        </td>
                        <td><span class="badge badge-{{ $b->status }}">{{ str_replace('_', ' ', $b->status) }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('admin.bookings.status', $b) }}">
                                @csrf @method('PATCH')
                                <select name="status" onchange="this.form.submit()" style="padding:0.25rem;">
                                    @foreach (\App\Models\Booking::STATUSES as $st)
                                        <option value="{{ $st }}" {{ $b->status === $st ? 'selected' : '' }}>
                                            {{ str_replace('_', ' ', $st) }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                    </tr>
                    @if ($b->notes)
                        <tr><td colspan="8" class="text-muted" style="padding-left:1rem; font-style:italic;">Notes: {{ $b->notes }}</td></tr>
                    @endif
                @empty
                    <tr><td colspan="8" class="text-muted" style="padding:1.5rem; text-align:center;">No bookings yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1rem;">{{ $bookings->links() }}</div>
@endsection
