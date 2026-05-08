@extends('layouts.admin')

@section('title', 'Import Products')

@section('content')
    <div class="page-header">
        <h1>Import Products</h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card">
        <p class="text-muted">CSV must include the following columns: <code>sku, model, storage_capacity, color, cost_price, list_price, quantity_on_hand</code>. Optional: <code>slug, online_price, hero_image, gallery_images, web_description, visible_online</code>.</p>
        <form method="POST" action="{{ route('admin.products.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="field">
                <label>CSV file</label>
                <input type="file" name="csv" accept=".csv,text/csv" required>
            </div>
            <button type="submit" class="btn">Import</button>
        </form>
    </div>
@endsection
