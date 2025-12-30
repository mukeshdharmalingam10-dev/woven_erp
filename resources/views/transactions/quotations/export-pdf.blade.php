<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation - {{ $quotation->quotation_id }}</title>
    <script>
        // Check for auto_print parameter immediately (before page loads)
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('auto_print') === '1') {
                // Set a flag that we'll use later
                window.AUTO_PRINT_ENABLED = true;
            }
        })();
    </script>
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
        .quotation-info {
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
        .terms-section {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .terms-row {
            margin-bottom: 10px;
        }
        .terms-label {
            font-weight: 600;
            color: #555;
            display: inline-block;
            min-width: 150px;
        }
        .terms-value {
            color: #111;
        }
        .no-print {
            margin-bottom: 20px;
            text-align: right;
        }
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
        }
        .back-button:hover {
            background: #5568d3;
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
    <div class="no-print">
        <a href="{{ route('quotations.index') }}" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Quotations
        </a>
    </div>
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
            <div class="quotation-info">
                <h1 style="text-align: right; margin: 0 0 10px 0;">QUOTATION</h1>
                <p><strong>Quotation ID:</strong> {{ $quotation->quotation_id }}</p>
                <p><strong>Date:</strong> {{ $quotation->created_at->format('d-m-Y') }}</p>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Customer Information</div>
        <div class="two-columns">
            <div>
                <div class="info-row">
                    <span class="info-label">Customer Name:</span>
                    <span class="info-value">{{ $quotation->customer->customer_name ?? '-' }}</span>
                </div>
                @if($quotation->customer && $quotation->customer->code)
                    <div class="info-row">
                        <span class="info-label">Customer Code:</span>
                        <span class="info-value">{{ $quotation->customer->code }}</span>
                    </div>
                @endif
                @if($quotation->contact_person_name)
                    <div class="info-row">
                        <span class="info-label">Contact Person:</span>
                        <span class="info-value">{{ $quotation->contact_person_name }}</span>
                    </div>
                @endif
                @if($quotation->contact_number)
                    <div class="info-row">
                        <span class="info-label">Contact Number:</span>
                        <span class="info-value">{{ $quotation->contact_number }}</span>
                    </div>
                @endif
            </div>
            <div>
                <div class="info-row">
                    <span class="info-label">Company Name:</span>
                    <span class="info-value">{{ $quotation->company_name ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Address:</span>
                </div>
                <div class="info-value" style="white-space: pre-line; margin-left: 140px;">
                    @if($quotation->address_line_1){{ $quotation->address_line_1 }}@endif
                    @if($quotation->address_line_2){{ "\n" . $quotation->address_line_2 }}@endif
                    @if($quotation->city){{ "\n" . $quotation->city }}@endif
                    @if($quotation->state){{ ", " . $quotation->state }}@endif
                    @if($quotation->postal_code){{ " - " . $quotation->postal_code }}@endif
                    @if($quotation->country){{ "\n" . $quotation->country }}@endif
                    @if(!$quotation->address_line_1 && !$quotation->city)-@endif
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Item Details</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 20%;">Product</th>
                    <th style="width: 25%;">Description</th>
                    <th style="width: 10%;" class="text-center">Quantity</th>
                    <th style="width: 10%;" class="text-center">UOM</th>
                    <th style="width: 15%;" class="text-right">Price</th>
                    <th style="width: 15%;" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->product->product_name ?? '-' }} @if($item->product && $item->product->code)({{ $item->product->code }})@endif</td>
                        <td>{{ $item->item_description ?: '-' }}</td>
                        <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-center">{{ $item->uom ?? '-' }}</td>
                        <td class="text-right">{{ number_format($item->price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="summary">
            <div class="summary-row grand-total">
                <span class="summary-label">Total Amount:</span>
                <span class="summary-value">{{ number_format($quotation->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    @if($quotation->validity || $quotation->payment_terms || $quotation->taxes)
        <div class="section">
            <div class="section-title">Terms and Conditions</div>
            <div class="terms-section">
                @if($quotation->validity)
                    <div class="terms-row">
                        <span class="terms-label">Validity:</span>
                        <span class="terms-value">{{ $quotation->validity }}</span>
                    </div>
                @endif
                @if($quotation->payment_terms)
                    <div class="terms-row">
                        <span class="terms-label">Payment Terms:</span>
                        <span class="terms-value">{{ $quotation->payment_terms }}</span>
                    </div>
                @endif
                @if($quotation->taxes)
                    <div class="terms-row">
                        <span class="terms-label">Taxes:</span>
                        <span class="terms-value">{{ $quotation->taxes }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div style="margin-top: 40px; text-align: center; color: #666; font-size: 11px;">
        <p>This is a computer-generated quotation. No signature required.</p>
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
    </div>

    <script>
        // Auto-print when coming from Save and Print action
        // Check URL parameter or sessionStorage flag
        const urlParams = new URLSearchParams(window.location.search);
        const sessionFlag = sessionStorage.getItem('quotation_auto_print') === '1';
        const urlFlag = urlParams.get('auto_print') === '1';
        const shouldAutoPrint = urlFlag || sessionFlag;
        
        if (shouldAutoPrint) {
            // Clear the sessionStorage flag
            if (sessionFlag) {
                sessionStorage.removeItem('quotation_auto_print');
            }
            
            // Function to trigger print
            function triggerPrint() {
                window.print();
            }
            
            // Try to print immediately if page is already loaded
            if (document.readyState === 'complete') {
                setTimeout(triggerPrint, 100);
            } else if (document.readyState === 'interactive') {
                setTimeout(triggerPrint, 300);
            } else {
                // Wait for DOM to be ready
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(triggerPrint, 500);
                });
            }
            
            // Also try on window load as backup
            window.addEventListener('load', function() {
                setTimeout(triggerPrint, 1000);
            });
            
            // Final fallback
            setTimeout(triggerPrint, 2000);
            
            // Redirect back to quotations list after print dialog closes
            window.addEventListener('afterprint', function() {
                setTimeout(function() {
                    window.location.href = '{{ route('quotations.index') }}';
                }, 500);
            });
        }
    </script>
</body>
</html>

