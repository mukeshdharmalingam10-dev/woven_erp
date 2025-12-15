@extends('layouts.dashboard')

@section('title', 'Permission Details - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Permission Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('permissions.edit', $permission->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500;">Edit</a>
            <a href="{{ route('permissions.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">Back to List</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Name</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $permission->name }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Slug</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $permission->slug }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Module</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $permission->module ?? 'N/A' }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Status</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">
                <span style="background: {{ $permission->is_active ? '#48bb78' : '#dc3545' }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                    {{ $permission->is_active ? 'Active' : 'Inactive' }}
                </span>
            </p>
        </div>
    </div>

    @if($permission->description)
        <div style="margin-bottom: 30px;">
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Description</label>
            <p style="color: #333; font-size: 16px; margin: 0;">{{ $permission->description }}</p>
        </div>
    @endif

    <div style="margin-top: 30px;">
        <h3 style="color: #333; margin-bottom: 15px;">Roles with this Permission ({{ $permission->roles->count() }})</h3>
        @if($permission->roles->count() > 0)
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                @foreach($permission->roles as $role)
                    <span style="background: #667eea; color: white; padding: 8px 15px; border-radius: 20px; font-size: 14px;">
                        {{ $role->name }}
                    </span>
                @endforeach
            </div>
        @else
            <p style="color: #999;">No roles have this permission.</p>
        @endif
    </div>
</div>
@endsection

