@extends('layouts.dashboard')

@section('title', 'Edit Customer - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Customer</h2>
        <a href="{{ route('customers.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
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

    <form action="{{ route('customers.update', $customer->id) }}" method="POST" id="customerForm">
        @csrf
        @method('PUT')

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Information</h3>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #666; font-weight: 500;">Customer ID</label>
                <div style="padding: 12px; background: #e9ecef; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; color: #333; font-weight: 500;">
                    {{ $customer->code }}
                </div>
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Customer ID cannot be changed</small>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="customer_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer/Company Name <span style="color: red;">*</span></label>
                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $customer->customer_name) }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter customer or company name">
                @error('customer_name')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div>
                    <label for="contact_name_1" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Contact Name 1</label>
                    <input type="text" name="contact_name_1" id="contact_name_1" value="{{ old('contact_name_1', $customer->contact_name_1) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter primary contact person name">
                    @error('contact_name_1')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="contact_name_2" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Contact Name 2</label>
                    <input type="text" name="contact_name_2" id="contact_name_2" value="{{ old('contact_name_2', $customer->contact_name_2) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter secondary contact person name">
                    @error('contact_name_2')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label for="phone_number" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $customer->phone_number) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter phone number">
                    @error('phone_number')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter email address">
                    @error('email')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Address Information</h3>
            
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

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label for="billing_address_line_1" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 1</label>
                    <input type="text" name="billing_address_line_1" id="billing_address_line_1" value="{{ old('billing_address_line_1', $customer->billing_address_line_1) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter address line 1">
                    @error('billing_address_line_1')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="billing_address_line_2" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 2</label>
                    <input type="text" name="billing_address_line_2" id="billing_address_line_2" value="{{ old('billing_address_line_2', $customer->billing_address_line_2) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter address line 2">
                    @error('billing_address_line_2')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label for="billing_city" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">City</label>
                    <input type="text" name="billing_city" id="billing_city" value="{{ old('billing_city', $customer->billing_city) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter city">
                    @error('billing_city')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="billing_state" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">State <span style="color: red;">*</span></label>
                    <select name="billing_state" id="billing_state" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                        <option value="">Select state</option>
                        @foreach($states as $state)
                            <option value="{{ $state }}" {{ old('billing_state', $customer->billing_state) === $state ? 'selected' : '' }}>{{ $state }}</option>
                        @endforeach
                    </select>
                    @error('billing_state')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label for="billing_postal_code" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Postal Code</label>
                    <input type="text" name="billing_postal_code" id="billing_postal_code" value="{{ old('billing_postal_code', $customer->billing_postal_code) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter postal code" maxlength="10">
                    @error('billing_postal_code')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="billing_country" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Country</label>
                    <select name="billing_country" id="billing_country"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                        <option value="India" {{ old('billing_country', $customer->billing_country ?? 'India') === 'India' ? 'selected' : '' }}>India</option>
                        <option value="Other" {{ old('billing_country', $customer->billing_country) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('billing_country')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Business Information</h3>
            
            <div style="margin-bottom: 20px;">
                <label for="gst_number" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">GST Number</label>
                <input type="text" name="gst_number" id="gst_number" value="{{ old('gst_number', $customer->gst_number) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter GST number (if applicable)">
                @error('gst_number')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Bank Details</h3>

            <div style="margin-bottom: 15px;">
                <label for="bank_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Bank Name</label>
                <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $customer->bank_name) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter bank name">
                @error('bank_name')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label for="ifsc_code" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">IFSC Code</label>
                    <input type="text" name="ifsc_code" id="ifsc_code" value="{{ old('ifsc_code', $customer->ifsc_code) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter IFSC code">
                    @error('ifsc_code')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="account_number" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Account Number</label>
                    <input type="text" name="account_number" id="account_number" value="{{ old('account_number', $customer->account_number) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="Enter account number">
                    @error('account_number')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="bank_branch_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Branch Name</label>
                <input type="text" name="bank_branch_name" id="bank_branch_name" value="{{ old('bank_branch_name', $customer->bank_branch_name) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter branch name">
                @error('bank_branch_name')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="button" onclick="resetForm()" style="padding: 12px 24px; background: #17a2b8; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Reset
            </button>
            <button type="submit" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Update
            </button>
            <a href="{{ route('customers.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function checkIfAddressesMatch() {
        const billingLine1 = document.getElementById('billing_address_line_1').value || '';
        const billingLine2 = document.getElementById('billing_address_line_2').value || '';
        const billingCity = document.getElementById('billing_city').value || '';
        const billingState = document.getElementById('billing_state').value || '';
        const billingPostal = document.getElementById('billing_postal_code').value || '';
        const billingCountry = document.getElementById('billing_country').value || '';
        
        const shippingLine1 = document.getElementById('shipping_address_line_1').value || '';
        const shippingLine2 = document.getElementById('shipping_address_line_2').value || '';
        const shippingCity = document.getElementById('shipping_city').value || '';
        const shippingState = document.getElementById('shipping_state').value || '';
        const shippingPostal = document.getElementById('shipping_postal_code').value || '';
        const shippingCountry = document.getElementById('shipping_country').value || '';
        
        const checkbox = document.getElementById('same_as_billing');
        
        if (!checkbox) return;
        
        // Check if all address fields match
        if (billingLine1 === shippingLine1 &&
            billingLine2 === shippingLine2 &&
            billingCity === shippingCity &&
            billingState === shippingState &&
            billingPostal === shippingPostal &&
            billingCountry === shippingCountry) {
            checkbox.checked = true;
        } else {
            checkbox.checked = false;
        }
    }

    function resetForm() {
        document.getElementById('customerForm').reset();
        // Restore original values
        document.getElementById('customer_name').value = '{{ addslashes($customer->customer_name) }}';
        document.getElementById('contact_name_1').value = '{{ addslashes($customer->contact_name_1 ?? '') }}';
        document.getElementById('contact_name_2').value = '{{ addslashes($customer->contact_name_2 ?? '') }}';
        document.getElementById('phone_number').value = '{{ addslashes($customer->phone_number ?? '') }}';
        document.getElementById('email').value = '{{ addslashes($customer->email ?? '') }}';
        document.getElementById('gst_number').value = '{{ addslashes($customer->gst_number ?? '') }}';
        document.getElementById('bank_name').value = '{{ addslashes($customer->bank_name ?? '') }}';
        document.getElementById('ifsc_code').value = '{{ addslashes($customer->ifsc_code ?? '') }}';
        document.getElementById('account_number').value = '{{ addslashes($customer->account_number ?? '') }}';
        document.getElementById('bank_branch_name').value = '{{ addslashes($customer->bank_branch_name ?? '') }}';
    }
</script>
@endpush
@endsection

