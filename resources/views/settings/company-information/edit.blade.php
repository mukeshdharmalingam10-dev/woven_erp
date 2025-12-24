@extends('layouts.dashboard')

@section('title', 'Edit Company Information - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Company Information</h2>
        <a href="{{ route('company-information.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back
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

    <form action="{{ route('company-information.update', $companyInfo->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="background: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #b3d9ff;">
            <strong style="color: #333;">Branch:</strong> <span style="color: #666;">{{ $companyInfo->branch->name }}</span>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="company_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Company Name <span style="color: red;">*</span></label>
            <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $companyInfo->company_name) }}" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            @error('company_name')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="logo" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Company Logo</label>
            @if($companyInfo->logo_path)
                <div style="margin-bottom: 10px;">
                    <p style="color: #666; font-size: 14px; margin-bottom: 8px;">Current Logo:</p>
                    @php
                        $logoUrl = asset('storage/' . $companyInfo->logo_path);
                    @endphp
                    <img src="{{ $logoUrl }}" alt="Current Logo" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 5px; padding: 5px;" onerror="this.style.display='none';">
                </div>
            @endif
            <input type="file" name="logo" id="logo" accept="image/jpeg,image/png,image/jpg"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                onchange="previewLogo(this)">
            <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">PNG/JPEG format, max 1MB. Leave empty to keep current logo.</small>
            <div id="logoPreview" style="margin-top: 10px; display: none;">
                <p style="color: #666; font-size: 14px; margin-bottom: 8px;">New Logo Preview:</p>
                <img id="previewImg" src="" alt="Logo Preview" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 5px; padding: 5px;">
            </div>
            @error('logo')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 16px; margin-bottom: 15px;">Address</h3>
            
            <div style="margin-bottom: 15px;">
                <label for="address_line_1" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 1 <span style="color: red;">*</span></label>
                <input type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1', $companyInfo->address_line_1) }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                @error('address_line_1')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 15px;">
                <label for="address_line_2" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 2 (Optional)</label>
                <input type="text" name="address_line_2" id="address_line_2" value="{{ old('address_line_2', $companyInfo->address_line_2) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                @error('address_line_2')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
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

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                <div>
                    <label for="city" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">City <span style="color: red;">*</span></label>
                    <input type="text" name="city" id="city" value="{{ old('city', $companyInfo->city) }}" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    @error('city')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="state" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">State <span style="color: red;">*</span></label>
                    <select name="state" id="state" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                        <option value="">Select state</option>
                        @foreach($states as $state)
                            <option value="{{ $state }}" {{ old('state', $companyInfo->state) === $state ? 'selected' : '' }}>{{ $state }}</option>
                        @endforeach
                    </select>
                    @error('state')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="pincode" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Pincode <span style="color: red;">*</span></label>
                    <input type="text" name="pincode" id="pincode" value="{{ old('pincode', $companyInfo->pincode) }}" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        pattern="[0-9]{6}" maxlength="6" placeholder="123456">
                    @error('pincode')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="gstin" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">GSTIN Number <span style="color: red;">*</span></label>
            <input type="text" name="gstin" id="gstin" value="{{ old('gstin', $companyInfo->gstin) }}" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-transform: uppercase;"
                placeholder="27AABCU9603R1ZX" maxlength="15">
            <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">15 characters: 2 digits + 5 letters + 4 digits + 1 letter + 1 alphanumeric + Z + 1 alphanumeric</small>
            @error('gstin')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="email" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Email ID</label>
                <input type="email" name="email" id="email" value="{{ old('email', $companyInfo->email) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                @error('email')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="phone" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Phone Number</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $companyInfo->phone) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="+91 1234567890">
                @error('phone')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="{{ route('company-information.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Update Company Information
            </button>
        </div>
    </form>
</div>

<script>
    function previewLogo(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('logoPreview').style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection

