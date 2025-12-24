@extends('layouts.dashboard')

@section('title', 'Raw Material Stock - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('raw-materials.index');
    $canRead = $user->canRead($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">
            <i class="fas fa-boxes" style="color: #667eea; margin-right: 10px;"></i>
            Raw Material Stock Report
        </h2>
    </div>

    <form method="GET" action="{{ route('stock.raw-material') }}" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or code..."
                style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            <button type="submit" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('stock.raw-material') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>

    @if(count($stockData) > 0)
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
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockData as $index => $data)
                        <tr style="border-bottom: 1px solid #dee2e6; {{ $data['is_low_stock'] ? 'background-color: #fff3cd;' : '' }}">
                            <td style="padding: 12px; color: #666;">{{ $index + 1 }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $data['raw_material']->code ?? 'N/A' }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $data['raw_material']->raw_material_name ?? 'N/A' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $data['raw_material']->unit_of_measure ?? '-' }}</td>
                            <td style="padding: 12px; text-align: right; color: #333; font-weight: 600;">
                                {{ number_format($data['current_stock'], 0) }}
                            </td>
                            <td style="padding: 12px; text-align: right; color: #666;">
                                {{ number_format($data['reorder_level'], 0) }}
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                @if($data['is_low_stock'])
                                    <span style="padding: 6px 12px; background: #ffc107; color: #333; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                        <i class="fas fa-exclamation-triangle"></i> Low Stock
                                    </span>
                                @else
                                    <span style="padding: 6px 12px; background: #28a745; color: white; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                        <i class="fas fa-check"></i> In Stock
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <p style="margin: 0; color: #666; font-size: 13px;">
                <strong>Note:</strong> Stock is calculated as: Initial Quantity + Material Inward - Production Consumption
            </p>
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-box-open" style="font-size: 48px; margin-bottom: 20px; opacity: 0.5;"></i>
            <p style="font-size: 18px; margin-bottom: 10px;">No raw materials found.</p>
            <p style="font-size: 14px; color: #999;">Please create raw materials in Raw Material Master first.</p>
        </div>
    @endif
</div>
@endsection

