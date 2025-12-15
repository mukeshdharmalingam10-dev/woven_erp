@extends('layouts.dashboard')

@section('title', 'Edit Organization - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    <h2 style="color: #333; margin-bottom: 25px;">Edit Organization</h2>

    <form action="{{ route('organizations.update', $organization->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 20px;">
            <label for="name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Organization Name <span style="color: red;">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $organization->name) }}" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                placeholder="Enter organization name">
            @error('name')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="description" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Description</label>
            <textarea name="description" id="description" rows="4" 
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                placeholder="Enter organization description">{{ old('description', $organization->description) }}</textarea>
            @error('description')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="contact_info" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Contact Information</label>
            <input type="text" name="contact_info" id="contact_info" value="{{ old('contact_info', $organization->address) }}"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                placeholder="Enter contact information (address, phone, etc.)">
            @error('contact_info')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="organization_admin_email" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Organization Admin Email</label>
            <input type="email" name="organization_admin_email" id="organization_admin_email" 
                value="{{ old('organization_admin_email', $organization->admin ? $organization->admin->email : '') }}"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                placeholder="Enter email for Organization Admin">
            <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                @if($organization->admin)
                    Current Admin: {{ $organization->admin->name }} ({{ $organization->admin->email }})
                @else
                    No Organization Admin assigned. Enter an email to create a new Organization Admin user.
                @endif
            </small>
            @error('organization_admin_email')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="{{ route('organizations.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
