@extends('layouts.dashboard')

@section('title', 'View Material Inward - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333; font-size: 22px; margin: 0;">Material Inward Details</h2>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('material-inwards.edit', $materialInward->id) }}" style="padding: 8px 16px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('material-inwards.index') }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div>
            <div style="font-size: 13px; color: #6b7280;">Inward Number</div>
            <div style="font-weight: 600; color: #111827;">{{ $materialInward->inward_number }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Received Date</div>
            <div style="font-weight: 600; color: #111827;">{{ optional($materialInward->received_date)->format('d-m-Y') }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Supplier</div>
            <div style="font-weight: 600; color: #111827;">{{ $materialInward->supplier->supplier_name ?? '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Purchase Order</div>
            <div style="font-weight: 600; color: #111827;">{{ $materialInward->purchaseOrder->po_number ?? '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Total Amount</div>
            <div style="font-weight: 600; color: #111827;">{{ number_format($materialInward->total_amount, 2) }}</div>
        </div>
    </div>

    <h3 style="margin-bottom: 12px; font-size: 18px; color: #333;">Material Items</h3>

    <div style="overflow-x: auto; margin-bottom: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 10px; text-align: left;">Material</th>
                    <th style="padding: 10px; text-align: right;">Qty Received</th>
                    <th style="padding: 10px; text-align: left;">UOM</th>
                    <th style="padding: 10px; text-align: right;">Unit Price</th>
                    <th style="padding: 10px; text-align: right;">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materialInward->items as $item)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 8px; color: #111827;">
                            {{ $item->rawMaterial->raw_material_name ?? '-' }}
                        </td>
                        <td style="padding: 8px; text-align: right; color: #111827;">
                            {{ number_format($item->quantity_received, 0) }}
                        </td>
                        <td style="padding: 8px; color: #111827;">
                            {{ $item->unit_of_measure }}
                        </td>
                        <td style="padding: 8px; text-align: right; color: #111827;">
                            {{ number_format($item->unit_price, 2) }}
                        </td>
                        <td style="padding: 8px; text-align: right; color: #111827;">
                            {{ number_format($item->total_amount, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="display: flex; justify-content: space-between; gap: 20px; margin-top: 20px; align-items: flex-start; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 260px;">
            <div style="font-size: 13px; color: #6b7280; margin-bottom: 4px;">Remarks</div>
            <div style="padding: 10px; border-radius: 5px; border: 1px solid #e5e7eb; background: #f9fafb; min-height: 60px; white-space: pre-wrap;">
                {{ $materialInward->remarks ?: '-' }}
            </div>
        </div>

        <div style="max-width: 320px; width: 100%; background: #f9fafb; padding: 16px 18px; border-radius: 8px; border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between;">
                <span style="font-weight: 700; color: #111827;">Total Amount:</span>
                <span style="font-weight: 700; color: #111827;">{{ number_format($materialInward->total_amount, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection


