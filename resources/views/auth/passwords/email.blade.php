@extends('layouts.auth')

@section('title', 'Forgot Password - Woven_ERP')

@section('content')
<div class="auth-header">
    <h1>Reset Password</h1>
    <p>Enter your email address and we'll send you a link to reset your password</p>
</div>

@if(session('status'))
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
        {{ session('status') }}
    </div>
@endif

@if(isset($db_error))
    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
        {{ $db_error }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
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

    <div style="display: flex; gap: 15px; margin-top: 20px;">
        <a href="{{ route('login') }}" style="flex: 1; padding: 12px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; text-align: center; font-weight: 500;">
            Cancel
        </a>
        <button type="submit" class="btn" style="flex: 1;">
            Send Reset Link
        </button>
    </div>
</form>
@endsection

