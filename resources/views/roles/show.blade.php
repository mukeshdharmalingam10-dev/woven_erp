@extends('layouts.dashboard')

@section('title', 'Role Details - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Role Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('roles.edit', $role->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500;">Edit</a>
            <a href="{{ route('roles.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">Back to List</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Name</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $role->name }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Slug</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $role->slug }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Status</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">
                <span style="background: {{ $role->is_active ? '#48bb78' : '#dc3545' }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                    {{ $role->is_active ? 'Active' : 'Inactive' }}
                </span>
            </p>
        </div>
    </div>

    @if($role->description)
        <div style="margin-bottom: 30px;">
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Description</label>
            <p style="color: #333; font-size: 16px; margin: 0;">{{ $role->description }}</p>
        </div>
    @endif

    <div style="margin-top: 30px;">
        <h3 style="color: #333; margin-bottom: 15px;">Permissions ({{ $role->permissions->count() }})</h3>
        @if($role->permissions->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
                @foreach($role->permissions->groupBy('module') as $module => $permissions)
                    <div style="border: 1px solid #ddd; border-radius: 5px; padding: 15px;">
                        <h4 style="color: #667eea; margin-bottom: 10px; font-size: 16px;">{{ ucfirst($module) }}</h4>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            @foreach($permissions as $permission)
                                <li style="padding: 5px 0; color: #333;">• {{ $permission->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: #999;">No permissions assigned.</p>
        @endif
    </div>
</div>
@endsection

