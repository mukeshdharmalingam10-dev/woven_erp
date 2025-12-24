@php
    $editing = isset($purchaseOrder);
@endphp

<div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
    <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Information</h3>
    
    <!-- First Row: Purchase Order Number (full width, auto-generated) -->
    <div style="margin-bottom: 20px;">
        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Purchase Order Number</label>
        <input type="text" class="form-control" value="{{ $editing ? $purchaseOrder->po_number : 'Auto-generated (PUR001, PUR002, etc.)' }}" disabled
            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
    </div>

    <!-- Second Row: Supplier Name and Purchase Date (side by side) -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <div>
            <label for="supplier_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Supplier Name <span style="color: red;">*</span></label>
            <select name="supplier_id" id="supplier_id" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                <option value="">-- Select Supplier --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}"
                        data-state="{{ $supplier->state ?? '' }}"
                        {{ old('supplier_id', $editing ? $purchaseOrder->supplier_id : '') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->supplier_name }} ({{ $supplier->code }})
                    </option>
                @endforeach
            </select>
            @error('supplier_id')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="purchase_date" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Purchase Date <span style="color: red;">*</span></label>
            <input type="date" name="purchase_date" id="purchase_date" required
                   value="{{ old('purchase_date', $editing ? optional($purchaseOrder->purchase_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            @error('purchase_date')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Third Row: Delivery Date and GST Classification (side by side) -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <label for="delivery_date" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Delivery Date</label>
            <input type="date" name="delivery_date" id="delivery_date"
                   value="{{ old('delivery_date', $editing && $purchaseOrder->delivery_date ? $purchaseOrder->delivery_date->format('Y-m-d') : '') }}"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            @error('delivery_date')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">GST Classification</label>
            <input type="text" id="gst_classification_view" disabled
                   value="@if($editing && $purchaseOrder->gst_classification) {{ $purchaseOrder->gst_classification === 'CGST_SGST' ? 'CGST + SGST' : 'IGST' }} @else Auto-select based on supplier and company location @endif"
                   placeholder="Auto-select based on supplier and company location"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
        </div>
    </div>
</div>

<div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
    <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Raw Materials</h3>

<div style="overflow-x: auto; margin-bottom: 20px;">
    <table id="itemsTable" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 10px; text-align: left;">Raw Material</th>
                <th style="padding: 10px; text-align: right;">Quantity</th>
                <th style="padding: 10px; text-align: right;">Unit Price</th>
                <th style="padding: 10px; text-align: right;">Total Amount</th>
                <th style="padding: 10px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $oldItems = old('items', $editing ? $purchaseOrder->items->toArray() : [['raw_material_id' => '', 'quantity' => '', 'unit_price' => '']]);
            @endphp
            @foreach($oldItems as $index => $item)
                <tr>
                    <td style="padding: 8px;">
                        <select name="items[{{ $index }}][raw_material_id]" required
                                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; background: #fff;">
                            <option value="">-- Select --</option>
                            @foreach($rawMaterials as $rm)
                                <option value="{{ $rm->id }}"
                                    {{ (int)($item['raw_material_id'] ?? $item['raw_material_id'] ?? 0) === $rm->id ? 'selected' : '' }}>
                                    {{ $rm->raw_material_name }} ({{ $rm->code }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td style="padding: 8px;">
                        <input type="number" step="1" min="0" name="items[{{ $index }}][quantity]"
                               value="{{ isset($item['quantity']) ? (int)$item['quantity'] : '' }}"
                               class="item-quantity"
                               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; text-align: right;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_price]"
                               value="{{ $item['unit_price'] ?? '' }}"
                               class="item-unit-price"
                               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; text-align: right;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="text" readonly
                               class="item-total-amount"
                               value=""
                               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; text-align: right; background: #e9ecef;">
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

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; margin-bottom: 20px;">
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Tax Type</h3>
        <div style="display: flex; gap: 20px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="radio" name="tax_type" value="CGST_SGST" id="tax_type_cgst_sgst" 
                       {{ old('tax_type', $editing && $purchaseOrder->gst_classification ? $purchaseOrder->gst_classification : 'CGST_SGST') === 'CGST_SGST' ? 'checked' : '' }}
                       style="width: 18px; height: 18px; cursor: pointer;">
                <span style="font-weight: 500; color: #333;">CGST and SGST</span>
            </label>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="radio" name="tax_type" value="IGST" id="tax_type_igst"
                       {{ old('tax_type', $editing && $purchaseOrder->gst_classification ? $purchaseOrder->gst_classification : 'CGST_SGST') === 'IGST' ? 'checked' : '' }}
                       style="width: 18px; height: 18px; cursor: pointer;">
                <span style="font-weight: 500; color: #333;">IGST</span>
            </label>
        </div>
        <input type="hidden" name="gst_classification" id="gst_classification">
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Amount</h3>
        
        <div style="margin-bottom: 15px;">
            <label for="gst_percentage" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">GST *</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="number" step="0.01" min="0" name="gst_percentage" id="gst_percentage" readonly
                       value="{{ old('gst_percentage', $editing && $purchaseOrder->gst_percentage_overall ? $purchaseOrder->gst_percentage_overall : '18') }}"
                       placeholder="Enter In (%)"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5;">
                <input type="text" readonly id="gst_amount"
                       value="0"
                       placeholder="GST Amount"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
            </div>
        </div>

        <div id="cgst_sgst_section" style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">CGST :</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="text" readonly id="cgst_percentage" value="0"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
                <input type="text" readonly id="cgst_amount" value="0"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
            </div>
            <label style="display: block; margin-bottom: 6px; margin-top: 10px; font-weight: 600; color: #333;">SGST :</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="text" readonly id="sgst_percentage" value="0"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
                <input type="text" readonly id="sgst_amount" value="0"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
            </div>
        </div>

        <div id="igst_section" style="margin-bottom: 15px; display: none;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">IGST :</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="text" readonly id="igst_percentage" value="0"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
                <input type="text" readonly id="igst_amount" value="0"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
            </div>
        </div>

        <div>
            <label for="net_amount" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Net Amount :</label>
            <input type="text" readonly id="net_amount" value="0"
                   style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; text-align: right;">
        </div>
    </div>
</div>

<div style="display: flex; justify-content: flex-end; margin-top: 20px;">
    <div style="max-width: 360px; width: 100%; background: #f9fafb; padding: 16px 18px; border-radius: 8px; border: 1px solid #e5e7eb;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="font-weight: 500; color: #374151;">Total Raw Material Amount:</span>
            <span id="total_raw_material_amount_view" style="font-weight: 600; color: #111827;">0.00</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="font-weight: 500; color: #374151;">Total GST Amount:</span>
            <span id="total_gst_amount_view" style="font-weight: 600; color: #111827;">0.00</span>
        </div>
        <div style="height: 1px; background: #e5e7eb; margin: 8px 0 10px;"></div>
        <div style="display: flex; justify-content: space-between;">
            <span style="font-weight: 700; color: #111827;">Grand Total:</span>
            <span id="grand_total_view" style="font-weight: 700; color: #111827;">0.00</span>
        </div>
    </div>
</div>

<input type="hidden" name="total_raw_material_amount" id="total_raw_material_amount">
<input type="hidden" name="total_gst_amount" id="total_gst_amount">
<input type="hidden" name="grand_total" id="grand_total">

@push('scripts')
<script>
    // Company state from server
    var companyState = @json($companyInfo->state ?? '');

    function updateGstClassification() {
        var supplierSelect = document.getElementById('supplier_id');
        var gstClassificationView = document.getElementById('gst_classification_view');
        
        if (!supplierSelect || !gstClassificationView) return;
        
        var selectedOption = supplierSelect.options[supplierSelect.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            gstClassificationView.value = 'Auto-select based on supplier and company location';
            return;
        }
        
        var supplierState = selectedOption.getAttribute('data-state') || '';
        
        if (!companyState || !supplierState) {
            gstClassificationView.value = 'Unable to determine (missing state information)';
            return;
        }
        
        // Compare states (case-insensitive)
        if (companyState.toLowerCase() === supplierState.toLowerCase()) {
            gstClassificationView.value = 'CGST + SGST';
        } else {
            gstClassificationView.value = 'IGST';
        }
    }

    function parseNumber(value) {
        var n = parseFloat(value);
        return isNaN(n) ? 0 : n;
    }

    function recalcRow(row) {
        var qty = parseNumber(row.querySelector('.item-quantity').value);
        var price = parseNumber(row.querySelector('.item-unit-price').value);
        var total = qty * price;
        row.querySelector('.item-total-amount').value = total.toFixed(2);
    }

    function recalcTotals() {
        var rows = document.querySelectorAll('#itemsTable tbody tr');
        var totalRaw = 0;

        rows.forEach(function (row) {
            totalRaw += parseNumber(row.querySelector('.item-total-amount').value);
        });

        var gstPercentage = parseNumber(document.getElementById('gst_percentage').value);
        var gstAmount = gstPercentage > 0 ? (totalRaw * gstPercentage) / 100 : 0;
        
        var taxTypeElement = document.querySelector('input[name="tax_type"]:checked');
        var taxType = taxTypeElement ? taxTypeElement.value : 'CGST_SGST';
        document.getElementById('gst_classification').value = taxType;

        var cgstAmount = 0, sgstAmount = 0, igstAmount = 0;
        var cgstPercentage = 0, sgstPercentage = 0, igstPercentage = 0;

        if (taxType === 'CGST_SGST') {
            cgstPercentage = gstPercentage / 2;
            sgstPercentage = gstPercentage / 2;
            cgstAmount = gstAmount / 2;
            sgstAmount = gstAmount / 2;
            document.getElementById('cgst_sgst_section').style.display = 'block';
            document.getElementById('igst_section').style.display = 'none';
        } else {
            igstPercentage = gstPercentage;
            igstAmount = gstAmount;
            document.getElementById('cgst_sgst_section').style.display = 'none';
            document.getElementById('igst_section').style.display = 'block';
        }

        var grandTotal = totalRaw + gstAmount;

        document.getElementById('total_raw_material_amount_view').innerText = totalRaw.toFixed(2);
        document.getElementById('total_gst_amount_view').innerText = gstAmount.toFixed(2);
        document.getElementById('grand_total_view').innerText = grandTotal.toFixed(2);
        document.getElementById('gst_amount').value = gstAmount.toFixed(2);
        document.getElementById('cgst_percentage').value = cgstPercentage.toFixed(2);
        document.getElementById('cgst_amount').value = cgstAmount.toFixed(2);
        document.getElementById('sgst_percentage').value = sgstPercentage.toFixed(2);
        document.getElementById('sgst_amount').value = sgstAmount.toFixed(2);
        document.getElementById('igst_percentage').value = igstPercentage.toFixed(2);
        document.getElementById('igst_amount').value = igstAmount.toFixed(2);
        document.getElementById('net_amount').value = grandTotal.toFixed(2);

        document.getElementById('total_raw_material_amount').value = totalRaw.toFixed(2);
        document.getElementById('total_gst_amount').value = gstAmount.toFixed(2);
        document.getElementById('grand_total').value = grandTotal.toFixed(2);
    }

    function attachRowEvents(row) {
        ['item-quantity', 'item-unit-price'].forEach(function (cls) {
            var input = row.querySelector('.' + cls);
            if (input) {
                input.addEventListener('input', function () {
                    recalcRow(row);
                    recalcTotals();
                });
            }
        });
    }

    function addItemRow() {
        var tbody = document.querySelector('#itemsTable tbody');
        var index = tbody.querySelectorAll('tr').length;

        var tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="padding: 8px;">
                <select name="items[${index}][raw_material_id]" required
                        style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; background: #fff;">
                    <option value="">-- Select --</option>
                    @foreach($rawMaterials as $rm)
                        <option value="{{ $rm->id }}">{{ $rm->raw_material_name }} ({{ $rm->code }})</option>
                    @endforeach
                </select>
            </td>
            <td style="padding: 8px;">
                <input type="number" step="1" min="0" name="items[${index}][quantity]"
                       class="item-quantity"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; text-align: right;">
            </td>
            <td style="padding: 8px;">
                <input type="number" step="0.01" min="0" name="items[${index}][unit_price]"
                       class="item-unit-price"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; text-align: right;">
            </td>
            <td style="padding: 8px;">
                <input type="text" readonly
                       class="item-total-amount"
                       value=""
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; text-align: right; background: #e9ecef;">
            </td>
            <td style="padding: 8px; text-align: center;">
                <button type="button" class="btn-remove-row" onclick="removeItemRow(this)"
                        style="padding: 6px 10px; background: #dc3545; color: #fff; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        attachRowEvents(tr);
    }

    function removeItemRow(button) {
        var tbody = document.querySelector('#itemsTable tbody');
        if (tbody.querySelectorAll('tr').length <= 1) {
            alert('At least one item is required.');
            return;
        }
        var row = button.closest('tr');
        if (row) {
            row.remove();
        }
        recalcTotals();
    }

    // Handle tax type change
    document.querySelectorAll('input[name="tax_type"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            recalcTotals();
        });
    });

    // Handle GST percentage change
    document.getElementById('gst_percentage').addEventListener('input', function() {
        recalcTotals();
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize GST percentage to 18 if empty
        var gstPercentageField = document.getElementById('gst_percentage');
        if (!gstPercentageField.value || gstPercentageField.value === '') {
            gstPercentageField.value = '18';
        }
        
        // Initialize GST Classification
        updateGstClassification();
        
        // Initialize tax type based on existing data
        @if($editing && $purchaseOrder->gst_classification)
            var existingGstType = '{{ $purchaseOrder->gst_classification }}';
            if (existingGstType === 'CGST_SGST') {
                document.getElementById('tax_type_cgst_sgst').checked = true;
            } else if (existingGstType === 'IGST') {
                document.getElementById('tax_type_igst').checked = true;
            }
        @endif
        
        // Update GST Classification when supplier changes
        var supplierSelect = document.getElementById('supplier_id');
        if (supplierSelect) {
            supplierSelect.addEventListener('change', function() {
                updateGstClassification();
                // Auto-select tax type based on classification
                var gstClassificationView = document.getElementById('gst_classification_view');
                if (gstClassificationView) {
                    var classification = gstClassificationView.value;
                    if (classification.includes('CGST')) {
                        document.getElementById('tax_type_cgst_sgst').checked = true;
                    } else if (classification.includes('IGST')) {
                        document.getElementById('tax_type_igst').checked = true;
                    }
                    recalcTotals();
                }
            });
        }
        
        // Initialize item rows
        var rows = document.querySelectorAll('#itemsTable tbody tr');
        rows.forEach(function (row) {
            attachRowEvents(row);
            recalcRow(row);
        });
        
        // Initialize totals with existing data
        recalcTotals();
    });
</script>
@endpush


