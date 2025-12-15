@extends('layouts.auth')

@section('title', 'Select Branch - Woven_ERP')

@section('content')
<div class="auth-header">
    <h1>Select Branch</h1>
    <p>Please select a branch to continue</p>
</div>

@if(session('success'))
    <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ route('branch.select.post') }}">
    @csrf

    <div class="form-group">
        <label for="branch_id">Select Branch</label>
        <select id="branch_id" name="branch_id" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; background-color: #fff; color: #333;">
            <option value="">-- Select a branch --</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ (old('branch_id', session('active_branch_id')) == $branch->id) ? 'selected' : '' }} style="background-color: #fff; color: #333;">
                    {{ $branch->name }} ({{ $branch->code }})
                </option>
            @endforeach
        </select>
        @error('branch_id')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn">Continue</button>
</form>
@endsection

