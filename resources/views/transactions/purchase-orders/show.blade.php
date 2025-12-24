@extends('layouts.dashboard')

@section('title', 'View Purchase Order - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333; font-size: 22px; margin: 0;">Purchase Order Details</h2>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('purchase-orders.edit', $purchaseOrder->id) }}" style="padding: 8px 16px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('purchase-orders.index') }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div>
            <div style="font-size: 13px; color: #6b7280;">PO Number</div>
            <div style="font-weight: 600; color: #111827;">{{ $purchaseOrder->po_number }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Supplier</div>
            <div style="font-weight: 600; color: #111827;">{{ $purchaseOrder->supplier->supplier_name ?? '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Purchase Date</div>
            <div style="font-weight: 600; color: #111827;">{{ optional($purchaseOrder->purchase_date)->format('d-m-Y') }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Delivery Date</div>
            <div style="font-weight: 600; color: #111827;">{{ optional($purchaseOrder->delivery_date)->format('d-m-Y') ?: '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">GST Percentage (Overall)</div>
            <div style="font-weight: 600; color: #111827;">
                {{ $purchaseOrder->gst_percentage_overall !== null ? $purchaseOrder->gst_percentage_overall . '%' : '-' }}
            </div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">GST Classification</div>
            <div style="font-weight: 600; color: #111827;">
                @if($purchaseOrder->gst_classification === 'CGST_SGST')
                    CGST + SGST
                @elseif($purchaseOrder->gst_classification === 'IGST')
                    IGST
                @else
                    -
                @endif
            </div>
        </div>
    </div>

    <h3 style="margin-bottom: 12px; font-size: 18px; color: #333;">Raw Materials</h3>

    <div style="overflow-x: auto; margin-bottom: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 10px; text-align: left;">Raw Material</th>
                    <th style="padding: 10px; text-align: right;">Quantity</th>
                    <th style="padding: 10px; text-align: right;">Unit Price</th>
                    <th style="padding: 10px; text-align: right;">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $item)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 8px; color: #111827;">
                            {{ $item->rawMaterial->raw_material_name ?? '-' }}
                        </td>
                        <td style="padding: 8px; text-align: right; color: #111827;">
                            {{ number_format($item->quantity, 3) }}
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

    <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
        <div style="max-width: 360px; width: 100%; background: #f9fafb; padding: 16px 18px; border-radius: 8px; border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: 500; color: #374151;">Total Raw Material Amount:</span>
                <span style="font-weight: 600; color: #111827;">{{ number_format($purchaseOrder->total_raw_material_amount, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: 500; color: #374151;">GST (Overall):</span>
                <span style="font-weight: 600; color: #111827;">
                    @if($purchaseOrder->gst_percentage_overall !== null)
                        {{ number_format($purchaseOrder->gst_percentage_overall, 2) }}%
                        @if($purchaseOrder->gst_classification === 'CGST_SGST')
                            (CGST + SGST)
                        @elseif($purchaseOrder->gst_classification === 'IGST')
                            (IGST)
                        @endif
                    @else
                        -
                    @endif
                </span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: 500; color: #374151;">Total GST Amount:</span>
                <span style="font-weight: 600; color: #111827;">{{ number_format($purchaseOrder->total_gst_amount, 2) }}</span>
            </div>
            <div style="height: 1px; background: #e5e7eb; margin: 8px 0 10px;"></div>
            <div style="display: flex; justify-content: space-between;">
                <span style="font-weight: 700; color: #111827;">Grand Total:</span>
                <span style="font-weight: 700; color: #111827;">{{ number_format($purchaseOrder->grand_total, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection


