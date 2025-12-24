@extends('layouts.dashboard')

@section('title', 'Payment Details - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Payment Details</h2>
        <div style="display: flex; gap: 10px;">
            @if($canWrite)
                <a href="{{ route('payment-trackings.edit', $paymentTracking->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
            <a href="{{ route('payment-trackings.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Payment Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Customer</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $paymentTracking->customer->customer_name ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Invoice Number</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $paymentTracking->salesInvoice->invoice_number ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Payment Date</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $paymentTracking->payment_date->format('d M Y') }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Payment Amount</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0; font-weight: 600;">₹{{ number_format($paymentTracking->payment_amount, 2) }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Payment Method</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $paymentTracking->payment_method }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Created By</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $paymentTracking->creator->name ?? 'N/A' }}</p>
            </div>
        </div>
        
        @if($paymentTracking->remarks)
            <div style="margin-top: 15px;">
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Remarks</label>
                <p style="color: #333; font-size: 16px; margin: 0; line-height: 1.6;">{{ $paymentTracking->remarks }}</p>
            </div>
        @endif
    </div>

    @if($paymentTracking->salesInvoice)
        @php
            $totalPaid = \App\Models\PaymentTracking::where('sales_invoice_id', $paymentTracking->sales_invoice_id)->sum('payment_amount');
            $balance = $paymentTracking->salesInvoice->grand_total - $totalPaid;
        @endphp
        <div style="background: #e7f3ff; padding: 20px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #667eea;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Invoice Information</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Invoice Total</label>
                    <p style="color: #333; font-size: 16px; margin: 0 0 20px 0; font-weight: 600;">₹{{ number_format($paymentTracking->salesInvoice->grand_total, 2) }}</p>
                </div>
                <div>
                    <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Total Paid</label>
                    <p style="color: #333; font-size: 16px; margin: 0 0 20px 0; font-weight: 600;">₹{{ number_format($totalPaid, 2) }}</p>
                </div>
                <div>
                    <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Balance</label>
                    <p style="color: {{ $balance > 0 ? '#dc3545' : '#28a745' }}; font-size: 16px; margin: 0 0 20px 0; font-weight: 600;">₹{{ number_format($balance, 2) }}</p>
                </div>
                <div>
                    <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Invoice Date</label>
                    <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $paymentTracking->salesInvoice->invoice_date->format('d M Y') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Additional Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Created At</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $paymentTracking->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Last Updated</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $paymentTracking->updated_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

