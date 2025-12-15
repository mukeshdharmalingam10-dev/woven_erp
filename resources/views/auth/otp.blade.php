@extends('layouts.auth')

@section('title', 'OTP Verification - Woven_ERP')

@section('content')
<div class="auth-header">
    <h1>OTP Verification</h1>
    <p>Enter the OTP sent to your email</p>
</div>

<form method="POST" action="{{ route('otp.verify') }}" id="otpForm">
    @csrf

    <div class="form-group">
        <label for="otp">Enter OTP</label>
        <input 
            type="text" 
            id="otp" 
            name="otp" 
            value="{{ $otp ?? '' }}" 
            required 
            autofocus
            maxlength="6"
            pattern="[0-9]{6}"
            placeholder="000000"
            style="text-align: center; font-size: 24px; letter-spacing: 10px;"
        >
        @error('otp')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn">Verify & Continue</button>
</form>

@push('scripts')
<script>
    // Auto-focus and format OTP input
    document.addEventListener('DOMContentLoaded', function() {
        const otpInput = document.getElementById('otp');
        
        // Only allow numbers
        otpInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Auto-submit when 6 digits are entered
        otpInput.addEventListener('input', function(e) {
            if (this.value.length === 6) {
                // Optional: Auto-submit after a short delay
                // setTimeout(() => {
                //     document.getElementById('otpForm').submit();
                // }, 500);
            }
        });
    });
</script>
@endpush
@endsection

