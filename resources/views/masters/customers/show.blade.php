@extends('layouts.dashboard')

@section('title', 'Customer Details - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Customer Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('customers.edit', $customer->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('customers.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Customer/Company Name</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->customer_name }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Contact Name 1</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->contact_name_1 ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Contact Name 2</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->contact_name_2 ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Phone Number</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->phone_number ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Email</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->email ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">GST Number</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->gst_number ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Address Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Address Line 1</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->billing_address_line_1 ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Address Line 2</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->billing_address_line_2 ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">City</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->billing_city ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">State</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->billing_state ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Postal Code</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->billing_postal_code ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Country</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->billing_country ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Bank Details</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Bank Name</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->bank_name ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">IFSC Code</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->ifsc_code ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Account Number</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->account_number ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Branch Name</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $customer->bank_branch_name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Additional Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Created At</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $customer->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Last Updated</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $customer->updated_at->format('d M Y, h:i A') }}</p>
            </div>
            @if($customer->creator)
                <div>
                    <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Created By</label>
                    <p style="color: #333; font-size: 16px; margin: 0;">{{ $customer->creator->name }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

