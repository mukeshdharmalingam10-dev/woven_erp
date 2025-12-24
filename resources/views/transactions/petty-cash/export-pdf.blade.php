<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Expense Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .filters p {
            margin: 3px 0;
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
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .summary h3 {
            margin-top: 0;
            color: #333;
        }
        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Daily Expense Report</h1>
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
        @if($request->filled('start_date') || $request->filled('end_date'))
            <p>Period: 
                {{ $request->filled('start_date') ? date('d-m-Y', strtotime($request->start_date)) : 'All' }} 
                to 
                {{ $request->filled('end_date') ? date('d-m-Y', strtotime($request->end_date)) : 'All' }}
            </p>
        @endif
        @if($request->filled('expense_category'))
            <p>Category: {{ $request->expense_category }}</p>
        @endif
    </div>

    @if($pettyCashEntries->count() > 0)
        {{-- Summary by Category --}}
        @if($categoryTotals->count() > 0)
            <div class="summary">
                <h3>Summary by Category</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th class="text-right">Count</th>
                            <th class="text-right">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categoryTotals as $category => $data)
                            <tr>
                                <td>{{ $category }}</td>
                                <td class="text-right">{{ $data['count'] }}</td>
                                <td class="text-right">{{ number_format($data['total'], 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td><strong>Grand Total</strong></td>
                            <td class="text-right"><strong>{{ $pettyCashEntries->count() }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($totalAmount, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Detailed Report --}}
        <h3>Detailed Report</h3>
        <table>
            <thead>
                <tr>
                    <th>Expense ID</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                    <th>Payment Method</th>
                    <th>Paid To</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pettyCashEntries as $entry)
                    <tr>
                        <td>{{ $entry->expense_id }}</td>
                        <td>{{ optional($entry->date)->format('d-m-Y') }}</td>
                        <td>{{ $entry->expense_category }}</td>
                        <td>{{ $entry->description ?: '-' }}</td>
                        <td class="text-right">{{ number_format($entry->amount, 2) }}</td>
                        <td>{{ $entry->payment_method }}</td>
                        <td>{{ $entry->paid_to }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalAmount, 2) }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    @else
        <p style="text-align: center; padding: 40px; color: #666;">
            No data found for the selected filters.
        </p>
    @endif
</body>
</html>

