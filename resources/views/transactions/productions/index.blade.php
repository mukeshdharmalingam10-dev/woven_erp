@extends('layouts.dashboard')

@section('title', 'Productions - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('productions.index');
    $canRead = $user->canRead($formName);
    $canWrite = $user->canWrite($formName);
    $canDelete = $user->canDelete($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Production Entries</h2>
        @if($canWrite)
            <a href="{{ route('productions.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> New Production
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('productions.index') }}" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by WO number or product..."
                style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            <button type="submit" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('productions.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>

    @if($productions->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Work Order</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Product</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Produced Qty</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Weight / Unit</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Total Weight</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productions as $production)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($productions->currentPage() - 1) * $productions->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333;">
                                {{ $production->workOrder->work_order_number ?? '-' }}
                            </td>
                            <td style="padding: 12px; color: #333;">
                                {{ $production->product->product_name ?? '-' }}
                            </td>
                            <td style="padding: 12px; color: #333; text-align: right;">
                                {{ number_format($production->produced_quantity, 0) }}
                            </td>
                            <td style="padding: 12px; color: #333; text-align: right;">
                                {{ number_format($production->weight_per_unit, 2) }}
                            </td>
                            <td style="padding: 12px; color: #333; text-align: right;">
                                {{ number_format($production->total_weight, 0) }}
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    @if($canRead)
                                        <a href="{{ route('productions.show', $production->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                    @if($canWrite)
                                        <a href="{{ route('productions.edit', $production->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                    @if($canDelete)
                                        <form action="{{ route('productions.destroy', $production->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this production entry?');">
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

        @include('partials.pagination', ['paginator' => $productions, 'routeUrl' => route('productions.index')])
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No production entries found.</p>
            @if($canWrite)
                <a href="{{ route('productions.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    Create First Production Entry
                </a>
            @endif
        </div>
    @endif
</div>
@endsection


