@extends('layouts.dashboard')

@section('title', 'Edit Product - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Product</h2>
        <a href="{{ route('products.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div style="margin-bottom: 20px;">
            <label for="product_category_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Product Category</label>
            <select name="product_category_id" id="product_category_id"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: white;">
                <option value="">Select Product Category</option>
                @if(isset($categories) && $categories->count() > 0)
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('product_category_id', $product->product_category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                @else
                    <option value="" disabled>No Product Categories available</option>
                @endif
            </select>
            @error('product_category_id')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
            @if(isset($categories) && $categories->count() == 0)
                <p style="color: #856404; font-size: 12px; margin-top: 5px; background: #fff3cd; padding: 8px; border-radius: 4px;">
                    <i class="fas fa-info-circle"></i> No Product Categories found. Please create Product Categories first.
                </p>
            @endif
        </div>

        <div style="margin-bottom: 20px;">
            <label for="name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Product Name <span style="color: red;">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            @error('name')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="description" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Description</label>
            <textarea name="description" id="description" rows="4"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="unit_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Unit <span style="color: red;">*</span></label>
            <select name="unit_id" id="unit_id" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                <option value="">Select Unit</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->symbol }})</option>
                @endforeach
            </select>
            @error('unit_id')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="{{ route('products.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Update Product
            </button>
        </div>
    </form>
</div>
@endsection
