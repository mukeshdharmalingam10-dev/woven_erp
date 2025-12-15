@extends('layouts.dashboard')

@section('title', 'Manage Permissions - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0 0 5px 0;">Assign Permissions to Role</h2>
        <p style="color: #666; margin: 0; font-size: 14px;">
            <i class="fas fa-user-shield" style="color: #667eea;"></i> 
            Select a role and assign permissions
        </p>
    </div>

    <form action="{{ route('role-permissions.store') }}" method="POST" id="roleSelectForm" style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
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
                    @foreach($allRoles as $r)
                        <option value="{{ $r->id }}" {{ $r->id == $role->id ? 'selected' : '' }}>
                            {{ $r->name }}
                            @if($r->description)
                                - {{ $r->description }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <script>
        document.getElementById('role_id').addEventListener('change', function() {
            if (this.value) {
                document.getElementById('roleSelectForm').submit();
            }
        });
    </script>

    <div style="background: #e7f3ff; padding: 12px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #667eea;">
        <p style="margin: 0; color: #333; font-size: 14px;">
            <i class="fas fa-info-circle" style="color: #667eea; margin-right: 8px;"></i>
            <strong>Selected Role:</strong> {{ $role->name }}
            @if($role->description)
                - {{ $role->description }}
            @endif
        </p>
    </div>

    <div style="background: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #667eea;">
        <div style="display: flex; align-items: start; gap: 10px;">
            <i class="fas fa-info-circle" style="color: #667eea; font-size: 20px; margin-top: 2px;"></i>
            <div style="flex: 1;">
                <strong style="color: #333; display: block; margin-bottom: 8px; font-size: 16px;">Permission Types Explained:</strong>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-top: 10px;">
                    <div style="background: white; padding: 15px; border-radius: 5px; border: 2px solid #667eea;">
                        <strong style="color: #667eea; display: block; margin-bottom: 8px; font-size: 16px;">
                            <i class="fas fa-eye"></i> Read (View)
                        </strong>
                        <p style="margin: 0; color: #666; font-size: 13px; line-height: 1.6;">
                            <strong>View only.</strong> User can view the list and details but <strong>cannot make any changes</strong> (no editing, adding, or deleting).
                        </p>
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                            <small style="color: #999;">✓ View list<br>✓ View details<br>✗ Cannot edit<br>✗ Cannot add<br>✗ Cannot delete</small>
                        </div>
                    </div>
                    <div style="background: white; padding: 15px; border-radius: 5px; border: 2px solid #28a745;">
                        <strong style="color: #28a745; display: block; margin-bottom: 8px; font-size: 16px;">
                            <i class="fas fa-edit"></i> Write (Edit/Add)
                        </strong>
                        <p style="margin: 0; color: #666; font-size: 13px; line-height: 1.6;">
                            <strong>Edit or Add.</strong> User can edit existing records or add new ones but <strong>cannot delete</strong> existing records.
                        </p>
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                            <small style="color: #999;">✓ View list<br>✓ View details<br>✓ Edit records<br>✓ Add new records<br>✗ Cannot delete</small>
                        </div>
                    </div>
                    <div style="background: white; padding: 15px; border-radius: 5px; border: 2px solid #dc3545;">
                        <strong style="color: #dc3545; display: block; margin-bottom: 8px; font-size: 16px;">
                            <i class="fas fa-trash"></i> Delete (Full Access)
                        </strong>
                        <p style="margin: 0; color: #666; font-size: 13px; line-height: 1.6;">
                            <strong>Full control.</strong> User can Read, Edit, Add, and Delete. This is the most powerful permission, giving complete control.
                        </p>
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                            <small style="color: #999;">✓ View list<br>✓ View details<br>✓ Edit records<br>✓ Add new records<br>✓ Delete records</small>
                        </div>
                    </div>
                </div>
                <div style="margin-top: 15px; padding: 12px; background: #fff3cd; border-radius: 5px; border-left: 4px solid #ffc107;">
                    <p style="margin: 0; color: #856404; font-size: 13px;">
                        <strong>Note:</strong> Each form (like "Units", "Customers", etc.) can have these three permission types assigned independently. 
                        For example, a role can have Read access to "Units" but Write access to "Customers".
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(isset($menus) && $menus->count() > 0)
        <form action="{{ route('role-permissions.update', $role->id) }}" method="POST">
            @csrf
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Page</th>
                            <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; width: 100px;">Read</th>
                            <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; width: 120px;">Add / Edit / Update</th>
                            <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; width: 110px;">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($menus as $menu)
                            {{-- Menu header row (matches left sidebar section) --}}
                            <tr>
                                <td style="padding: 10px 12px; background: #e2e8f0; font-weight: 700; color: #111827; border-top: 2px solid #cbd5e1; border-bottom: 2px solid #cbd5e1;">
                                    {{ $menu->name }}
                                </td>
                                {{-- Menu-wise select/unselect all checkboxes --}}
                                <td style="padding: 8px 12px; text-align: center; background: #e2e8f0; border-top: 2px solid #cbd5e1; border-bottom: 2px solid #cbd5e1;">
                                    <input type="checkbox"
                                           class="menu-select-all"
                                           data-menu-id="{{ $menu->id }}"
                                           data-perm="read"
                                           style="width:18px; height:18px; cursor:pointer;"
                                           title="Select/Unselect all Read for {{ $menu->name }}">
                                </td>
                                <td style="padding: 8px 12px; text-align: center; background: #e2e8f0; border-top: 2px solid #cbd5e1; border-bottom: 2px solid #cbd5e1;">
                                    <input type="checkbox"
                                           class="menu-select-all"
                                           data-menu-id="{{ $menu->id }}"
                                           data-perm="write"
                                           style="width:18px; height:18px; cursor:pointer;"
                                           title="Select/Unselect all Add/Edit/Update for {{ $menu->name }}">
                                </td>
                                <td style="padding: 8px 12px; text-align: center; background: #e2e8f0; border-top: 2px solid #cbd5e1; border-bottom: 2px solid #cbd5e1;">
                                    <input type="checkbox"
                                           class="menu-select-all"
                                           data-menu-id="{{ $menu->id }}"
                                           data-perm="delete"
                                           style="width:18px; height:18px; cursor:pointer;"
                                           title="Select/Unselect all Delete for {{ $menu->name }}">
                                </td>
                            </tr>

                            {{-- Forms directly under this menu (no submenu) --}}
                            @foreach($menu->forms->where('submenu_id', null) as $form)
                                @php
                                    $rp = $roleFormPermissions->get($form->id);
                                    $permType = $rp ? (int)$rp->permission_type : null;
                                    $readChecked   = in_array($permType, [\App\Models\RoleFormPermission::VIEW, \App\Models\RoleFormPermission::ADD_EDIT_UPDATE, \App\Models\RoleFormPermission::FULL_ACCESS], true);
                                    $writeChecked  = in_array($permType, [\App\Models\RoleFormPermission::ADD_EDIT_UPDATE, \App\Models\RoleFormPermission::FULL_ACCESS], true);
                                    $deleteChecked = $permType === \App\Models\RoleFormPermission::FULL_ACCESS;
                                @endphp
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 10px 12px; color: #333;">
                                        {{ $form->name }}
                                    </td>
                                    <td style="padding: 10px 12px; text-align:center;">
                                        <input type="checkbox"
                                               name="form_permissions[{{ $form->id }}][read]"
                                               value="1"
                                               class="perm-read"
                                               data-menu-id="{{ $menu->id }}"
                                               style="width:18px; height:18px; cursor:pointer;"
                                               {{ $readChecked ? 'checked' : '' }}>
                                    </td>
                                    <td style="padding: 10px 12px; text-align:center;">
                                        <input type="checkbox"
                                               name="form_permissions[{{ $form->id }}][write]"
                                               value="1"
                                               class="perm-write"
                                               data-menu-id="{{ $menu->id }}"
                                               style="width:18px; height:18px; cursor:pointer;"
                                               {{ $writeChecked ? 'checked' : '' }}>
                                    </td>
                                    <td style="padding: 10px 12px; text-align:center;">
                                        <input type="checkbox"
                                               name="form_permissions[{{ $form->id }}][delete]"
                                               value="1"
                                               class="perm-delete"
                                               data-menu-id="{{ $menu->id }}"
                                               style="width:18px; height:18px; cursor:pointer;"
                                               {{ $deleteChecked ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            @endforeach

                            {{-- Submenus and their forms --}}
                            @foreach($menu->submenus as $submenu)
                                @if(trim($submenu->name) !== 'Transactions')
                                    <tr>
                                        <td colspan="4" style="padding: 8px 12px; background:#fafafa; font-weight:500; color:#374151; border-top:1px solid #e5e7eb;">
                                            {{ $submenu->name }}
                                        </td>
                                    </tr>
                                @endif
                                @foreach($submenu->forms as $form)
                                    @php
                                        $rp = $roleFormPermissions->get($form->id);
                                        $permType = $rp ? (int)$rp->permission_type : null;
                                        $readChecked   = in_array($permType, [\App\Models\RoleFormPermission::VIEW, \App\Models\RoleFormPermission::ADD_EDIT_UPDATE, \App\Models\RoleFormPermission::FULL_ACCESS], true);
                                        $writeChecked  = in_array($permType, [\App\Models\RoleFormPermission::ADD_EDIT_UPDATE, \App\Models\RoleFormPermission::FULL_ACCESS], true);
                                        $deleteChecked = $permType === \App\Models\RoleFormPermission::FULL_ACCESS;
                                    @endphp
                                    <tr style="border-bottom: 1px solid #dee2e6;">
                                        <td style="padding: 10px 12px; color: #333;">
                                            {{ $form->name }}
                                        </td>
                                        <td style="padding: 10px 12px; text-align:center;">
                                            <input type="checkbox"
                                                   name="form_permissions[{{ $form->id }}][read]"
                                                   value="1"
                                                   class="perm-read"
                                                   data-menu-id="{{ $menu->id }}"
                                                   style="width:18px; height:18px; cursor:pointer;"
                                                   {{ $readChecked ? 'checked' : '' }}>
                                        </td>
                                        <td style="padding: 10px 12px; text-align:center;">
                                            <input type="checkbox"
                                                   name="form_permissions[{{ $form->id }}][write]"
                                                   value="1"
                                                   class="perm-write"
                                                   data-menu-id="{{ $menu->id }}"
                                                   style="width:18px; height:18px; cursor:pointer;"
                                                   {{ $writeChecked ? 'checked' : '' }}>
                                        </td>
                                        <td style="padding: 10px 12px; text-align:center;">
                                            <input type="checkbox"
                                                   name="form_permissions[{{ $form->id }}][delete]"
                                                   value="1"
                                                   class="perm-delete"
                                                   data-menu-id="{{ $menu->id }}"
                                                   style="width:18px; height:18px; cursor:pointer;"
                                                   {{ $deleteChecked ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 25px; display: flex; gap: 15px;">
                <button type="submit" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                    <i class="fas fa-save"></i> Save Permissions
                </button>
                <a href="{{ route('role-permissions.select') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </form>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No forms found. Please run the MenuFormSeeder.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Menu-level select/unselect all
        document.querySelectorAll('.menu-select-all').forEach(function (headerCheckbox) {
            headerCheckbox.addEventListener('change', function () {
                const menuId = this.getAttribute('data-menu-id');
                const perm = this.getAttribute('data-perm'); // read/write/delete
                const checked = this.checked;

                document.querySelectorAll('.perm-' + perm + '[data-menu-id="' + menuId + '"]').forEach(function (cb) {
                    cb.checked = checked;

                    if (checked) {
                        // Ensure only one permission type per row when using menu-level checkbox
                        const row = cb.closest('tr');
                        if (!row) return;
                        ['read', 'write', 'delete'].forEach(function (p) {
                            if (p !== perm) {
                                const other = row.querySelector('.perm-' + p + '[data-menu-id="' + menuId + '"]');
                                if (other) {
                                    other.checked = false;
                                }
                            }
                        });
                    }
                });

                // When a menu-level permission is checked, uncheck the other menu-level permissions
                if (checked) {
                    document.querySelectorAll('.menu-select-all[data-menu-id="' + menuId + '"]').forEach(function (otherHeader) {
                        if (otherHeader !== headerCheckbox) {
                            otherHeader.checked = false;
                        }
                    });
                }
            });
        });

        // Row-level exclusivity: only one of read/write/delete can be selected at a time
        document.querySelectorAll('.perm-read, .perm-write, .perm-delete').forEach(function (input) {
            input.addEventListener('change', function () {
                if (!this.checked) {
                    return; // only react when checkbox is turned on
                }

                let perm = 'read';
                if (this.classList.contains('perm-write')) perm = 'write';
                if (this.classList.contains('perm-delete')) perm = 'delete';

                const row = this.closest('tr');
                if (!row) return;

                ['read', 'write', 'delete'].forEach(function (p) {
                    if (p !== perm) {
                        const other = row.querySelector('.perm-' + p);
                        if (other) {
                            other.checked = false;
                        }
                    }
                });
            });
        });
    });
</script>
@endpush
