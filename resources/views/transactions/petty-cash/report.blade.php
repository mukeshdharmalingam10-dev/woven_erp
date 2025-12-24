@extends('layouts.dashboard')

@section('title', 'Daily Expense Report - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('petty-cash.index');
    $canRead = $user->canRead($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Daily Expense Report</h2>
        <a href="{{ route('petty-cash.index') }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    {{-- Filter Form --}}
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
        <form method="GET" action="{{ route('petty-cash.report') }}" id="reportForm">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                <div>
                    <label for="start_date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                           style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                </div>

                <div>
                    <label for="end_date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                           style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                </div>

                <div>
                    <label for="expense_category" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Expense Category</label>
                    <select name="expense_category" id="expense_category"
                            style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('expense_category') === $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-search"></i> Generate Report
                </button>
                <button type="button" onclick="exportPdf()" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                <button type="button" onclick="exportExcel()" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                @if(request()->anyFilled(['start_date', 'end_date', 'expense_category']))
                    <a href="{{ route('petty-cash.report') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center;">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($pettyCashEntries->count() > 0)
        {{-- Summary Cards --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 25px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 8px; color: white;">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Total Entries</div>
                <div style="font-size: 32px; font-weight: 700;">{{ $pettyCashEntries->count() }}</div>
            </div>
            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 20px; border-radius: 8px; color: white;">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Total Amount</div>
                <div style="font-size: 32px; font-weight: 700;">{{ number_format($totalAmount, 2) }}</div>
            </div>
            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 20px; border-radius: 8px; color: white;">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Categories</div>
                <div style="font-size: 32px; font-weight: 700;">{{ $categoryTotals->count() }}</div>
            </div>
        </div>

        {{-- Category Summary --}}
        @if($categoryTotals->count() > 0)
            <div style="margin-bottom: 25px;">
                <h3 style="color: #333; font-size: 18px; margin-bottom: 15px;">Summary by Category</h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Category</th>
                                <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Count</th>
                                <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoryTotals as $category => $data)
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 12px; color: #333;">{{ $category }}</td>
                                    <td style="padding: 12px; text-align: right; color: #666;">{{ $data['count'] }}</td>
                                    <td style="padding: 12px; text-align: right; color: #333; font-weight: 500;">{{ number_format($data['total'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr style="background: #f8f9fa; border-top: 2px solid #dee2e6;">
                                <td style="padding: 12px; font-weight: 700; color: #333;">Grand Total</td>
                                <td style="padding: 12px; text-align: right; font-weight: 700; color: #333;">{{ $pettyCashEntries->count() }}</td>
                                <td style="padding: 12px; text-align: right; font-weight: 700; color: #333;">{{ number_format($totalAmount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Detailed Report Table --}}
        <div style="margin-bottom: 25px;">
            <h3 style="color: #333; font-size: 18px; margin-bottom: 15px;">Detailed Report</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Expense ID</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Date</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Category</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Description</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Amount</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Payment Method</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Paid To</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pettyCashEntries as $entry)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px; color: #333; font-weight: 500;">{{ $entry->expense_id }}</td>
                                <td style="padding: 12px; color: #666;">{{ optional($entry->date)->format('d-m-Y') }}</td>
                                <td style="padding: 12px; color: #333;">{{ $entry->expense_category }}</td>
                                <td style="padding: 12px; color: #333;">{{ Str::limit($entry->description, 30) ?: '-' }}</td>
                                <td style="padding: 12px; text-align: right; color: #333; font-weight: 500;">{{ number_format($entry->amount, 2) }}</td>
                                <td style="padding: 12px; color: #333;">{{ $entry->payment_method }}</td>
                                <td style="padding: 12px; color: #333;">{{ $entry->paid_to }}</td>
                            </tr>
                        @endforeach
                        <tr style="background: #f8f9fa; border-top: 2px solid #dee2e6;">
                            <td colspan="4" style="padding: 12px; font-weight: 700; color: #333; text-align: right;">Total:</td>
                            <td style="padding: 12px; text-align: right; font-weight: 700; color: #333;">{{ number_format($totalAmount, 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-chart-line" style="font-size: 48px; margin-bottom: 20px; opacity: 0.5;"></i>
            <p style="font-size: 18px; margin-bottom: 10px;">No data found for the selected filters.</p>
            <p style="font-size: 14px; color: #999;">Please adjust your date range or category filter and try again.</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function exportPdf() {
        const form = document.getElementById('reportForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        window.open('{{ route("petty-cash.export.pdf") }}?' + params.toString(), '_blank');
    }

    function exportExcel() {
        const form = document.getElementById('reportForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        window.location.href = '{{ route("petty-cash.export.excel") }}?' + params.toString();
    }
</script>
@endpush
@endsection

