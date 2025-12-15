@extends('layouts.dashboard')

@section('title', 'Role Permissions - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="color: #333; font-size: 24px; margin: 0 0 5px 0;">Role Permissions</h2>
            <p style="color: #666; margin: 0; font-size: 14px;">
                <i class="fas fa-key" style="color: #667eea;"></i> 
                Manage permissions for each role
            </p>
        </div>
        <a href="{{ route('role-permissions.create') }}" style="padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Add
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($roles->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; background: white;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; border-bottom: 2px solid #dee2e6;">Role Name</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; border-bottom: 2px solid #dee2e6; width: 200px;">Permission Count</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; border-bottom: 2px solid #dee2e6; width: 150px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr style="border-bottom: 1px solid #dee2e6; transition: background-color 0.2s;">
                            <td style="padding: 15px; color: #333;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-user-shield" style="color: #667eea; font-size: 18px;"></i>
                                    <div>
                                        <strong style="font-size: 16px; display: block; margin-bottom: 4px;">{{ $role->name }}</strong>
                                        @if($role->description)
                                            <span style="color: #666; font-size: 13px;">{{ $role->description }}</span>
                                        @else
                                            <span style="color: #999; font-size: 13px; font-style: italic;">No description</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <span style="display: inline-block; background: #667eea; color: white; padding: 6px 16px; border-radius: 20px; font-weight: 500; font-size: 14px;">
                                    {{ $role->permission_count }} {{ $role->permission_count == 1 ? 'Permission' : 'Permissions' }}
                                </span>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <a href="{{ route('role-permissions.edit', $role->id) }}" 
                                   style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; font-size: 14px; transition: background-color 0.2s;"
                                   onmouseover="this.style.background='#5568d3'"
                                   onmouseout="this.style.background='#667eea'">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-radius: 5px; border-left: 4px solid #667eea;">
            <div style="display: flex; align-items: start; gap: 10px;">
                <i class="fas fa-info-circle" style="color: #667eea; font-size: 18px; margin-top: 2px;"></i>
                <div style="flex: 1;">
                    <strong style="color: #333; display: block; margin-bottom: 8px;">Note:</strong>
                    <p style="margin: 0; color: #666; line-height: 1.8; font-size: 14px;">
                        Only roles with assigned permissions are shown in this list. Click <strong>"Add"</strong> to assign permissions to a new role, or click <strong>"Edit"</strong> to modify permissions for an existing role.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-user-shield" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
            <p style="font-size: 18px; margin-bottom: 20px;">No roles with permissions found.</p>
            <a href="{{ route('role-permissions.create') }}" style="padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> Add Permissions to Role
            </a>
        </div>
    @endif
</div>
@endsection
