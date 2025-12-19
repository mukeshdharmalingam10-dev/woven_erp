@extends('layouts.dashboard')

@section('title', 'Raw Materials - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    @php
        $user = auth()->user();
        $formName = $user->getFormNameFromRoute('raw-materials.index');
        $canRead = $user->canRead($formName);
        $canWrite = $user->canWrite($formName);
        $canDelete = $user->canDelete($formName);
    @endphp
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Raw Materials</h2>
        @if($canWrite)
            <a href="{{ route('raw-materials.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> Add New Raw Material
            </a>
        @endif
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Search Form --}}
    <form method="GET" action="{{ route('raw-materials.index') }}" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, code, or unit..." 
                style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            <button type="submit" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('raw-materials.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>

    @if($rawMaterials->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Code</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Raw Material Name</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Unit</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Quantity</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Reorder Level</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Supplier</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Price/Unit</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Status</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rawMaterials as $rawMaterial)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($rawMaterials->currentPage() - 1) * $rawMaterials->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $rawMaterial->code }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $rawMaterial->raw_material_name }}</td>
                            <td style="padding: 12px; color: #666;">{{ $rawMaterial->unit_of_measure }}</td>
                            <td style="padding: 12px; text-align: right; color: #666;">
                                <span style="{{ $rawMaterial->isLowStock() ? 'color: #dc3545; font-weight: 600;' : '' }}">
                                    {{ number_format($rawMaterial->quantity_available, 2) }}
                                </span>
                            </td>
                            <td style="padding: 12px; text-align: right; color: #666;">{{ number_format($rawMaterial->reorder_level, 2) }}</td>
                            <td style="padding: 12px; color: #666;">{{ $rawMaterial->supplier ? $rawMaterial->supplier->supplier_name : 'N/A' }}</td>
                            <td style="padding: 12px; text-align: right; color: #666;">₹{{ number_format($rawMaterial->price_per_unit, 2) }}</td>
                            <td style="padding: 12px; text-align: center;">
                                @if($rawMaterial->is_active)
                                    <span style="padding: 4px 12px; background: #d4edda; color: #155724; border-radius: 12px; font-size: 12px;">Active</span>
                                @else
                                    <span style="padding: 4px 12px; background: #f8d7da; color: #721c24; border-radius: 12px; font-size: 12px;">Inactive</span>
                                @endif
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    @if($canRead)
                                        <a href="{{ route('raw-materials.show', $rawMaterial->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;" title="View">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                    @if($canWrite)
                                        <a href="{{ route('raw-materials.edit', $rawMaterial->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;" title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                    @if($canDelete)
                                        <form action="{{ route('raw-materials.destroy', $rawMaterial->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this raw material?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;" title="Delete">
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

        <div style="margin-top: 20px;">
            {{ $rawMaterials->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No raw materials found.</p>
            @if($canWrite)
                <a href="{{ route('raw-materials.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    Create First Raw Material
                </a>
            @endif
        </div>
    @endif
</div>
@endsection

