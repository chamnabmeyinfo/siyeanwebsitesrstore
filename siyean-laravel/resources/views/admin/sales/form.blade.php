@extends('layouts.admin')

@section('title', 'New sale')

@section('content')
    <div class="page-header">
        <h1>Record sale</h1>
        <a href="{{ route('admin.sales.index') }}" class="btn btn-secondary">← Back</a>
    </div>

    <form method="POST" action="{{ route('admin.sales.store') }}" class="card">
        @csrf

        <div class="field">
            <label>Product</label>
            <select name="product_id" required>
                <option value="">— Select product —</option>
                @foreach ($products as $p)
                    <option value="{{ $p->id }}" data-price="{{ $p->list_price }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->sku }} · {{ $p->model }} · {{ $p->color }} {{ $p->storage_capacity }}GB (qty {{ $p->quantity_on_hand }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid-3">
            <div class="field">
                <label>Quantity</label>
                <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" required>
            </div>
            <div class="field">
                <label>Unit price</label>
                <input type="number" step="0.01" name="unit_price" value="{{ old('unit_price') }}" required>
            </div>
            <div class="field">
                <label>Discount</label>
                <input type="number" step="0.01" name="discount" value="{{ old('discount', 0) }}">
            </div>
        </div>

        <div class="grid-2">
            <div class="field">
                <label>Tax rate (%)</label>
                <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', 0) }}">
            </div>
            <div class="field">
                <label>Payment method</label>
                <select name="payment_method">
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="bank_transfer">Bank transfer</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>

        <h3>Customer</h3>
        <div class="grid-3">
            <div class="field">
                <label>Name</label>
                <input type="text" name="customer_name" value="{{ old('customer_name') }}" required>
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" name="customer_email" value="{{ old('customer_email') }}">
            </div>
            <div class="field">
                <label>Phone</label>
                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}">
            </div>
        </div>

        <div class="field">
            <label>Notes</label>
            <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="btn">Record sale</button>
    </form>

    <script>
        document.querySelector('select[name=product_id]').addEventListener('change', function (e) {
            const opt = e.target.selectedOptions[0];
            const price = opt?.dataset.price;
            if (price) document.querySelector('input[name=unit_price]').value = price;
        });
    </script>
@endsection
