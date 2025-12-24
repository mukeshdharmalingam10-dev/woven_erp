@extends('layouts.dashboard')

@section('title', 'View Daily Expense - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333; font-size: 22px; margin: 0;">Daily Expense Entry Details</h2>
        <div style="display: flex; gap: 10px;">
            @php
                $user = auth()->user();
                $formName = $user->getFormNameFromRoute('petty-cash.index');
                $canWrite = $user->canWrite($formName);
            @endphp
            @if($canWrite)
                <a href="{{ route('petty-cash.edit', $pettyCash->id) }}" style="padding: 8px 16px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-size: 14px;">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
            <a href="{{ route('petty-cash.index') }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #666; font-size: 13px;">Expense ID</label>
            <div style="color: #333; font-size: 16px; font-weight: 500;">{{ $pettyCash->expense_id }}</div>
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #666; font-size: 13px;">Date</label>
            <div style="color: #333; font-size: 16px;">{{ optional($pettyCash->date)->format('d-m-Y') }}</div>
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #666; font-size: 13px;">Expense Category</label>
            <div style="color: #333; font-size: 16px;">{{ $pettyCash->expense_category }}</div>
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #666; font-size: 13px;">Amount</label>
            <div style="color: #333; font-size: 16px; font-weight: 500;">{{ number_format($pettyCash->amount, 2) }}</div>
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #666; font-size: 13px;">Payment Method</label>
            <div style="color: #333; font-size: 16px;">{{ $pettyCash->payment_method }}</div>
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #666; font-size: 13px;">Paid To</label>
            <div style="color: #333; font-size: 16px;">{{ $pettyCash->paid_to }}</div>
        </div>
    </div>

    @if($pettyCash->description)
    <div style="margin-bottom: 20px;">
        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Description</label>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; color: #333;">{{ $pettyCash->description }}</div>
    </div>
    @endif

    @if($pettyCash->remarks)
    <div style="margin-bottom: 20px;">
        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Remarks</label>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; color: #333;">{{ $pettyCash->remarks }}</div>
    </div>
    @endif

    @if($pettyCash->receipt_path)
    <div style="margin-bottom: 20px;">
        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Receipt</label>
        <div>
            <a href="{{ asset('storage/' . $pettyCash->receipt_path) }}" target="_blank" style="padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-file"></i> View Receipt
            </a>
        </div>
    </div>
    @endif
</div>
@endsection

