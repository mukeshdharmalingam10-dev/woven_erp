@extends('layouts.dashboard')

@section('title', 'Change Password - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Change Password</h2>
        <a href="{{ route('dashboard') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
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

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #ffeaa7;">
        <strong><i class="fas fa-info-circle"></i> Important:</strong> After changing your password, you will be logged out and must log in again with your new password.
    </div>

    <form action="{{ route('account.change-password') }}" method="POST" id="changePasswordForm">
        @csrf
        <div style="margin-bottom: 20px;">
            <label for="current_password" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Current Password <span style="color: red;">*</span></label>
            <div style="position: relative;">
                <input type="password" name="current_password" id="current_password" required
                    style="width: 100%; padding: 12px 40px 12px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    class="@error('current_password') border-red-500 @enderror"
                    autocomplete="current-password">
                <button type="button"
                    onclick="togglePasswordVisibility('current_password', this)"
                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: transparent; cursor: pointer; color: #666;">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('current_password')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="new_password" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">New Password <span style="color: red;">*</span></label>
                <div style="position: relative;">
                    <input type="password" name="new_password" id="new_password" required
                        style="width: 100%; padding: 12px 40px 12px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        class="@error('new_password') border-red-500 @enderror"
                        autocomplete="new-password">
                    <button type="button"
                        onclick="togglePasswordVisibility('new_password', this)"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: transparent; cursor: pointer; color: #666;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Minimum 8 characters, 1 uppercase, 1 lowercase, 1 number</small>
                @error('new_password')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="new_password_confirmation" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Confirm New Password <span style="color: red;">*</span></label>
                <div style="position: relative;">
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                        style="width: 100%; padding: 12px 40px 12px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        class="@error('new_password_confirmation') border-red-500 @enderror"
                        autocomplete="new-password">
                    <button type="button"
                        onclick="togglePasswordVisibility('new_password_confirmation', this)"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: transparent; cursor: pointer; color: #666;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('new_password_confirmation')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="button" onclick="clearForm()" style="padding: 12px 24px; background: #6c757d; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                <i class="fas fa-eraser"></i> Clear
            </button>
            <a href="{{ route('dashboard') }}" style="padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                <i class="fas fa-save"></i> Change Password
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function clearForm() {
        document.getElementById('changePasswordForm').reset();
        // Clear any validation messages
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(msg => msg.remove());
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
</script>
@endpush
@endsection

