@extends('layouts.dashboard')

@section('title', 'Sales Invoices - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('sales-invoices.index');
    $canRead = $user->canRead($formName);
    $canWrite = $user->canWrite($formName);
    $canDelete = $user->canDelete($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Sales Invoices</h2>
        @if($canWrite)
            <a href="{{ route('sales-invoices.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> New Sales Invoice
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('sales-invoices.index') }}" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by invoice number or customer..."
                style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            <button type="submit" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('sales-invoices.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>

    @if($salesInvoices->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Invoice Number</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Customer</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Invoice Date</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Grand Total</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salesInvoices as $invoice)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($salesInvoices->currentPage() - 1) * $salesInvoices->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $invoice->invoice_number }}</td>
                            <td style="padding: 12px; color: #333;">{{ $invoice->customer->customer_name ?? '-' }}</td>
                            <td style="padding: 12px; color: #666;">{{ optional($invoice->invoice_date)->format('d-m-Y') }}</td>
                            <td style="padding: 12px; color: #333; text-align: right;">{{ number_format($invoice->grand_total, 2) }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    @if($canRead)
                                        <a href="{{ route('sales-invoices.show', $invoice->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                    @if($canWrite)
                                        <a href="{{ route('sales-invoices.edit', $invoice->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                    @if($canDelete)
                                        <form action="{{ route('sales-invoices.destroy', $invoice->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this sales invoice?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @include('partials.pagination', ['paginator' => $salesInvoices, 'routeUrl' => route('sales-invoices.index')])
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No sales invoices found.</p>
            @if($canWrite)
                <a href="{{ route('sales-invoices.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    Create First Sales Invoice
                </a>
            @endif
        </div>
    @endif
</div>

@if(request('print_id'))
    @push('scripts')
    <script>
        (function() {
            var printId = {{ request('print_id') }};
            var printUrl = '{{ route("sales-invoices.print", ":id") }}'.replace(':id', printId);
            var printWindow = window.open(printUrl, '_blank');
            
            // The print will be triggered automatically by the export-pdf view's window.onload
            // This ensures the window opens and the print dialog appears
            if (printWindow) {
                // Focus the print window
                printWindow.focus();
            }
        })();
    </script>
    @endpush
@endif
@endsection

