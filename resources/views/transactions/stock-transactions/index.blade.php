@extends('layouts.dashboard')

@section('title', 'Stock Transactions - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('stock-transactions.index');
    $canRead = $user->canRead($formName);
    $canWrite = $user->canWrite($formName);
    $canDelete = $user->canDelete($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Stock Transactions</h2>
        @if($canWrite)
            <a href="{{ route('stock-transactions.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> New Stock Transaction
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('stock-transactions.index') }}" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by transaction number or document number..."
                style="flex: 1; min-width: 200px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            <select name="transaction_type" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                <option value="">All Types</option>
                <option value="stock_in" {{ request('transaction_type') === 'stock_in' ? 'selected' : '' }}>Stock In</option>
                <option value="stock_out" {{ request('transaction_type') === 'stock_out' ? 'selected' : '' }}>Stock Out</option>
            </select>
            <button type="submit" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request('search') || request('transaction_type'))
                <a href="{{ route('stock-transactions.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>

    @if($stockTransactions->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Transaction Number</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Date</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Type</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Item</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Quantity</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">UOM</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Source Document</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockTransactions as $transaction)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($stockTransactions->currentPage() - 1) * $stockTransactions->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $transaction->transaction_number }}</td>
                            <td style="padding: 12px; color: #666;">{{ optional($transaction->transaction_date)->format('d-m-Y') }}</td>
                            <td style="padding: 12px; color: #333;">
                                @if($transaction->transaction_type === 'stock_in')
                                    <span style="padding: 4px 8px; background: #28a745; color: white; border-radius: 4px; font-size: 12px;">Stock In</span>
                                @else
                                    <span style="padding: 4px 8px; background: #dc3545; color: white; border-radius: 4px; font-size: 12px;">Stock Out</span>
                                @endif
                            </td>
                            <td style="padding: 12px; color: #333;">
                                {{ $transaction->item_name }}
                                <small style="color: #666; display: block; font-size: 11px;">
                                    ({{ ucfirst(str_replace('_', ' ', $transaction->item_type)) }})
                                </small>
                            </td>
                            <td style="padding: 12px; color: #333; text-align: right;">{{ number_format($transaction->quantity, 3) }}</td>
                            <td style="padding: 12px; color: #333;">{{ $transaction->unit_of_measure }}</td>
                            <td style="padding: 12px; color: #333;">
                                @if($transaction->source_document_number)
                                    {{ $transaction->source_document_number }}
                                    <small style="color: #666; display: block; font-size: 11px;">
                                        ({{ ucfirst(str_replace('_', ' ', $transaction->source_document_type)) }})
                                    </small>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    @if($canRead)
                                        <a href="{{ route('stock-transactions.show', $transaction->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                    @if($canWrite)
                                        <a href="{{ route('stock-transactions.edit', $transaction->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                    @if($canDelete)
                                        <form action="{{ route('stock-transactions.destroy', $transaction->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this stock transaction?');">
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

        @include('partials.pagination', ['paginator' => $stockTransactions, 'routeUrl' => route('stock-transactions.index')])
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No stock transactions found.</p>
            @if($canWrite)
                <a href="{{ route('stock-transactions.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    Create First Stock Transaction
                </a>
            @endif
        </div>
    @endif
</div>
@endsection

