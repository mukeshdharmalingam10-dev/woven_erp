@extends('layouts.auth')

@section('title', 'Login - Woven_ERP')

@section('content')
<div class="auth-header">
    <h1>{{ $companyName ?? 'Welcome Back' }}</h1>
    <p>Sign in to your ERP account</p>
</div>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group">
        <label for="email">Email Address</label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            value="{{ old('email') }}" 
            required 
            autofocus
            placeholder="Enter your email"
        >
        @error('email')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <div style="position: relative;">
            <input 
                type="password" 
                id="password" 
                name="password" 
                required
                placeholder="Enter your password"
                style="padding-right: 40px;"
            >
            <button type="button"
                onclick="togglePasswordVisibility('password', this)"
                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: transparent; cursor: pointer; color: #666;">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div style="text-align: right; margin-bottom: 20px;">
        <a href="{{ route('password.request') }}" style="color: #667eea; text-decoration: none; font-size: 14px;">Forgot Password?</a>
    </div>

    <button type="submit" class="btn">Login</button>
</form>

@push('scripts')
<script>
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

