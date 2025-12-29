@extends('layouts.dashboard')

@section('title', 'Credit Notes - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('credit-notes.index');
    $canRead = $user->canRead($formName);
    $canWrite = $user->canWrite($formName);
    $canDelete = $user->canDelete($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Credit Notes</h2>
        @if($canWrite)
            <a href="{{ route('credit-notes.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> New Credit Note
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('credit-notes.index') }}" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by credit note number, party name, or reference..."
                style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            <select name="status" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                <option value="">All Status</option>
                <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                <option value="Submitted" {{ request('status') == 'Submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('credit-notes.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    @if($creditNotes->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Credit Note Number</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Date</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Party</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Reference Document</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Reason</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Total Amount</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Status</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($creditNotes as $creditNote)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($creditNotes->currentPage() - 1) * $creditNotes->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $creditNote->credit_note_number }}</td>
                            <td style="padding: 12px; color: #666;">{{ optional($creditNote->credit_note_date)->format('d-m-Y') }}</td>
                            <td style="padding: 12px; color: #333;">{{ $creditNote->party_name ?? '-' }}</td>
                            <td style="padding: 12px; color: #666;">
                                @if($creditNote->reference_document_type && $creditNote->reference_document_number)
                                    {{ $creditNote->reference_document_type }}: {{ $creditNote->reference_document_number }}
                                @else
                                    Manual
                                @endif
                            </td>
                            <td style="padding: 12px; color: #666;">{{ $creditNote->credit_note_reason ?? '-' }}</td>
                            <td style="padding: 12px; color: #333; text-align: right;">{{ number_format($creditNote->total_credit_amount, 2) }}</td>
                            <td style="padding: 12px; text-align: center;">
                                @if($creditNote->status == 'Draft')
                                    <span style="padding: 4px 12px; background: #ffc107; color: #333; border-radius: 12px; font-size: 12px; font-weight: 500;">Draft</span>
                                @elseif($creditNote->status == 'Submitted')
                                    <span style="padding: 4px 12px; background: #28a745; color: white; border-radius: 12px; font-size: 12px; font-weight: 500;">Submitted</span>
                                @else
                                    <span style="padding: 4px 12px; background: #dc3545; color: white; border-radius: 12px; font-size: 12px; font-weight: 500;">Cancelled</span>
                                @endif
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    @if($canRead)
                                        <a href="{{ route('credit-notes.show', $creditNote->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                    @if($canWrite && $creditNote->isDraft())
                                        <a href="{{ route('credit-notes.edit', $creditNote->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                    @if($canDelete && $creditNote->isDraft())
                                        <form action="{{ route('credit-notes.destroy', $creditNote->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this credit note?');">
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

        @include('partials.pagination', ['paginator' => $creditNotes, 'routeUrl' => route('credit-notes.index')])
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No credit notes found.</p>
            @if($canWrite)
                <a href="{{ route('credit-notes.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    Create First Credit Note
                </a>
            @endif
        </div>
    @endif
</div>
@endsection

