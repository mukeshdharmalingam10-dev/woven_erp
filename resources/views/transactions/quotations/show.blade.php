@extends('layouts.dashboard')

@section('title', 'View Quotation - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Quotation Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('quotations.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            @php
                $user = auth()->user();
                $formName = $user->getFormNameFromRoute('quotations.index');
                $canWrite = $user->canWrite($formName);
            @endphp
            @if($canWrite)
                <a href="{{ route('quotations.edit', $quotation->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px;">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Header Information</h3>
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Quotation ID</label>
                <p style="color: #333; font-size: 16px; font-weight: 500;">{{ $quotation->quotation_id }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Customer</label>
                <p style="color: #333; font-size: 16px;">{{ $quotation->customer->customer_name ?? '-' }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Contact Person Name</label>
                <p style="color: #333; font-size: 16px;">{{ $quotation->contact_person_name ?? '-' }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Contact Number</label>
                <p style="color: #333; font-size: 16px;">{{ $quotation->contact_number ?? '-' }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Company Name</label>
                <p style="color: #333; font-size: 16px;">{{ $quotation->company_name ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Address Information</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Address Line 1</label>
                <p style="color: #333; font-size: 16px;">{{ $quotation->address_line_1 ?? '-' }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Address Line 2</label>
                <p style="color: #333; font-size: 16px;">{{ $quotation->address_line_2 ?? '-' }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">City</label>
                <p style="color: #333; font-size: 16px;">{{ $quotation->city ?? '-' }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">State</label>
                <p style="color: #333; font-size: 16px;">{{ $quotation->state ?? '-' }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Postal Code</label>
                <p style="color: #333; font-size: 16px;">{{ $quotation->postal_code ?? '-' }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Country</label>
                <p style="color: #333; font-size: 16px;">{{ $quotation->country ?? 'India' }}</p>
            </div>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Item Details</h3>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 10px; text-align: left;">Product Name</th>
                        <th style="padding: 10px; text-align: left;">Item Description</th>
                        <th style="padding: 10px; text-align: right;">Quantity</th>
                        <th style="padding: 10px; text-align: left;">UOM</th>
                        <th style="padding: 10px; text-align: right;">Price</th>
                        <th style="padding: 10px; text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotation->items as $item)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 10px;">{{ $item->product->product_name ?? '-' }} ({{ $item->product->code ?? '-' }})</td>
                            <td style="padding: 10px;">{{ $item->item_description ?? '-' }}</td>
                            <td style="padding: 10px; text-align: right;">{{ number_format($item->quantity, 2) }}</td>
                            <td style="padding: 10px;">{{ $item->uom ?? '-' }}</td>
                            <td style="padding: 10px; text-align: right;">{{ number_format($item->price, 2) }}</td>
                            <td style="padding: 10px; text-align: right; font-weight: 500;">{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
            <div style="max-width: 300px; width: 100%; background: #f9fafb; padding: 16px 18px; border-radius: 8px; border: 1px solid #e5e7eb;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="font-weight: 700; color: #111827;">Total Amount:</span>
                    <span style="font-weight: 700; color: #111827;">{{ number_format($quotation->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Terms and Conditions</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Validity</label>
                <p style="color: #333; font-size: 16px;">{{ $quotation->validity ?? '-' }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Payment Terms</label>
                <p style="color: #333; font-size: 16px;">{{ $quotation->payment_terms ?? '-' }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Taxes</label>
                <p style="color: #333; font-size: 16px;">{{ $quotation->taxes ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

