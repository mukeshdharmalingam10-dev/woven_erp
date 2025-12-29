@extends('layouts.dashboard')

@section('title', 'Work Orders - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('work-orders.index');
    $canRead = $user->canRead($formName);
    $canWrite = $user->canWrite($formName);
    $canDelete = $user->canDelete($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Work Orders</h2>
        @if($canWrite)
            <a href="{{ route('work-orders.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> New Work Order
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('work-orders.index') }}" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by WO number, customer, or product..."
                style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            <button type="submit" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('work-orders.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>

    @if($workOrders->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Work Order Number</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Customer</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Product</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Qty to Produce</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Per Kg Weight</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Work Order Date</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Status</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workOrders as $wo)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($workOrders->currentPage() - 1) * $workOrders->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $wo->work_order_number }}</td>
                            <td style="padding: 12px; color: #333;">{{ $wo->customer->customer_name ?? '-' }}</td>
                            <td style="padding: 12px; color: #333;">{{ $wo->product->product_name ?? '-' }}</td>
                            <td style="padding: 12px; color: #333; text-align: right;">{{ number_format($wo->quantity_to_produce, 0) }}</td>
                            <td style="padding: 12px; color: #333; text-align: right;">
                                {{ $wo->per_kg_weight !== null ? number_format($wo->per_kg_weight, 3) : '-' }}
                            </td>
                            <td style="padding: 12px; color: #666;">{{ optional($wo->work_order_date)->format('d-m-Y') }}</td>
                            <td style="padding: 12px; text-align: center;">
                                @if($wo->status === \App\Models\WorkOrder::STATUS_COMPLETED)
                                    <span style="padding: 4px 12px; background: #d4edda; color: #155724; border-radius: 12px; font-size: 12px;">Completed</span>
                                @else
                                    <span style="padding: 4px 12px; background: #fff3cd; color: #856404; border-radius: 12px; font-size: 12px;">Open</span>
                                @endif
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    @if($canRead)
                                        <a href="{{ route('work-orders.show', $wo->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                    @if($canWrite)
                                        <a href="{{ route('work-orders.edit', $wo->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                    @if($canDelete)
                                        <form action="{{ route('work-orders.destroy', $wo->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this work order?');">
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

        @include('partials.pagination', ['paginator' => $workOrders, 'routeUrl' => route('work-orders.index')])
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No work orders found.</p>
            @if($canWrite)
                <a href="{{ route('work-orders.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    Create First Work Order
                </a>
            @endif
        </div>
    @endif
</div>
@endsection


