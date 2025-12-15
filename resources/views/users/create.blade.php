@extends('layouts.dashboard')

@section('title', 'Create User - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    <h2 style="color: #333; margin-bottom: 25px; font-size: 24px;">Create New User</h2>

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

    <form action="{{ route('users.store') }}" method="POST" id="userForm">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Full Name <span style="color: red;">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    class="@error('name') border-red-500 @enderror">
                @error('name')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Email <span style="color: red;">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    class="@error('email') border-red-500 @enderror">
                @error('email')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="mobile" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Mobile Number</label>
            <input
                type="tel"
                name="mobile"
                id="mobile"
                value="{{ old('mobile') }}"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                class="@error('mobile') border-red-500 @enderror"
                placeholder="e.g., 9876543210"
                maxlength="10"
                pattern="[0-9]{10}"
                title="Enter a valid 10-digit mobile number"
                oninput="sanitizeMobileInput(this)">
            @error('mobile')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="password" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Password <span style="color: red;">*</span></label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" value="{{ old('password') }}" required
                        style="width: 100%; padding: 12px 40px 12px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        class="@error('password') border-red-500 @enderror"
                        autocomplete="new-password">
                    <button type="button"
                        onclick="togglePasswordVisibility('password', this)"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: transparent; cursor: pointer; color: #666;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Minimum 8 characters, 1 uppercase, 1 lowercase, 1 number</small>
                @error('password')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Confirm Password <span style="color: red;">*</span></label>
                <div style="position: relative;">
                    <input type="password" name="password_confirmation" id="password_confirmation" value="{{ old('password_confirmation') }}" required
                        style="width: 100%; padding: 12px 40px 12px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        class="@error('password_confirmation') border-red-500 @enderror"
                        autocomplete="new-password">
                    <button type="button"
                        onclick="togglePasswordVisibility('password_confirmation', this)"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: transparent; cursor: pointer; color: #666;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password_confirmation')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="roles" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Roles <span style="color: red;">*</span></label>
            <select name="roles[]" id="roles" multiple required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; min-height: 100px;"
                onchange="toggleBranchSelection()">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ in_array($role->id, old('roles', [])) ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Hold Ctrl (Windows) or Cmd (Mac) to select multiple roles</small>
            @error('roles')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Branches multi-select - Hidden for all users including superadmin --}}
        <div style="display: none; margin-bottom: 20px;" id="branches-section">
            <label for="branches" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Branches <span style="color: red;">*</span> <span id="branches-required-note" style="color: #666; font-size: 12px;">(Required for non-Super Admin roles)</span></label>
            @if($branches->count() > 0)
                <select name="branches[]" id="branches" multiple
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; min-height: 120px;">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ in_array($branch->id, old('branches', [])) ? 'selected' : '' }}>
                            {{ $branch->name }} ({{ $branch->code }})
                        </option>
                    @endforeach
                </select>
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Hold Ctrl (Windows) or Cmd (Mac) to select multiple branches</small>
            @else
                <div style="background: #fff3cd; color: #856404; padding: 12px; border-radius: 5px; border: 1px solid #ffeaa7;">
                    <strong>No branches available!</strong> Please create at least one branch before creating users.
                    <a href="{{ route('branches.create') }}" style="color: #667eea; text-decoration: underline; margin-left: 10px;">Create Branch</a>
                </div>
            @endif
            @error('branches')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="background: #e7f3ff; color: #004085; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #b3d7ff;">
            <strong><i class="fas fa-info-circle"></i> Note:</strong>
            <p style="margin: 8px 0 0 0; font-size: 14px;">
                Email will not be sent to the user. Please share the login credentials (email and password) with the user externally. 
                The user can change their password after first login.
            </p>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="{{ route('users.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Save
            </button>
        </div>
    </form>
</div>

<script>
function sanitizeMobileInput(input) {
    // Allow only digits and limit to 10 characters
    let digits = input.value.replace(/\D/g, '');
    if (digits.length > 10) {
        digits = digits.slice(0, 10);
    }
    input.value = digits;
}

function toggleBranchSelection() {
    const roleSelect = document.getElementById('roles');
    const branchesSection = document.getElementById('branches-section');
    const branchesSelect = document.getElementById('branches');
    
    // Check if any selected role is Super Admin
    let hasSuperAdmin = false;
    for (let i = 0; i < roleSelect.options.length; i++) {
        if (roleSelect.options[i].selected) {
            const roleName = roleSelect.options[i].text.toLowerCase();
            if (roleName.includes('super admin')) {
                hasSuperAdmin = true;
                break;
            }
        }
    }
    
    if (hasSuperAdmin) {
        branchesSection.style.display = 'none';
        branchesSelect.removeAttribute('required');
        // Clear all selections
        for (let i = 0; i < branchesSelect.options.length; i++) {
            branchesSelect.options[i].selected = false;
        }
    } else {
        branchesSection.style.display = 'block';
        branchesSelect.setAttribute('required', 'required');
    }
}

function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (!input) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleBranchSelection();

    // Front-end validation for mobile number on submit
    const form = document.getElementById('userForm');
    const mobileInput = document.getElementById('mobile');

    if (form && mobileInput) {
        form.addEventListener('submit', function (e) {
            const value = mobileInput.value.trim();
            if (value !== '' && !/^[0-9]{10}$/.test(value)) {
                e.preventDefault();
                alert('Please enter a valid 10-digit mobile number.');
                mobileInput.focus();
            }
        });
    }
});
</script>

@endsection
