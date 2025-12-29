@php
    $editing = isset($pettyCash);
@endphp

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div>
        <label for="expense_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Expense ID</label>
        <input type="text" name="expense_id" id="expense_id"
               value="{{ old('expense_id', $editing ? $pettyCash->expense_id : '') }}"
               placeholder="Auto Generated if left empty"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('expense_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Date <span style="color:red">*</span></label>
        <input type="date" name="date" id="date" required
               value="{{ old('date', $editing ? optional($pettyCash->date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('date')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="expense_category" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Expense Category <span style="color:red">*</span></label>
        <select name="expense_category" id="expense_category" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Category --</option>
            <option value="Stationery" {{ old('expense_category', $editing ? $pettyCash->expense_category : '') === 'Stationery' ? 'selected' : '' }}>Stationery</option>
            <option value="Office Supplies" {{ old('expense_category', $editing ? $pettyCash->expense_category : '') === 'Office Supplies' ? 'selected' : '' }}>Office Supplies</option>
            <option value="Transportation" {{ old('expense_category', $editing ? $pettyCash->expense_category : '') === 'Transportation' ? 'selected' : '' }}>Transportation</option>
            <option value="Meals" {{ old('expense_category', $editing ? $pettyCash->expense_category : '') === 'Meals' ? 'selected' : '' }}>Meals</option>
            <option value="Utilities" {{ old('expense_category', $editing ? $pettyCash->expense_category : '') === 'Utilities' ? 'selected' : '' }}>Utilities</option>
            <option value="Maintenance" {{ old('expense_category', $editing ? $pettyCash->expense_category : '') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
            <option value="Marketing" {{ old('expense_category', $editing ? $pettyCash->expense_category : '') === 'Marketing' ? 'selected' : '' }}>Marketing</option>
            <option value="Other" {{ old('expense_category', $editing ? $pettyCash->expense_category : '') === 'Other' ? 'selected' : '' }}>Other</option>
        </select>
        @error('expense_category')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="description" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Description</label>
        <input type="text" name="description" id="description"
               value="{{ old('description', $editing ? $pettyCash->description : '') }}"
               placeholder="e.g., Printer toner, Taxi fare"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('description')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="amount" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Amount <span style="color:red">*</span></label>
        <input type="number" step="0.01" min="0" name="amount" id="amount" required
               value="{{ old('amount', $editing ? $pettyCash->amount : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('amount')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="payment_method" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Payment Method <span style="color:red">*</span></label>
        <select name="payment_method" id="payment_method" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="Cash" {{ old('payment_method', $editing ? $pettyCash->payment_method : 'Cash') === 'Cash' ? 'selected' : '' }}>Cash</option>
            <option value="Credit" {{ old('payment_method', $editing ? $pettyCash->payment_method : '') === 'Credit' ? 'selected' : '' }}>Credit</option>
            <option value="Debit" {{ old('payment_method', $editing ? $pettyCash->payment_method : '') === 'Debit' ? 'selected' : '' }}>Debit</option>
        </select>
        @error('payment_method')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="paid_to" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Paid To <span style="color:red">*</span></label>
        <input type="text" name="paid_to" id="paid_to" required
               value="{{ old('paid_to', $editing ? $pettyCash->paid_to : '') }}"
               placeholder="e.g., John Doe, XYZ Suppliers"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('paid_to')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="receipt" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Receipt</label>
        <input type="file" name="receipt" id="receipt" accept=".pdf,.jpg,.jpeg,.png"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;"
               onchange="previewNewReceipt(this)">
        @error('receipt')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
        @if($editing && $pettyCash->receipt_path)
            @php
                $receiptExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($pettyCash->receipt_path);
                // Use route to serve receipt file directly (more reliable than symlink)
                // Add cache-busting parameter using file modification time to force refresh
                $cacheBuster = $receiptExists ? '?v=' . \Illuminate\Support\Facades\Storage::disk('public')->lastModified($pettyCash->receipt_path) : '';
                $receiptUrl = $receiptExists ? route('petty-cash.receipt', $pettyCash->id) . $cacheBuster : null;
                $isImage = $receiptExists && in_array(strtolower(pathinfo($pettyCash->receipt_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']);
            @endphp
            @if($receiptExists && $receiptUrl)
                <div id="receipt-preview-container" style="margin-top: 12px; padding: 12px; background: #f8f9fa; border-radius: 5px; border: 1px solid #dee2e6;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                        <a href="{{ $receiptUrl }}" target="_blank" style="color: #667eea; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                            <i class="fas fa-file"></i> View Current Receipt
                        </a>
                        <button type="button" onclick="deleteReceipt({{ $pettyCash->id }})" 
                                style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; display: inline-flex; align-items: center; gap: 5px;">
                            <i class="fas fa-trash"></i> Delete Receipt
                        </button>
                    </div>
                    @if($isImage)
                        <div style="margin-top: 10px;">
                            <img id="receipt-image-preview" src="{{ $receiptUrl }}" alt="Receipt" 
                                 style="max-width: 300px; max-height: 300px; border: 1px solid #ddd; border-radius: 5px; padding: 5px; cursor: pointer;"
                                 onclick="window.open('{{ $receiptUrl }}', '_blank')"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <div style="display: none; color: #dc3545; font-size: 12px; margin-top: 5px;">Image not found. Please check the file path.</div>
                        </div>
                    @endif
                    <input type="hidden" name="delete_receipt" id="delete_receipt" value="0">
                </div>
            @else
                <div style="margin-top: 8px; padding: 8px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 5px; color: #856404; font-size: 12px;">
                    <i class="fas fa-exclamation-triangle"></i> Receipt file not found in storage. The file may have been deleted.
                </div>
            @endif
        @endif
        <small style="color: #666; font-size: 12px; display: block; margin-top: 4px;">Accepted formats: PDF, JPG, PNG (Max 5MB)</small>
    </div>
</div>

<div style="margin-bottom: 20px;">
    <label for="remarks" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Remarks</label>
    <textarea name="remarks" id="remarks" rows="3"
              placeholder="Additional remarks or comments (optional)"
              style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-family: inherit;">{{ old('remarks', $editing ? $pettyCash->remarks : '') }}</textarea>
    @error('remarks')
        <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
    @enderror
</div>

@if($editing && $pettyCash->receipt_path)
@push('scripts')
<script>
    function deleteReceipt(pettyCashId) {
        if (!confirm('Are you sure you want to delete this receipt? This action cannot be undone.')) {
            return;
        }
        
        fetch('{{ route("petty-cash.delete-receipt", $pettyCash->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Receipt deleted successfully.');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete receipt'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the receipt.');
        });
    }

</script>
@endpush
@endif

@push('scripts')
<script>
    // Preview new receipt when file is selected (for both create and edit)
    function previewNewReceipt(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension);
            
            if (isImage) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let receiptPreviewContainer = document.getElementById('receipt-preview-container');
                    let receiptImagePreview = document.getElementById('receipt-image-preview');
                    
                    if (!receiptPreviewContainer) {
                        // Create preview container if it doesn't exist
                        const receiptInput = document.getElementById('receipt');
                        const parentDiv = receiptInput.closest('div');
                        
                        receiptPreviewContainer = document.createElement('div');
                        receiptPreviewContainer.id = 'receipt-preview-container';
                        receiptPreviewContainer.style.cssText = 'margin-top: 12px; padding: 12px; background: #f8f9fa; border-radius: 5px; border: 1px solid #dee2e6;';
                        
                        const imageDiv = document.createElement('div');
                        imageDiv.style.cssText = 'margin-top: 10px;';
                        
                        receiptImagePreview = document.createElement('img');
                        receiptImagePreview.id = 'receipt-image-preview';
                        receiptImagePreview.style.cssText = 'max-width: 300px; max-height: 300px; border: 1px solid #ddd; border-radius: 5px; padding: 5px; cursor: pointer;';
                        receiptImagePreview.onclick = function() {
                            window.open(e.target.result, '_blank');
                        };
                        
                        imageDiv.appendChild(receiptImagePreview);
                        receiptPreviewContainer.appendChild(imageDiv);
                        parentDiv.appendChild(receiptPreviewContainer);
                    }
                    
                    if (receiptImagePreview) {
                        receiptImagePreview.src = e.target.result;
                        receiptImagePreview.style.display = 'block';
                        receiptImagePreview.onclick = function() {
                            window.open(e.target.result, '_blank');
                        };
                    } else {
                        const imageDiv = receiptPreviewContainer.querySelector('div[style*="margin-top: 10px"]');
                        if (imageDiv) {
                            const img = imageDiv.querySelector('img');
                            if (img) {
                                img.src = e.target.result;
                                img.style.display = 'block';
                            } else {
                                receiptImagePreview = document.createElement('img');
                                receiptImagePreview.id = 'receipt-image-preview';
                                receiptImagePreview.style.cssText = 'max-width: 300px; max-height: 300px; border: 1px solid #ddd; border-radius: 5px; padding: 5px; cursor: pointer;';
                                receiptImagePreview.src = e.target.result;
                                receiptImagePreview.onclick = function() {
                                    window.open(e.target.result, '_blank');
                                };
                                imageDiv.appendChild(receiptImagePreview);
                            }
                        }
                    }
                };
                reader.readAsDataURL(file);
            }
        }
    }
    
    // Make function available globally
    window.previewNewReceipt = previewNewReceipt;
</script>
@endpush

