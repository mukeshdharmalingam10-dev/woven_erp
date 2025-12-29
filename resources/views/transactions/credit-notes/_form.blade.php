@php
    $editing = isset($creditNote) && $creditNote->exists;
    $creditNoteReason = old('credit_note_reason', $editing ? $creditNote->credit_note_reason : '');
@endphp

{{-- Header Fields --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div>
        <label for="credit_note_number" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Credit Note Number</label>
        <input type="text" name="credit_note_number" id="credit_note_number" readonly
               value="{{ old('credit_note_number', $editing ? $creditNote->credit_note_number : '') }}"
               placeholder="Auto Generated"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5;">
        @error('credit_note_number')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="credit_note_date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Credit Note Date <span style="color:red">*</span></label>
        <input type="date" name="credit_note_date" id="credit_note_date" required
               value="{{ old('credit_note_date', $editing ? optional($creditNote->credit_note_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('credit_note_date')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="reference_document_type" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Reference Document Type</label>
        <select name="reference_document_type" id="reference_document_type"
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="Manual" {{ old('reference_document_type', $editing ? $creditNote->reference_document_type : 'Manual') == 'Manual' ? 'selected' : '' }}>Manual</option>
            <option value="Purchase Invoice" {{ old('reference_document_type', $editing ? $creditNote->reference_document_type : '') == 'Purchase Invoice' ? 'selected' : '' }}>Purchase Invoice</option>
            <option value="Sales Invoice" {{ old('reference_document_type', $editing ? $creditNote->reference_document_type : '') == 'Sales Invoice' ? 'selected' : '' }}>Sales Invoice</option>
            <option value="Dispatch" {{ old('reference_document_type', $editing ? $creditNote->reference_document_type : '') == 'Dispatch' ? 'selected' : '' }}>Dispatch</option>
        </select>
        @error('reference_document_type')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div id="reference_document_number_container" style="display: none;">
        <label for="reference_document_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Reference Document</label>
        <select name="reference_document_id" id="reference_document_id"
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Document --</option>
        </select>
        <input type="hidden" name="reference_document_number" id="reference_document_number" value="{{ old('reference_document_number', $editing ? $creditNote->reference_document_number : '') }}">
        @error('reference_document_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Party Information --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div>
        <label for="party_type" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Party Type</label>
        <select name="party_type" id="party_type"
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Party Type --</option>
            <option value="Supplier" {{ old('party_type', $editing ? $creditNote->party_type : '') == 'Supplier' ? 'selected' : '' }}>Supplier</option>
            <option value="Customer" {{ old('party_type', $editing ? $creditNote->party_type : '') == 'Customer' ? 'selected' : '' }}>Customer</option>
        </select>
        @error('party_type')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div id="party_id_container">
        <label for="party_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Party Name</label>
        <select name="party_id" id="party_id"
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Party --</option>
        </select>
        <input type="hidden" name="party_name" id="party_name">
        @error('party_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="gst_number" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">GST Number</label>
        <input type="text" name="gst_number" id="gst_number" readonly
               value="{{ old('gst_number', $editing ? $creditNote->gst_number : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5;">
        @error('gst_number')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    {{-- Currency field hidden - default to INR --}}
    <input type="hidden" name="currency" id="currency" value="{{ old('currency', $editing ? $creditNote->currency : ($companyInfo->currency ?? 'INR')) }}">
</div>

{{-- Credit Note Reason and Remarks --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div>
        <label for="credit_note_reason" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Credit Note Reason</label>
        <select name="credit_note_reason" id="credit_note_reason"
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Reason --</option>
            <option value="Sales Return" {{ $creditNoteReason == 'Sales Return' ? 'selected' : '' }}>Sales Return</option>
            <option value="Rate Difference" {{ $creditNoteReason == 'Rate Difference' ? 'selected' : '' }}>Rate Difference</option>
            <option value="Excess Billing" {{ $creditNoteReason == 'Excess Billing' ? 'selected' : '' }}>Excess Billing</option>
            <option value="Service Cancellation" {{ $creditNoteReason == 'Service Cancellation' ? 'selected' : '' }}>Service Cancellation</option>
            <option value="Damage Compensation" {{ $creditNoteReason == 'Damage Compensation' ? 'selected' : '' }}>Damage Compensation</option>
            <option value="Others" {{ $creditNoteReason == 'Others' ? 'selected' : '' }}>Others</option>
        </select>
        @error('credit_note_reason')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div id="remarks_container" style="display: none;">
        <label for="remarks" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Remarks <span style="color:red">*</span></label>
        <textarea name="remarks" id="remarks" rows="3"
                  style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; resize: vertical;">{{ old('remarks', $editing ? $creditNote->remarks : '') }}</textarea>
        @error('remarks')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Line Items --}}
<h3 style="margin-bottom: 12px; font-size: 18px; color: #333;">Line Items</h3>
<div style="overflow-x: auto; margin-bottom: 20px;">
    <table id="itemsTable" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 10px; text-align: left;">Item Name</th>
                <th style="padding: 10px; text-align: left;">Description</th>
                <th style="padding: 10px; text-align: right;">Quantity</th>
                <th style="padding: 10px; text-align: left;">Unit</th>
                <th style="padding: 10px; text-align: right;">Rate</th>
                <th style="padding: 10px; text-align: right;">Amount</th>
                <th style="padding: 10px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $oldItems = old('items', $editing ? $creditNote->items->toArray() : [['product_id' => '', 'item_name' => '', 'description' => '', 'quantity' => '', 'unit_of_measure' => '', 'rate' => '']]);
            @endphp
            @foreach($oldItems as $index => $item)
                <tr class="item-row">
                    <td style="padding: 8px;">
                        <select name="items[{{ $index }}][product_id]" class="item-product"
                                style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                            <option value="">-- Select --</option>
                        </select>
                        <input type="hidden" name="items[{{ $index }}][item_name]" class="item-name" value="{{ $item['item_name'] ?? '' }}">
                    </td>
                    <td style="padding: 8px;">
                        <textarea name="items[{{ $index }}][description]" rows="2" class="item-description"
                                  style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; resize: vertical;">{{ $item['description'] ?? '' }}</textarea>
                    </td>
                    <td style="padding: 8px;">
                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][quantity]" class="item-quantity"
                               value="{{ $item['quantity'] ?? '' }}"
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="text" name="items[{{ $index }}][unit_of_measure]" class="item-unit" readonly
                               value="{{ $item['unit_of_measure'] ?? '' }}"
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: center;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][rate]" class="item-rate"
                               value="{{ $item['rate'] ?? '' }}"
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="text" readonly class="item-amount"
                               value="{{ isset($item['amount']) ? number_format($item['amount'], 2) : '0.00' }}"
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
                        <input type="hidden" name="items[{{ $index }}][amount]" class="item-amount-hidden" value="{{ $item['amount'] ?? 0 }}">
                    </td>
                    <td style="padding: 8px; text-align: center;">
                        <button type="button" class="btn-remove-row" onclick="removeItemRow(this)"
                                style="padding: 6px 10px; background: #dc3545; color: #fff; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<button type="button" onclick="addItemRow()"
        style="padding: 10px 18px; background: #28a745; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px;">
    <i class="fas fa-plus"></i> Add Row
</button>

{{-- Tax Type and Amount Section --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; margin-bottom: 20px;">
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Tax Type</h3>
        <div style="display: flex; gap: 20px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="radio" name="tax_type" value="CGST_SGST" id="tax_type_cgst_sgst" 
                       {{ old('tax_type', $editing && isset($creditNote) && $creditNote->gst_classification ? $creditNote->gst_classification : 'CGST_SGST') === 'CGST_SGST' ? 'checked' : '' }}
                       style="width: 18px; height: 18px; cursor: pointer;">
                <span style="font-weight: 500; color: #333;">CGST and SGST</span>
            </label>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="radio" name="tax_type" value="IGST" id="tax_type_igst"
                       {{ old('tax_type', $editing && isset($creditNote) && $creditNote->gst_classification ? $creditNote->gst_classification : 'CGST_SGST') === 'IGST' ? 'checked' : '' }}
                       style="width: 18px; height: 18px; cursor: pointer;">
                <span style="font-weight: 500; color: #333;">IGST</span>
            </label>
        </div>
        <input type="hidden" name="gst_classification" id="gst_classification">
        
        <div style="margin-top: 20px;">
            <label for="gst_percentage" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">GST Percentage <span style="color:red">*</span></label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="number" step="0.01" min="0" max="100" name="gst_percentage" id="gst_percentage"
                       value="{{ old('gst_percentage', $editing && isset($creditNote) ? $creditNote->gst_percentage : '18') }}"
                       placeholder="Enter In (%)"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                <input type="text" readonly id="gst_amount"
                       value="0.00"
                       placeholder="GST Amount"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
            </div>
        </div>

        <div id="cgst_sgst_section" style="margin-top: 15px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">CGST:</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="text" readonly id="cgst_percentage" value="0.00"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
                <input type="text" readonly id="cgst_amount" value="0.00"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
            </div>
            <label style="display: block; margin-bottom: 6px; margin-top: 10px; font-weight: 600; color: #333;">SGST:</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="text" readonly id="sgst_percentage" value="0.00"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
                <input type="text" readonly id="sgst_amount" value="0.00"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
            </div>
        </div>

        <div id="igst_section" style="margin-top: 15px; display: none;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">IGST:</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="text" readonly id="igst_percentage" value="0.00"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
                <input type="text" readonly id="igst_amount" value="0.00"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
            </div>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Amount Summary</h3>
        <div style="margin-bottom: 15px;">
            <label for="subtotal" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Subtotal:</label>
            <input type="text" readonly id="subtotal" value="0.00"
                   style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
        </div>
        <div style="margin-bottom: 15px;">
            <label for="adjustments" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Adjustments:</label>
            <input type="number" step="0.01" name="adjustments" id="adjustments"
                   value="{{ old('adjustments', $editing ? $creditNote->adjustments : 0) }}"
                   style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
        </div>
        <div>
            <label for="total_credit_amount" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Total Credit Amount:</label>
            <input type="text" readonly id="total_credit_amount" value="0.00"
                   style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right; font-weight: 700;">
        </div>
    </div>
</div>

{{-- Hidden fields for calculations --}}
<input type="hidden" name="subtotal" id="subtotal_hidden" value="0">
<input type="hidden" name="cgst_amount" id="cgst_amount_hidden" value="0">
<input type="hidden" name="sgst_amount" id="sgst_amount_hidden" value="0">
<input type="hidden" name="igst_amount" id="igst_amount_hidden" value="0">
<input type="hidden" name="total_credit_amount" id="total_credit_amount_hidden" value="0">

@push('scripts')
<script>
    const products = @json($products->map(function($p) { return ['id' => $p->id, 'name' => $p->product_name, 'unit' => $p->unit_of_measure ?? '']; }));
    const suppliers = @json($suppliersMapped);
    const customers = @json($customersMapped);
    const salesInvoices = @json($salesInvoices);
    const editing = @json($editing);
    const creditNoteReason = @json($creditNoteReason);
    const companyState = @json($companyInfo->state ?? null);
    
    // Store reference document items for dropdown
    let referenceDocumentItems = [];
    
     // Initialize on page load
     document.addEventListener('DOMContentLoaded', function() {
         updateReferenceDocumentVisibility();
         updatePartyVisibility();
         updateRemarksVisibility();
         loadReferenceDocuments();
         updatePartyOptions();
         // Don't call updateItemDropdowns on page load - only when reference document is selected
         
         // If editing and reference document type is set, ensure container is visible and load items
         @if($editing && isset($creditNote) && $creditNote->reference_document_type && $creditNote->reference_document_type !== 'Manual')
             const refType = document.getElementById('reference_document_type').value;
             if (refType && refType !== 'Manual') {
                 document.getElementById('reference_document_number_container').style.display = 'block';
                // Reference document number is stored in hidden field
                
                // Load reference document items if reference document is selected
                @if($creditNote->reference_document_id)
                    const refDocId = {{ $creditNote->reference_document_id }};
                    // Load reference document details to populate items dropdown
                    fetch(`{{ route('credit-notes.reference-document-details') }}?type=${encodeURIComponent(refType)}&id=${refDocId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.items && data.items.length > 0) {
                                referenceDocumentItems = data.items;
                                updateItemDropdowns();
                                
                                // Set selected values in dropdowns based on saved item names
                                // But DON'T trigger change event to preserve saved values (rate, quantity, etc.)
                                const allProductSelects = document.querySelectorAll('.item-product');
                                allProductSelects.forEach(function(select) {
                                    const row = select.closest('tr');
                                    const itemNameInput = row.querySelector('.item-name');
                                    const savedItemName = itemNameInput ? itemNameInput.value : '';
                                    
                                    if (savedItemName) {
                                        // Find matching reference item
                                        for (let i = 0; i < select.options.length; i++) {
                                            const option = select.options[i];
                                            const optionItemName = option.getAttribute('data-name');
                                            if (optionItemName === savedItemName) {
                                                select.value = option.value;
                                                // Don't trigger change event - preserve saved values
                                                // Only update unit if it's empty
                                                const unitField = row.querySelector('.item-unit');
                                                if (unitField && !unitField.value) {
                                                    unitField.value = option.getAttribute('data-unit') || '';
                                                }
                                                break;
                                            }
                                        }
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error loading reference document details for edit:', error);
                        });
                @endif
             }
         @endif
         
         // Initialize tax type
         const taxTypeElement = document.querySelector('input[name="tax_type"]:checked');
         if (taxTypeElement) {
             document.getElementById('gst_classification').value = taxTypeElement.value;
         } else {
             // Default to CGST_SGST if nothing selected
             document.getElementById('tax_type_cgst_sgst').checked = true;
             document.getElementById('gst_classification').value = 'CGST_SGST';
         }
         
         // Initialize GST percentage if empty
         const gstPercentageField = document.getElementById('gst_percentage');
         if (!gstPercentageField.value || gstPercentageField.value === '') {
             gstPercentageField.value = '18';
         }
         
         // Update tax component visibility based on initial tax type
         updateTaxComponentVisibility();
         recalcTotals();
         
         // Set date field readonly if status is not Draft
         @if($editing && isset($creditNote) && !$creditNote->isDraft())
             document.getElementById('credit_note_date').readOnly = true;
         @endif
         
         // Handle form submission - convert reference items to proper format
         const form = document.querySelector('form');
         if (form) {
             form.addEventListener('submit', function(e) {
                 // Process all item rows
                 const allProductSelects = document.querySelectorAll('.item-product');
                 allProductSelects.forEach(function(select) {
                     const value = select.value;
                     // If value starts with "ref_", it's a reference document item
                     if (value && value.startsWith('ref_')) {
                         // Get the index from "ref_0" -> 0
                         const refIndex = parseInt(value.replace('ref_', ''));
                         const selectedOption = select.options[select.selectedIndex];
                         const itemNameInput = select.closest('tr').querySelector('.item-name');
                         
                         // Set product_id to empty (null) for reference items
                         select.value = '';
                         
                         // Ensure item_name is set from the reference item
                         if (selectedOption && itemNameInput) {
                             const itemName = selectedOption.getAttribute('data-name') || '';
                             if (itemName) {
                                 itemNameInput.value = itemName;
                             }
                         }
                     }
                 });
             });
         }
     });
    
    // Reference Document Type Change
    document.getElementById('reference_document_type').addEventListener('change', function() {
        // Clear all auto-populated fields when type changes
        clearAutoPopulatedFields();
        updateReferenceDocumentVisibility();
        loadReferenceDocuments();
        if (this.value !== 'Manual') {
            document.getElementById('party_type').disabled = true;
            document.getElementById('party_id').disabled = true;
        } else {
            document.getElementById('party_type').disabled = false;
            document.getElementById('party_id').disabled = false;
        }
    });
    
    // Reference Document Selection
    document.getElementById('reference_document_id').addEventListener('change', function() {
        console.log('Reference document selected, value:', this.value);
        if (this.value) {
            // Set the reference document number from the selected option
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption) {
                // Get document number from data-number attribute or text content
                let docNumber = selectedOption.getAttribute('data-number');
                if (!docNumber && selectedOption.textContent) {
                    docNumber = selectedOption.textContent.trim();
                }
                if (docNumber) {
                    console.log('Setting reference document number to:', docNumber);
                    document.getElementById('reference_document_number').value = docNumber;
                } else {
                    console.warn('Could not extract document number from option');
                }
            }
            loadReferenceDocumentDetails();
        } else {
            console.log('Reference document cleared');
            document.getElementById('reference_document_number').value = '';
        }
    });
    
    // Party Type Change
    document.getElementById('party_type').addEventListener('change', function() {
        updatePartyOptions();
    });
    
     // Party Selection
     document.getElementById('party_id').addEventListener('change', function() {
         updatePartyDetails();
         updateTaxTypeFromParty();
     });
     
     // Tax Type Change
     document.querySelectorAll('input[name="tax_type"]').forEach(function(radio) {
         radio.addEventListener('change', function() {
             document.getElementById('gst_classification').value = this.value;
             updateTaxComponentVisibility();
             recalcTotals();
         });
     });
     
     // GST Percentage Change
     document.getElementById('gst_percentage').addEventListener('input', function() {
         recalcTotals();
     });
     
     function updateTaxComponentVisibility() {
         const taxTypeElement = document.querySelector('input[name="tax_type"]:checked');
         const taxType = taxTypeElement ? taxTypeElement.value : 'CGST_SGST';
         
         const cgstSection = document.getElementById('cgst_sgst_section');
         const igstSection = document.getElementById('igst_section');
         
         if (taxType === 'CGST_SGST') {
             if (cgstSection) cgstSection.style.display = 'block';
             if (igstSection) igstSection.style.display = 'none';
         } else {
             if (cgstSection) cgstSection.style.display = 'none';
             if (igstSection) igstSection.style.display = 'block';
         }
     }
    
     // Credit Note Reason Change
     document.getElementById('credit_note_reason').addEventListener('change', function() {
         updateRemarksVisibility();
     });
    
    function updateReferenceDocumentVisibility() {
        const refType = document.getElementById('reference_document_type').value;
        const container = document.getElementById('reference_document_number_container');
        if (refType && refType !== 'Manual') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
            document.getElementById('reference_document_id').value = '';
            document.getElementById('reference_document_number').value = '';
        }
    }
    
    function updatePartyVisibility() {
        const refType = document.getElementById('reference_document_type').value;
        const partyTypeField = document.getElementById('party_type');
        const partyIdField = document.getElementById('party_id');
        
        if (refType && refType !== 'Manual') {
            partyTypeField.disabled = true;
            partyIdField.disabled = true;
        } else {
            partyTypeField.disabled = false;
            partyIdField.disabled = false;
        }
    }
    
    function updateRemarksVisibility() {
        const reason = document.getElementById('credit_note_reason').value;
        const container = document.getElementById('remarks_container');
        const remarksField = document.getElementById('remarks');
        
        if (reason === 'Others') {
            container.style.display = 'block';
            remarksField.required = true;
        } else {
            container.style.display = 'none';
            remarksField.required = false;
        }
    }
    
    function clearAutoPopulatedFields() {
        // Clear party fields
        document.getElementById('party_type').value = '';
        document.getElementById('party_id').value = '';
        document.getElementById('party_name').value = '';
        document.getElementById('gst_number').value = '';
        
        // Clear reference document fields
        document.getElementById('reference_document_id').value = '';
        document.getElementById('reference_document_number').value = '';
        
        // Clear reference document items
        referenceDocumentItems = [];
        updateItemDropdowns();
        
        // Clear and reset line items to one empty row
        const tbody = document.querySelector('#itemsTable tbody');
        if (tbody) {
            tbody.innerHTML = '';
            // Add one empty row
            addItemRow();
        }
        
        // Reset totals
        recalcTotals();
        
        // Reset party options
        updatePartyOptions();
    }
    
    function loadReferenceDocuments() {
        const type = document.getElementById('reference_document_type').value;
        const select = document.getElementById('reference_document_id');
        
        if (!type || type === 'Manual') {
            select.innerHTML = '<option value="">-- Select Document --</option>';
            document.getElementById('reference_document_number').value = '';
            return;
        }
        
        // Show loading state
        select.innerHTML = '<option value="">Loading...</option>';
        select.disabled = true;
        
        // Fetch documents via AJAX
        fetch(`{{ route('credit-notes.reference-documents') }}?type=${encodeURIComponent(type)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                select.disabled = false;
                select.innerHTML = '<option value="">-- Select Document --</option>';
                
                if (data && data.length > 0) {
                    data.forEach(function(doc) {
                        const option = document.createElement('option');
                        option.value = doc.id;
                        // Show only document number without date for all types
                        option.textContent = doc.number;
                        option.setAttribute('data-number', doc.number);
                        select.appendChild(option);
                    });
                } else {
                    // No documents found
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = '-- No documents found --';
                    select.appendChild(option);
                }
                
                @if($editing && isset($creditNote) && $creditNote->reference_document_id)
                    select.value = {{ $creditNote->reference_document_id }};
                    // Set the reference document number
                    const selectedOption = select.options[select.selectedIndex];
                    if (selectedOption) {
                        document.getElementById('reference_document_number').value = selectedOption.getAttribute('data-number') || '{{ $creditNote->reference_document_number ?? '' }}';
                    }
                @endif
            })
            .catch(error => {
                console.error('Error loading reference documents:', error);
                select.disabled = false;
                select.innerHTML = '<option value="">-- Error loading documents --</option>';
                // Fallback to sales invoices if available and type is Sales Invoice
                if (type === 'Sales Invoice' && typeof salesInvoices !== 'undefined' && salesInvoices.length > 0) {
                    select.innerHTML = '<option value="">-- Select Document --</option>';
                    salesInvoices.forEach(function(inv) {
                        const option = document.createElement('option');
                        option.value = inv.id;
                        option.textContent = inv.invoice_number;
                        option.setAttribute('data-date', inv.invoice_date);
                        option.setAttribute('data-number', inv.invoice_number);
                        select.appendChild(option);
                    });
                    
                    @if($editing && isset($creditNote) && $creditNote->reference_document_id)
                        select.value = {{ $creditNote->reference_document_id }};
                        const selectedOption = select.options[select.selectedIndex];
                        if (selectedOption) {
                            document.getElementById('reference_document_number').value = selectedOption.getAttribute('data-number') || '{{ $creditNote->reference_document_number ?? '' }}';
                        }
                    @endif
                }
            });
    }
    
    function loadReferenceDocumentDetails() {
        const type = document.getElementById('reference_document_type').value;
        const id = document.getElementById('reference_document_id').value;
        
        if (!type || !id || type === 'Manual') {
            console.log('Skipping loadReferenceDocumentDetails - type:', type, 'id:', id);
            return;
        }
        
        console.log('Loading reference document details - type:', type, 'id:', id);
        
        fetch(`{{ route('credit-notes.reference-document-details') }}?type=${encodeURIComponent(type)}&id=${id}`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);
                if (data) {
                    if (data.party_type) {
                        console.log('Setting party info:', data.party_type, data.party_id, data.party_name);
                        
                        // Temporarily enable fields to set values
                        const partyTypeField = document.getElementById('party_type');
                        const partyIdField = document.getElementById('party_id');
                        const wasPartyTypeDisabled = partyTypeField.disabled;
                        const wasPartyIdDisabled = partyIdField.disabled;
                        
                        partyTypeField.disabled = false;
                        partyIdField.disabled = false;
                        
                        // Set party type and update options
                        partyTypeField.value = data.party_type;
                        updatePartyOptions();
                        
                        // Set party ID and details after options are loaded
                        setTimeout(function() {
                            if (partyIdField) {
                                partyIdField.value = data.party_id;
                                updatePartyDetails();
                                
                                // Restore disabled state if needed
                                if (wasPartyTypeDisabled) {
                                    partyTypeField.disabled = true;
                                }
                                if (wasPartyIdDisabled) {
                                    partyIdField.disabled = true;
                                }
                            }
                        }, 300);
                        
                        // Set party name and GST number directly
                        document.getElementById('party_name').value = data.party_name || '';
                        document.getElementById('gst_number').value = data.gst_number || '';
                        
                        // Update tax type based on party
                        updateTaxTypeFromParty();
                    } else {
                        console.log('No party_type in data');
                    }
                    
                    if (data.items && data.items.length > 0) {
                        console.log('Storing reference document items:', data.items.length);
                        // Store items for dropdown instead of auto-populating
                        referenceDocumentItems = data.items;
                        // Update all item dropdowns to include reference document items
                        updateItemDropdowns();
                    } else {
                        console.log('No items in data or items array is empty');
                        referenceDocumentItems = [];
                        updateItemDropdowns();
                    }
                } else {
                    console.log('No data received');
                }
            })
            .catch(error => {
                console.error('Error loading reference document details:', error);
                alert('Error loading document details: ' + error.message + '. Please check the browser console for more details.');
            });
    }
    
    function updatePartyOptions() {
        const partyType = document.getElementById('party_type').value;
        const select = document.getElementById('party_id');
        
        select.innerHTML = '<option value="">-- Select Party --</option>';
        
         if (partyType === 'Supplier') {
             suppliers.forEach(function(supplier) {
                 const option = document.createElement('option');
                 option.value = supplier.id;
                 option.textContent = supplier.name + (supplier.code ? ' (' + supplier.code + ')' : '');
                 option.setAttribute('data-gst', supplier.gst || '');
                 option.setAttribute('data-state', supplier.state || '');
                 select.appendChild(option);
             });
         } else if (partyType === 'Customer') {
             customers.forEach(function(customer) {
                 const option = document.createElement('option');
                 option.value = customer.id;
                 option.textContent = customer.name + (customer.code ? ' (' + customer.code + ')' : '');
                 option.setAttribute('data-gst', customer.gst || '');
                 option.setAttribute('data-state', customer.state || '');
                 select.appendChild(option);
             });
         }
        
        @if($editing && isset($creditNote) && $creditNote->party_id)
            select.value = {{ $creditNote->party_id }};
            updatePartyDetails();
        @endif
    }
    
     function updatePartyDetails() {
         const select = document.getElementById('party_id');
         const option = select.options[select.selectedIndex];
         
         if (option && option.value) {
             document.getElementById('party_name').value = option.textContent.split(' (')[0];
             document.getElementById('gst_number').value = option.getAttribute('data-gst') || '';
         } else {
             document.getElementById('party_name').value = '';
             document.getElementById('gst_number').value = '';
         }
     }
     
     function updateTaxTypeFromParty() {
         const partyType = document.getElementById('party_type').value;
         const partyId = document.getElementById('party_id').value;
         
         if (!partyType || !partyId || !companyState) {
             return;
         }
         
         let partyState = null;
         
         if (partyType === 'Supplier') {
             const supplier = suppliers.find(s => s.id == partyId);
             if (supplier) {
                 partyState = supplier.state;
             }
         } else if (partyType === 'Customer') {
             const customer = customers.find(c => c.id == partyId);
             if (customer) {
                 partyState = customer.state;
             }
         }
         
         if (partyState) {
             const taxTypeCGST = document.getElementById('tax_type_cgst_sgst');
             const taxTypeIGST = document.getElementById('tax_type_igst');
             
             // Compare states (case-insensitive)
             if (companyState.toLowerCase() === partyState.toLowerCase()) {
                 // Same state - CGST and SGST
                 if (taxTypeCGST) {
                     taxTypeCGST.checked = true;
                     document.getElementById('gst_classification').value = 'CGST_SGST';
                 }
             } else {
                 // Different state - IGST
                 if (taxTypeIGST) {
                     taxTypeIGST.checked = true;
                     document.getElementById('gst_classification').value = 'IGST';
                 }
             }
             
             // Trigger change event to update calculations
             const selectedRadio = document.querySelector('input[name="tax_type"]:checked');
             if (selectedRadio) {
                 selectedRadio.dispatchEvent(new Event('change'));
             }
         }
     }
    
     function addItemRow() {
         const tbody = document.querySelector('#itemsTable tbody');
         const index = tbody.querySelectorAll('tr').length;
         const row = createItemRow(index);
         tbody.appendChild(row);
         attachRowEvents(row);
     }
    
    function addItemRowFromReference(item, index) {
        const tbody = document.querySelector('#itemsTable tbody');
        const row = createItemRow(index);
        
        // Set values from reference item
        const productSelect = row.querySelector('.item-product');
        const itemNameInput = row.querySelector('.item-name');
        
        if (item.product_id) {
            // Product exists - select it from dropdown
            productSelect.value = item.product_id;
            productSelect.dispatchEvent(new Event('change'));
        } else if (item.item_name) {
            // Raw material or custom item - no product_id
            // Set item name in hidden field
            itemNameInput.value = item.item_name;
            
            // For raw materials, we need to show the item name
            // Create a text input to display the item name
            const nameDisplay = document.createElement('input');
            nameDisplay.type = 'text';
            nameDisplay.value = item.item_name;
            nameDisplay.readOnly = true;
            nameDisplay.style.cssText = 'width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5;';
            nameDisplay.className = 'item-name-display';
            
            // Hide the product select and show the name display
            productSelect.style.display = 'none';
            productSelect.parentElement.insertBefore(nameDisplay, productSelect);
            
            // Set unit of measure
            if (item.unit_of_measure) {
                row.querySelector('.item-unit').value = item.unit_of_measure;
            }
        }
        
        row.querySelector('.item-description').value = item.description || '';
        row.querySelector('.item-quantity').value = item.quantity || '';
        row.querySelector('.item-rate').value = item.rate || '';
        
        tbody.appendChild(row);
        attachRowEvents(row);
        recalcRow(row);
    }
    
    function createItemRow(index) {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        
        // Build reference document items options only if they exist
        let refItemsOptions = '';
        if (referenceDocumentItems && referenceDocumentItems.length > 0) {
            refItemsOptions = referenceDocumentItems.map((item, idx) => 
                `<option value="ref_${idx}" data-unit="${item.unit_of_measure || ''}" data-name="${item.item_name || ''}" data-type="reference" data-quantity="${item.quantity || ''}" data-rate="${item.rate || ''}" data-description="${item.description || ''}" data-product-id="${item.product_id || ''}">${item.item_name || 'Item ' + (idx + 1)}</option>`
            ).join('');
        }
        
        tr.innerHTML = `
            <td style="padding: 8px;">
                <select name="items[${index}][product_id]" class="item-product"
                        style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                    <option value="">-- Select --</option>
                    ${refItemsOptions}
                </select>
                <input type="hidden" name="items[${index}][item_name]" class="item-name" value="">
            </td>
            <td style="padding: 8px;">
                <textarea name="items[${index}][description]" rows="2" class="item-description"
                          style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; resize: vertical;"></textarea>
            </td>
            <td style="padding: 8px;">
                <input type="number" step="0.01" min="0" name="items[${index}][quantity]" class="item-quantity"
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
            </td>
            <td style="padding: 8px;">
                <input type="text" name="items[${index}][unit_of_measure]" class="item-unit" readonly
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: center;">
            </td>
            <td style="padding: 8px;">
                <input type="number" step="0.01" min="0" name="items[${index}][rate]" class="item-rate"
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
            </td>
            <td style="padding: 8px;">
                <input type="text" readonly class="item-amount"
                       value="0.00"
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
                <input type="hidden" name="items[${index}][amount]" class="item-amount-hidden" value="0">
            </td>
            <td style="padding: 8px; text-align: center;">
                <button type="button" class="btn-remove-row" onclick="removeItemRow(this)"
                        style="padding: 6px 10px; background: #dc3545; color: #fff; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        return tr;
    }
    
    function removeItemRow(button) {
        const tbody = document.querySelector('#itemsTable tbody');
        if (tbody.querySelectorAll('tr').length <= 1) {
            alert('At least one item is required.');
            return;
        }
        button.closest('tr').remove();
        recalcTotals();
    }
    
    function updateItemDropdowns() {
        // Update all existing item dropdowns
        const allProductSelects = document.querySelectorAll('.item-product');
        allProductSelects.forEach(function(select) {
            updateSingleItemDropdown(select);
        });
    }
    
    function updateSingleItemDropdown(select) {
        const currentValue = select.value;
        const row = select.closest('tr');
        const itemNameInput = row ? row.querySelector('.item-name') : null;
        const savedItemName = itemNameInput ? itemNameInput.value : '';
        
        // Clear and rebuild dropdown
        select.innerHTML = '<option value="">-- Select --</option>';
        
        // Only add reference document items if they exist (when reference document is selected)
        if (referenceDocumentItems && referenceDocumentItems.length > 0) {
            referenceDocumentItems.forEach(function(item, index) {
                const option = document.createElement('option');
                option.value = 'ref_' + index; // Use prefix to identify reference items
                option.textContent = item.item_name || 'Item ' + (index + 1);
                option.setAttribute('data-unit', item.unit_of_measure || '');
                option.setAttribute('data-name', item.item_name || '');
                option.setAttribute('data-type', 'reference');
                option.setAttribute('data-quantity', item.quantity || '');
                option.setAttribute('data-rate', item.rate || '');
                option.setAttribute('data-description', item.description || '');
                option.setAttribute('data-product-id', item.product_id || '');
                select.appendChild(option);
            });
        }
        
        // Restore previous selection - try by value first, then by item name
        if (currentValue && currentValue.startsWith('ref_')) {
            // Try to restore by value
            if (select.querySelector(`option[value="${currentValue}"]`)) {
                select.value = currentValue;
            }
        } else if (savedItemName) {
            // Try to restore by matching item name
            for (let i = 0; i < select.options.length; i++) {
                const option = select.options[i];
                const optionItemName = option.getAttribute('data-name');
                if (optionItemName === savedItemName) {
                    select.value = option.value;
                    break;
                }
            }
        }
    }
    
    function attachRowEvents(row) {
        const productSelect = row.querySelector('.item-product');
        const quantityInput = row.querySelector('.item-quantity');
        const rateInput = row.querySelector('.item-rate');
        
        // Update this dropdown with reference items (only if reference items are available)
        if (referenceDocumentItems && referenceDocumentItems.length > 0) {
            updateSingleItemDropdown(productSelect);
        }
        
        productSelect.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const itemType = option.getAttribute('data-type');
            
            if (itemType === 'reference') {
                // Handle reference document item
                const itemName = option.getAttribute('data-name') || '';
                const unit = option.getAttribute('data-unit') || '';
                const quantity = option.getAttribute('data-quantity') || '';
                const rate = option.getAttribute('data-rate') || '';
                const description = option.getAttribute('data-description') || '';
                const productId = option.getAttribute('data-product-id') || '';
                
                // Set item name
                row.querySelector('.item-name').value = itemName;
                
                // Only set unit if it's empty (preserve existing value when editing)
                const unitField = row.querySelector('.item-unit');
                if (unitField && !unitField.value) {
                    unitField.value = unit;
                }
                
                // Only set quantity if it's empty (preserve existing value when editing)
                const quantityField = row.querySelector('.item-quantity');
                if (quantityField && !quantityField.value) {
                    quantityField.value = quantity;
                }
                
                // Only set rate if it's empty (preserve existing value when editing)
                const rateField = row.querySelector('.item-rate');
                if (rateField && !rateField.value) {
                    rateField.value = rate;
                }
                
                // Only set description if it's empty (preserve existing value when editing)
                const descriptionField = row.querySelector('.item-description');
                if (descriptionField && !descriptionField.value) {
                    descriptionField.value = description;
                }
                
                // If it has a product_id, set it in the hidden field or select
                if (productId) {
                    const productSelectField = row.querySelector('.item-product');
                    // Try to find matching product
                    for (let i = 0; i < productSelectField.options.length; i++) {
                        if (productSelectField.options[i].value == productId) {
                            productSelectField.value = productId;
                            break;
                        }
                    }
                }
            }
            
            recalcRow(row);
        });
        
        quantityInput.addEventListener('input', function() {
            recalcRow(row);
        });
        
        rateInput.addEventListener('input', function() {
            recalcRow(row);
        });
    }
    
    function recalcRow(row) {
        const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
        const rate = parseFloat(row.querySelector('.item-rate').value) || 0;
        const amount = quantity * rate;
        
        row.querySelector('.item-amount').value = amount.toFixed(2);
        row.querySelector('.item-amount-hidden').value = amount.toFixed(2);
        
        recalcTotals();
    }
    
     function recalcTotals() {
         const rows = document.querySelectorAll('#itemsTable tbody tr');
         let subtotal = 0;
         
         rows.forEach(function(row) {
             const amount = parseFloat(row.querySelector('.item-amount-hidden').value) || 0;
             subtotal += amount;
         });
         
         // Get GST percentage from input field
         const gstPercentage = parseFloat(document.getElementById('gst_percentage').value) || 18;
         
         // Get selected tax type
         const taxTypeElement = document.querySelector('input[name="tax_type"]:checked');
         const taxType = taxTypeElement ? taxTypeElement.value : 'CGST_SGST';
         document.getElementById('gst_classification').value = taxType;
         
         // Calculate GST amount
         const gstAmount = (subtotal * gstPercentage) / 100;
         
         let cgstRate = 0, sgstRate = 0, igstRate = 0;
         let cgstAmount = 0, sgstAmount = 0, igstAmount = 0;
         
         if (taxType === 'CGST_SGST') {
             cgstRate = gstPercentage / 2;
             sgstRate = gstPercentage / 2;
             cgstAmount = gstAmount / 2;
             sgstAmount = gstAmount / 2;
             igstAmount = 0;
         } else {
             igstRate = gstPercentage;
             igstAmount = gstAmount;
             cgstAmount = 0;
             sgstAmount = 0;
         }
         
         const adjustments = parseFloat(document.getElementById('adjustments').value) || 0;
         const totalCreditAmount = subtotal + gstAmount - adjustments;
         
         // Update display
         document.getElementById('subtotal').value = subtotal.toFixed(2);
         document.getElementById('gst_amount').value = gstAmount.toFixed(2);
         document.getElementById('cgst_percentage').value = cgstRate.toFixed(2);
         document.getElementById('cgst_amount').value = cgstAmount.toFixed(2);
         document.getElementById('sgst_percentage').value = sgstRate.toFixed(2);
         document.getElementById('sgst_amount').value = sgstAmount.toFixed(2);
         document.getElementById('igst_percentage').value = igstRate.toFixed(2);
         document.getElementById('igst_amount').value = igstAmount.toFixed(2);
         document.getElementById('total_credit_amount').value = totalCreditAmount.toFixed(2);
         
         // Update hidden fields
         document.getElementById('subtotal_hidden').value = subtotal.toFixed(2);
         document.getElementById('cgst_amount_hidden').value = cgstAmount.toFixed(2);
         document.getElementById('sgst_amount_hidden').value = sgstAmount.toFixed(2);
         document.getElementById('igst_amount_hidden').value = igstAmount.toFixed(2);
         document.getElementById('total_credit_amount_hidden').value = totalCreditAmount.toFixed(2);
     }
    
    // Attach events to existing rows
    document.querySelectorAll('#itemsTable tbody tr').forEach(function(row) {
        attachRowEvents(row);
    });
    
    // Adjustments change
    document.getElementById('adjustments').addEventListener('input', function() {
        recalcTotals();
    });
</script>
@endpush

