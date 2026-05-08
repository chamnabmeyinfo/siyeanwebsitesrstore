@extends('layouts.admin')

@section('title', $product->exists ? 'Edit ' . $product->sku : 'New product')

@section('content')
    <div class="page-header">
        <h1>{{ $product->exists ? 'Edit ' . $product->sku : 'New product' }}</h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">← Back</a>
    </div>

    <form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" class="card">
        @csrf
        @if ($product->exists) @method('PUT') @endif

        <div class="grid-2">
            <div class="field">
                <label>SKU</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required>
            </div>
            <div class="field">
                <label>Slug (auto from SKU if blank)</label>
                <input type="text" name="slug" value="{{ old('slug', $product->slug) }}">
            </div>
        </div>

        <div class="grid-3">
            <div class="field">
                <label>Model</label>
                <input type="text" name="model" value="{{ old('model', $product->model) }}" required>
            </div>
            <div class="field">
                <label>Storage (GB)</label>
                <input type="number" name="storage_capacity" value="{{ old('storage_capacity', $product->storage_capacity) }}" min="0" required>
            </div>
            <div class="field">
                <label>Color</label>
                <input type="text" name="color" value="{{ old('color', $product->color) }}" required>
            </div>
        </div>

        <div class="grid-3">
            <div class="field">
                <label>Cost price</label>
                <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" required>
            </div>
            <div class="field">
                <label>List price</label>
                <input type="number" step="0.01" name="list_price" value="{{ old('list_price', $product->list_price) }}" required>
            </div>
            <div class="field">
                <label>Online price (optional)</label>
                <input type="number" step="0.01" name="online_price" value="{{ old('online_price', $product->online_price) }}">
            </div>
        </div>

        <div class="grid-2">
            <div class="field">
                <label>Quantity on hand</label>
                <input type="number" name="quantity_on_hand" value="{{ old('quantity_on_hand', $product->quantity_on_hand ?? 0) }}" min="0" required>
            </div>
            <div class="field">
                <label>Hero image URL</label>
                <input type="url" name="hero_image" value="{{ old('hero_image', $product->hero_image) }}">
            </div>
        </div>

        <div class="field">
            <label>Gallery image URLs (one per line)</label>
            <textarea name="gallery_images" rows="3">{{ old('gallery_images', is_array($product->gallery_images) ? implode("\n", $product->gallery_images) : '') }}</textarea>
        </div>

        <div class="field">
            <label>Web description</label>
            <textarea name="web_description" rows="4">{{ old('web_description', $product->web_description) }}</textarea>
        </div>

        <div class="field">
            <label>
                <input type="checkbox" name="visible_online" value="1" {{ old('visible_online', $product->visible_online ?? true) ? 'checked' : '' }}>
                Visible online
            </label>
        </div>

        <button type="submit" class="btn">{{ $product->exists ? 'Save changes' : 'Create product' }}</button>
    </form>
@endsection
