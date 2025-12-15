@extends('layouts.dashboard')

@section('title', 'Edit Permission - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    <h2 style="color: #333; margin-bottom: 25px;">Edit Permission</h2>

    <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Name <span style="color: red;">*</span></label>
                <input type="text" name="name" value="{{ old('name', $permission->name) }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                @error('name')<p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Slug <span style="color: red;">*</span></label>
                <input type="text" name="slug" value="{{ old('slug', $permission->slug) }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                @error('slug')<p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>@enderror
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Module</label>
            <input type="text" name="module" value="{{ old('module', $permission->module) }}"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;"
                placeholder="e.g., users, organizations, branches">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Description</label>
            <textarea name="description" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">{{ old('description', $permission->description) }}</textarea>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="{{ route('permissions.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">Cancel</a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer;">Update Permission</button>
        </div>
    </form>
</div>
@endsection

