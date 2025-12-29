@extends('layouts.dashboard')

@section('title', 'Notes - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('notes.index');
    $canRead = $user->canRead($formName);
    $canWrite = $user->canWrite($formName);
    $canDelete = $user->canDelete($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Notes</h2>
        @if($canWrite)
            <a href="{{ route('notes.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> New Note
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('notes.index') }}" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, content, or customer..."
                style="flex: 1; min-width: 200px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            <select name="visibility" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                <option value="">All Visibility</option>
                <option value="internal" {{ request('visibility') === 'internal' ? 'selected' : '' }}>Internal</option>
                <option value="external" {{ request('visibility') === 'external' ? 'selected' : '' }}>External</option>
            </select>
            <button type="submit" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request('search') || request('visibility'))
                <a href="{{ route('notes.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>

    @if($notes->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Title</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Customer</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Created By</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Date Created</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Visibility</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notes as $note)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($notes->currentPage() - 1) * $notes->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $note->title }}</td>
                            <td style="padding: 12px; color: #333;">{{ $note->customer ? $note->customer->customer_name : '-' }}</td>
                            <td style="padding: 12px; color: #333;">{{ $note->creator ? $note->creator->name : '-' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $note->created_at->format('d-m-Y') }}</td>
                            <td style="padding: 12px; color: #333;">
                                <span style="padding: 4px 8px; background: {{ $note->visibility === 'internal' ? '#17a2b8' : '#28a745' }}; color: white; border-radius: 4px; font-size: 12px;">
                                    {{ ucfirst($note->visibility) }}
                                </span>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    @if($canRead)
                                        <a href="{{ route('notes.show', $note->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                    @if($canWrite)
                                        <a href="{{ route('notes.edit', $note->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                    @if($canDelete)
                                        <form action="{{ route('notes.destroy', $note->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this note?');">
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

        @include('partials.pagination', ['paginator' => $notes, 'routeUrl' => route('notes.index')])
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No notes found.</p>
            @if($canWrite)
                <a href="{{ route('notes.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    Create First Note
                </a>
            @endif
        </div>
    @endif
</div>
@endsection

