@extends('layouts.dashboard')

@section('title', 'Payment Tracking - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('payment-trackings.index');
    $canRead = $user->canRead($formName);
    $canWrite = $user->canWrite($formName);
    $canDelete = $user->canDelete($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Payment Tracking</h2>
        @if($canWrite)
            <a href="{{ route('payment-trackings.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> Record Payment
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('payment-trackings.index') }}" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by customer, invoice number, or payment method..."
                style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            <button type="submit" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('payment-trackings.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>

    @if($paymentTrackings->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Customer</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Invoice Number</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Payment Date</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Payment Amount</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Payment Method</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Invoice Total</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Total Paid</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Balance</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paymentTrackings as $index => $payment)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #333;">{{ $paymentTrackings->firstItem() + $index }}</td>
                            <td style="padding: 12px; color: #333;">{{ $payment->customer->customer_name ?? 'N/A' }}</td>
                            <td style="padding: 12px; color: #333;">{{ $payment->salesInvoice->invoice_number ?? 'N/A' }}</td>
                            <td style="padding: 12px; color: #333;">{{ $payment->payment_date->format('d M Y') }}</td>
                            <td style="padding: 12px; text-align: right; color: #333; font-weight: 500;">₹{{ number_format($payment->payment_amount, 2) }}</td>
                            <td style="padding: 12px; text-align: center; color: #333;">{{ $payment->payment_method }}</td>
                            <td style="padding: 12px; text-align: right; color: #333; font-weight: 500;">₹{{ number_format($payment->invoice_total ?? 0, 2) }}</td>
                            <td style="padding: 12px; text-align: right; color: #333; font-weight: 500;">₹{{ number_format($payment->total_paid ?? 0, 2) }}</td>
                            <td style="padding: 12px; text-align: right; color: {{ ($payment->balance ?? 0) > 0 ? '#dc3545' : '#28a745' }}; font-weight: 600;">₹{{ number_format($payment->balance ?? 0, 2) }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    @if($canRead)
                                        <a href="{{ route('payment-trackings.show', $payment->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    @if($canWrite)
                                        <a href="{{ route('payment-trackings.edit', $payment->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    @if($canDelete)
                                        <form action="{{ route('payment-trackings.destroy', $payment->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this payment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @include('partials.pagination', ['paginator' => $paymentTrackings, 'routeUrl' => route('payment-trackings.index')])
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-receipt" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
            <p style="font-size: 18px; margin-bottom: 10px;">No payment records found</p>
            @if($canWrite)
                <a href="{{ route('payment-trackings.create') }}" style="padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;">
                    Record First Payment
                </a>
            @endif
        </div>
    @endif
</div>
@endsection

