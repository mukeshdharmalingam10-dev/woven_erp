@extends('layouts.dashboard')

@section('title', 'View Production - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Production Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('productions.edit', $production->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('productions.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Production Information</h3>
        
        <!-- First Row: Work Order Number (full width) -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Work Order Number</label>
            <p style="color: #333; font-size: 16px; margin: 0;">{{ $production->workOrder->work_order_number ?? '-' }}</p>
        </div>

        <!-- Second Row: Product Name and Produced Quantity (side by side) -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Product Name</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ $production->product->product_name ?? '-' }} @if($production->product) ({{ $production->product->code }}) @endif</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Produced Quantity</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ number_format($production->produced_quantity, 0) }}</p>
            </div>
        </div>

        <!-- Third Row: Weight of 1 Bag/Unit and Total Weight Produced (side by side) -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Weight of 1 Bag/Unit</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ number_format($production->weight_per_unit, 2) }}</p>
            </div>
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Total Weight Produced</label>
                <p style="color: #333; font-size: 16px; margin: 0;">{{ number_format($production->total_weight, 0) }}</p>
            </div>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Additional Information</h3>
        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Remarks</label>
            <p style="color: #333; font-size: 16px; margin: 0; white-space: pre-wrap;">{{ $production->remarks ?: '-' }}</p>
        </div>
    </div>
</div>
@endsection


