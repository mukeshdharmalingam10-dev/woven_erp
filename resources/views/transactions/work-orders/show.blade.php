@extends('layouts.dashboard')

@section('title', 'View Work Order - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333; font-size: 22px; margin: 0;">Work Order Details</h2>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('work-orders.edit', $workOrder->id) }}" style="padding: 8px 16px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('work-orders.index') }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div>
            <div style="font-size: 13px; color: #6b7280;">Work Order Number</div>
            <div style="font-weight: 600; color: #111827;">{{ $workOrder->work_order_number }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Work Order Date</div>
            <div style="font-weight: 600; color: #111827;">{{ optional($workOrder->work_order_date)->format('d-m-Y') }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Customer</div>
            <div style="font-weight: 600; color: #111827;">{{ $workOrder->customer->customer_name ?? '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Product</div>
            <div style="font-weight: 600; color: #111827;">{{ $workOrder->product->product_name ?? '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Quantity to be Produced</div>
            <div style="font-weight: 600; color: #111827;">{{ number_format($workOrder->quantity_to_produce, 0) }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Per Kg Weight</div>
            <div style="font-weight: 600; color: #111827;">
                {{ $workOrder->per_kg_weight !== null ? number_format($workOrder->per_kg_weight, 3) : '-' }}
            </div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Status</div>
            <div style="font-weight: 600; color: #111827;">
                @if($workOrder->status === \App\Models\WorkOrder::STATUS_COMPLETED)
                    <span style="padding: 4px 12px; background: #d4edda; color: #155724; border-radius: 12px; font-size: 12px;">Completed</span>
                @else
                    <span style="padding: 4px 12px; background: #fff3cd; color: #856404; border-radius: 12px; font-size: 12px;">Open</span>
                @endif
            </div>
        </div>
        @php
            $totalProducedQuantity = $workOrder->productions()->sum('produced_quantity');
        @endphp
        <div>
            <div style="font-size: 13px; color: #6b7280;">Total Produced Quantity</div>
            <div style="font-weight: 600; color: #111827;">{{ number_format($totalProducedQuantity, 0) }}</div>
        </div>
    </div>
</div>
@endsection


