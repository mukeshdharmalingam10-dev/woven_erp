@extends('layouts.dashboard')

@section('title', 'Edit Quotation - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333; font-size: 22px; margin: 0;">Edit Quotation</h2>
        <a href="{{ route('quotations.index') }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <strong>There were some problems with your input:</strong>
            <ul style="margin-top: 8px; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('quotations.update', $quotation->id) }}" id="quotationForm">
        @csrf
        @method('PUT')
        @include('transactions.quotations._form')

        <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
            <a href="{{ route('quotations.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                Cancel
            </a>
            <button type="submit" name="action" value="save" style="padding: 10px 22px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                <i class="fas fa-save"></i> Update Quotation
            </button>
            <button type="submit" name="print" value="1" id="saveAndPrintBtn" style="padding: 10px 22px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                <i class="fas fa-print"></i> Save and Print
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Handle Save and Print button - ensure print dialog opens after save
    document.addEventListener('DOMContentLoaded', function() {

        const saveAndPrintBtn = document.getElementById('saveAndPrintBtn');
        const form = document.getElementById('quotationForm');
        
        if (saveAndPrintBtn && form) {
            saveAndPrintBtn.addEventListener('click', function(e) {
                // Set a flag in sessionStorage to indicate print is needed
                sessionStorage.setItem('quotation_auto_print', '1');
            });
        }
    });
</script>
@endpush
@endsection

