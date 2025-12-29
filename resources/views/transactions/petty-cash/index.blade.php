@extends('layouts.dashboard')

@section('title', 'Daily Expense - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('petty-cash.index');
    $canRead = $user->canRead($formName);
    $canWrite = $user->canWrite($formName);
    $canDelete = $user->canDelete($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Daily Expense</h2>
        @if($canWrite)
            <a href="{{ route('petty-cash.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> New Daily Expense Entry
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('petty-cash.index') }}" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by expense ID, paid to, category, or description..."
                style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            <button type="submit" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('petty-cash.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>

    @if($pettyCash->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Expense ID</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Date</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Category</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Description</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Amount</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Payment Method</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Paid To</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pettyCash as $entry)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($pettyCash->currentPage() - 1) * $pettyCash->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $entry->expense_id }}</td>
                            <td style="padding: 12px; color: #666;">{{ optional($entry->date)->format('d-m-Y') }}</td>
                            <td style="padding: 12px; color: #333;">{{ $entry->expense_category }}</td>
                            <td style="padding: 12px; color: #333;">{{ Str::limit($entry->description, 30) }}</td>
                            <td style="padding: 12px; color: #333; text-align: right;">{{ number_format($entry->amount, 2) }}</td>
                            <td style="padding: 12px; color: #333;">{{ $entry->payment_method }}</td>
                            <td style="padding: 12px; color: #333;">{{ $entry->paid_to }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    @if($canRead)
                                        <a href="{{ route('petty-cash.show', $entry->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                    @if($canWrite)
                                        <a href="{{ route('petty-cash.edit', $entry->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                    @if($canDelete)
                                        <form action="{{ route('petty-cash.destroy', $entry->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this daily expense entry?');">
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

        @include('partials.pagination', ['paginator' => $pettyCash, 'routeUrl' => route('petty-cash.index')])
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No daily expense entries found.</p>
            @if($canWrite)
                <a href="{{ route('petty-cash.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    Create First Daily Expense Entry
                </a>
            @endif
        </div>
    @endif
</div>
@endsection

