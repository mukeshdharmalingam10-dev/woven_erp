@extends('layouts.dashboard')

@section('title', 'Edit Role - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    <h2 style="color: #333; margin-bottom: 25px;">Edit Role</h2>

    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Name <span style="color: red;">*</span></label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                @error('name')<p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Slug <span style="color: red;">*</span></label>
                <input type="text" name="slug" value="{{ old('slug', $role->slug) }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                @error('slug')<p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>@enderror
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Description</label>
            <textarea name="description" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">{{ old('description', $role->description) }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Permissions</label>
            <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px; padding: 15px;">
                @foreach($permissions as $module => $modulePermissions)
                    <div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                            <h4 style="color: #667eea; margin: 0; font-size: 16px; font-weight: 600;">{{ ucfirst(str_replace('-', ' ', $module)) }}</h4>
                            <label style="display: flex; align-items: center; cursor: pointer; font-size: 13px; color: #667eea; font-weight: 500;">
                                <input type="checkbox" class="module-select-all" data-module="{{ $module }}" 
                                    style="margin-right: 5px;">
                                <span>Select All</span>
                            </label>
                        </div>
                        <div class="module-permissions" data-module="{{ $module }}" style="margin-left: 10px;">
                            @foreach($modulePermissions as $permission)
                                <label style="display: flex; align-items: center; margin-bottom: 8px; cursor: pointer;">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                        class="permission-checkbox" data-module="{{ $module }}"
                                        {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}
                                        style="margin-right: 8px;">
                                    <span>{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="{{ route('roles.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">Cancel</a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer;">Update Role</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle "Select All" for each module
    document.querySelectorAll('.module-select-all').forEach(function(selectAllCheckbox) {
        const module = selectAllCheckbox.getAttribute('data-module');
        const modulePermissions = document.querySelectorAll('.permission-checkbox[data-module="' + module + '"]');
        
        // Check if all permissions in this module are selected
        function updateSelectAllState() {
            const allChecked = Array.from(modulePermissions).every(cb => cb.checked);
            const someChecked = Array.from(modulePermissions).some(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        }
        
        // Update select all state when individual checkboxes change
        modulePermissions.forEach(function(checkbox) {
            checkbox.addEventListener('change', updateSelectAllState);
        });
        
        // Handle select all checkbox click
        selectAllCheckbox.addEventListener('change', function() {
            modulePermissions.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
        
        // Initial state
        updateSelectAllState();
    });
});
</script>
@endsection

