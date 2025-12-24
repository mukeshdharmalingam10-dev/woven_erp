@extends('layouts.dashboard')

@section('title', 'View Sales Invoice - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333; font-size: 22px; margin: 0;">Sales Invoice Details</h2>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('sales-invoices.edit', $salesInvoice->id) }}" style="padding: 8px 16px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('sales-invoices.index') }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div>
            <div style="font-size: 13px; color: #6b7280;">Invoice Number</div>
            <div style="font-weight: 600; color: #111827;">{{ $salesInvoice->invoice_number }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Customer</div>
            <div style="font-weight: 600; color: #111827;">{{ $salesInvoice->customer->customer_name ?? '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Invoice Date</div>
            <div style="font-weight: 600; color: #111827;">{{ optional($salesInvoice->invoice_date)->format('d-m-Y') }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">GST Percentage (Overall)</div>
            <div style="font-weight: 600; color: #111827;">
                {{ $salesInvoice->gst_percentage_overall !== null ? $salesInvoice->gst_percentage_overall . '%' : '-' }}
            </div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">GST Classification</div>
            <div style="font-weight: 600; color: #111827;">
                @if($salesInvoice->gst_classification === 'CGST_SGST')
                    CGST + SGST
                @elseif($salesInvoice->gst_classification === 'IGST')
                    IGST
                @else
                    -
                @endif
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
        <div>
            <div style="font-size: 13px; color: #6b7280; margin-bottom: 6px;">Billing Address</div>
            <div style="padding: 10px; background: #f9fafb; border-radius: 5px; border: 1px solid #e5e7eb; white-space: pre-line;">{{ $salesInvoice->billing_address ?: '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280; margin-bottom: 6px;">Shipping Address</div>
            <div style="padding: 10px; background: #f9fafb; border-radius: 5px; border: 1px solid #e5e7eb; white-space: pre-line;">{{ $salesInvoice->shipping_address ?: '-' }}</div>
        </div>
    </div>

    <h3 style="margin-bottom: 12px; font-size: 18px; color: #333;">Products</h3>

    <div style="overflow-x: auto; margin-bottom: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 10px; text-align: left;">Product</th>
                    <th style="padding: 10px; text-align: right;">Quantity Sold</th>
                    <th style="padding: 10px; text-align: right;">Unit Price</th>
                    <th style="padding: 10px; text-align: right;">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesInvoice->items as $item)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 8px; color: #111827;">
                            {{ $item->product->product_name ?? '-' }}
                        </td>
                        <td style="padding: 8px; text-align: right; color: #111827;">
                            {{ number_format($item->quantity_sold, 0) }}
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
                <span style="font-weight: 500; color: #374151;">Total Sales Amount:</span>
                <span style="font-weight: 600; color: #111827;">{{ number_format($salesInvoice->total_sales_amount, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: 500; color: #374151;">GST (Overall):</span>
                <span style="font-weight: 600; color: #111827;">
                    @if($salesInvoice->gst_percentage_overall !== null)
                        {{ number_format($salesInvoice->gst_percentage_overall, 2) }}%
                        @if($salesInvoice->gst_classification === 'CGST_SGST')
                            (CGST + SGST)
                        @elseif($salesInvoice->gst_classification === 'IGST')
                            (IGST)
                        @endif
                    @else
                        -
                    @endif
                </span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: 500; color: #374151;">Total GST Amount:</span>
                <span style="font-weight: 600; color: #111827;">{{ number_format($salesInvoice->total_gst_amount, 2) }}</span>
            </div>
            <div style="height: 1px; background: #e5e7eb; margin: 8px 0 10px;"></div>
            <div style="display: flex; justify-content: space-between;">
                <span style="font-weight: 700; color: #111827;">Grand Total:</span>
                <span style="font-weight: 700; color: #111827;">{{ number_format($salesInvoice->grand_total, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

