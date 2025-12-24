<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Invoice - {{ $salesInvoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .company-info {
            flex: 1;
        }
        .invoice-info {
            text-align: right;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 3px 0;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: 600;
            color: #555;
            display: inline-block;
            min-width: 140px;
        }
        .info-value {
            color: #111;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #667eea;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .summary-label {
            font-weight: 500;
            color: #374151;
        }
        .summary-value {
            font-weight: 600;
            color: #111827;
        }
        .grand-total {
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }
        .grand-total .summary-label,
        .grand-total .summary-value {
            font-weight: 700;
            font-size: 14px;
        }
        @media print {
            body {
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <div class="company-info">
                @if($companyInfo)
                    <h1>{{ $companyInfo->company_name }}</h1>
                    <p>{{ $companyInfo->address_line_1 }}</p>
                    @if($companyInfo->address_line_2)
                        <p>{{ $companyInfo->address_line_2 }}</p>
                    @endif
                    <p>{{ $companyInfo->city }}, {{ $companyInfo->state }} - {{ $companyInfo->pincode }}</p>
                    @if($companyInfo->gstin)
                        <p><strong>GSTIN:</strong> {{ $companyInfo->gstin }}</p>
                    @endif
                    @if($companyInfo->email)
                        <p><strong>Email:</strong> {{ $companyInfo->email }}</p>
                    @endif
                    @if($companyInfo->phone)
                        <p><strong>Phone:</strong> {{ $companyInfo->phone }}</p>
                    @endif
                @else
                    <h1>Company Information</h1>
                @endif
            </div>
            <div class="invoice-info">
                <h1 style="text-align: right; margin: 0 0 10px 0;">SALES INVOICE</h1>
                <p><strong>Invoice Number:</strong> {{ $salesInvoice->invoice_number }}</p>
                <p><strong>Invoice Date:</strong> {{ optional($salesInvoice->invoice_date)->format('d-m-Y') }}</p>
                @if($salesInvoice->buyer_order_number)
                    <p><strong>Buyer Order Number:</strong> {{ $salesInvoice->buyer_order_number }}</p>
                @endif
                <p><strong>Mode of Order:</strong> {{ $salesInvoice->mode_of_order ?? 'IMMEDIATE' }}</p>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Customer Information</div>
        <div class="two-columns">
            <div>
                <div class="info-row">
                    <span class="info-label">Customer Name:</span>
                    <span class="info-value">{{ $salesInvoice->customer->customer_name ?? '-' }}</span>
                </div>
                @if($salesInvoice->customer && $salesInvoice->customer->code)
                    <div class="info-row">
                        <span class="info-label">Customer Code:</span>
                        <span class="info-value">{{ $salesInvoice->customer->code }}</span>
                    </div>
                @endif
                @if($salesInvoice->customer && $salesInvoice->customer->gstin)
                    <div class="info-row">
                        <span class="info-label">GSTIN:</span>
                        <span class="info-value">{{ $salesInvoice->customer->gstin }}</span>
                    </div>
                @endif
            </div>
            <div>
                <div class="info-row">
                    <span class="info-label">Billing Address:</span>
                </div>
                <div class="info-value" style="white-space: pre-line; margin-left: 140px;">{{ $salesInvoice->billing_address ?: '-' }}</div>
            </div>
        </div>
        @if($salesInvoice->shipping_address)
            <div style="margin-top: 10px;">
                <div class="info-row">
                    <span class="info-label">Shipping Address:</span>
                </div>
                <div class="info-value" style="white-space: pre-line; margin-left: 140px;">{{ $salesInvoice->shipping_address }}</div>
            </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Product Details</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 25%;">Product</th>
                    <th style="width: 30%;">Description</th>
                    <th style="width: 10%;" class="text-center">Quantity</th>
                    <th style="width: 15%;" class="text-right">Unit Price</th>
                    <th style="width: 15%;" class="text-right">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesInvoice->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->product->product_name ?? '-' }} @if($item->product && $item->product->code)({{ $item->product->code }})@endif</td>
                        <td>{{ $item->description ?: '-' }}</td>
                        <td class="text-center">{{ number_format($item->quantity_sold, 0) }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="summary">
            <div class="summary-row">
                <span class="summary-label">Total Sales Amount:</span>
                <span class="summary-value">{{ number_format($salesInvoice->total_sales_amount, 2) }}</span>
            </div>
            @if($salesInvoice->gst_percentage_overall !== null)
                <div class="summary-row">
                    <span class="summary-label">GST ({{ number_format($salesInvoice->gst_percentage_overall, 2) }}%):
                        @if($salesInvoice->gst_classification === 'CGST_SGST')
                            (CGST + SGST)
                        @elseif($salesInvoice->gst_classification === 'IGST')
                            (IGST)
                        @endif
                    </span>
                    <span class="summary-value">{{ number_format($salesInvoice->total_gst_amount, 2) }}</span>
                </div>
            @endif
            <div class="summary-row grand-total">
                <span class="summary-label">Grand Total:</span>
                <span class="summary-value">{{ number_format($salesInvoice->grand_total, 2) }}</span>
            </div>
        </div>
    </div>

    <div style="margin-top: 40px; text-align: center; color: #666; font-size: 11px;">
        <p>This is a computer-generated invoice. No signature required.</p>
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

