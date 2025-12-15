@extends('layouts.dashboard')

@section('title', 'Assign Permissions to Role - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0 0 5px 0;">Assign Permissions to Role</h2>
        <p style="color: #666; margin: 0; font-size: 14px;">
            <i class="fas fa-user-shield" style="color: #667eea;"></i> 
            Select a role and assign permissions
        </p>
    </div>

    <form action="{{ route('role-permissions.store') }}" method="POST" style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        @csrf
        <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 250px;">
                <label for="role_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">
                    <i class="fas fa-user-shield" style="color: #667eea; margin-right: 5px;"></i>
                    Select Role <span style="color: #dc3545;">*</span>
                </label>
                <select name="role_id" id="role_id" required
                        style="width: 100%; padding: 10px; border: 1px solid #dee2e6; border-radius: 5px; font-size: 14px; background: white;">
                    <option value="">-- Select a Role --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                            @if($role->description)
                                - {{ $role->description }}
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <span style="color: #dc3545; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>
            <div style="margin-top: 28px;">
                <button type="submit" style="padding: 10px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer; font-size: 14px; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-arrow-right"></i> Continue
                </button>
            </div>
        </div>
    </form>

    <div style="background: #e7f3ff; padding: 15px; border-radius: 5px; border-left: 4px solid #667eea;">
        <div style="display: flex; align-items: start; gap: 10px;">
            <i class="fas fa-info-circle" style="color: #667eea; font-size: 18px; margin-top: 2px;"></i>
            <div style="flex: 1;">
                <strong style="color: #333; display: block; margin-bottom: 8px;">How it works:</strong>
                <ol style="margin: 0; padding-left: 20px; color: #666; line-height: 1.8; font-size: 14px;">
                    <li>Select a role from the dropdown above</li>
                    <li>Click <strong>"Continue"</strong> to proceed to the permission assignment page</li>
                    <li>For each form/resource, assign <strong>Read</strong>, <strong>Write</strong>, or <strong>Delete</strong> permissions</li>
                    <li>Save your changes to update the role's permissions</li>
                </ol>
            </div>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <a href="{{ route('role-permissions.select') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back to Role Permissions List
        </a>
    </div>
</div>
@endsection

