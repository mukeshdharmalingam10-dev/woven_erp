@extends('layouts.dashboard')

@section('title', 'Create Branch - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    <h2 style="color: #333; margin-bottom: 25px;">Create New Branch</h2>

    <form action="{{ route('branches.store') }}" method="POST">
        @csrf

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Branch Name <span style="color: red;">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;"
                placeholder="Enter branch name">
            @error('name')<p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>@enderror
        </div>

        @php
            $states = [
                'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana',
                'Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur',
                'Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu',
                'Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal',
                'Andaman and Nicobar Islands','Chandigarh','Dadra and Nagar Haveli and Daman and Diu',
                'Delhi','Jammu and Kashmir','Ladakh','Lakshadweep','Puducherry'
            ];
        @endphp

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 16px; margin-bottom: 15px;">Branch Address</h3>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 1 <span style="color: red;">*</span></label>
                <input type="text" name="address_line_1" value="{{ old('address_line_1') }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter address line 1">
                @error('address_line_1')<p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>@enderror
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 2 (Optional)</label>
                <input type="text" name="address_line_2" value="{{ old('address_line_2') }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter address line 2">
                @error('address_line_2')<p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>@enderror
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">City <span style="color: red;">*</span></label>
                    <input type="text" name="city" value="{{ old('city') }}" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter city">
                    @error('city')<p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">State <span style="color: red;">*</span></label>
                    <select name="state" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                        <option value="">Select state</option>
                        @foreach($states as $state)
                            <option value="{{ $state }}" {{ old('state') === $state ? 'selected' : '' }}>{{ $state }}</option>
                        @endforeach
                    </select>
                    @error('state')<p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Pincode <span style="color: red;">*</span></label>
                    <input type="text" name="pincode" value="{{ old('pincode') }}" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        pattern="[0-9]{6}" maxlength="6" placeholder="123456">
                    @error('pincode')<p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Branch Contact Information</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;"
                placeholder="Enter contact information (phone, email, etc.)">
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="{{ route('branches.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">Cancel</a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer;">Save</button>
        </div>
    </form>
</div>
@endsection
