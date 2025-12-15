@extends('layouts.dashboard')

@section('title', 'View Company Information - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Company Information Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('company-information.edit', $companyInfo->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('company-information.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Branch Information</h3>
        <p style="margin: 8px 0; color: #666;">
            <strong style="color: #333;">Branch Name:</strong> {{ $companyInfo->branch->name }}
        </p>
        <p style="margin: 8px 0; color: #666;">
            <strong style="color: #333;">Branch Code:</strong> {{ $companyInfo->branch->code }}
        </p>
    </div>

    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; padding: 20px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 20px;">Company Details</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <p style="margin: 8px 0; color: #666;">
                    <strong style="color: #333;">Company Name:</strong><br>
                    <span style="font-size: 16px; color: #333;">{{ $companyInfo->company_name }}</span>
                </p>
            </div>
            <div>
                @if($companyInfo->logo_path)
                    <p style="margin: 8px 0 10px 0; color: #666;">
                        <strong style="color: #333;">Company Logo:</strong>
                    </p>
                    @php
                        $logoUrl = asset('storage/' . $companyInfo->logo_path);
                    @endphp
                    <img src="{{ $logoUrl }}" alt="Company Logo" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 5px; padding: 5px;" onerror="this.style.display='none';">
                @else
                    <p style="margin: 8px 0; color: #999;">
                        <strong style="color: #333;">Company Logo:</strong> Not uploaded
                    </p>
                @endif
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <p style="margin: 8px 0; color: #666;">
                <strong style="color: #333;">Address:</strong><br>
                {{ $companyInfo->address_line_1 }}<br>
                @if($companyInfo->address_line_2)
                    {{ $companyInfo->address_line_2 }}<br>
                @endif
                {{ $companyInfo->city }}, {{ $companyInfo->state }} - {{ $companyInfo->pincode }}
            </p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <p style="margin: 8px 0; color: #666;">
                    <strong style="color: #333;">GSTIN:</strong><br>
                    <span style="font-size: 16px; color: #333; font-weight: 500;">{{ $companyInfo->gstin }}</span>
                </p>
            </div>
            <div>
                <p style="margin: 8px 0; color: #666;">
                    <strong style="color: #333;">Email:</strong><br>
                    {{ $companyInfo->email ?? 'N/A' }}
                </p>
            </div>
        </div>

        <div>
            <p style="margin: 8px 0; color: #666;">
                <strong style="color: #333;">Phone:</strong><br>
                {{ $companyInfo->phone ?? 'N/A' }}
            </p>
        </div>
    </div>

    <div style="background: #fff3cd; padding: 20px; border-radius: 5px; border: 1px solid #ffc107;">
        <h3 style="color: #856404; font-size: 16px; margin-bottom: 15px;">Preview - How it appears in Quotations/Invoices</h3>
        <div style="background: white; padding: 20px; border-radius: 5px; border: 1px solid #ddd;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                <div>
                    @if($companyInfo->logo_path)
                        @php
                            $logoUrl = asset('storage/' . $companyInfo->logo_path);
                        @endphp
                        <img src="{{ $logoUrl }}" alt="Logo" style="max-width: 150px; max-height: 80px; margin-bottom: 10px;" onerror="this.style.display='none';">
                    @endif
                    <h4 style="color: #333; font-size: 18px; margin: 0 0 10px 0;">{{ $companyInfo->company_name }}</h4>
                    <p style="color: #666; font-size: 14px; margin: 5px 0; line-height: 1.6;">
                        {{ $companyInfo->address_line_1 }}<br>
                        @if($companyInfo->address_line_2)
                            {{ $companyInfo->address_line_2 }}<br>
                        @endif
                        {{ $companyInfo->city }}, {{ $companyInfo->state }} - {{ $companyInfo->pincode }}
                    </p>
                    <p style="color: #666; font-size: 14px; margin: 5px 0;">
                        <strong>GSTIN:</strong> {{ $companyInfo->gstin }}
                    </p>
                    @if($companyInfo->email || $companyInfo->phone)
                        <p style="color: #666; font-size: 14px; margin: 5px 0;">
                            @if($companyInfo->email)Email: {{ $companyInfo->email }}@endif
                            @if($companyInfo->email && $companyInfo->phone) | @endif
                            @if($companyInfo->phone)Phone: {{ $companyInfo->phone }}@endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

