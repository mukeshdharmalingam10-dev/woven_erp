@php
    $editing = isset($stockTransaction);
    $rawMaterialsData = $rawMaterials->mapWithKeys(function($item) {
        return [$item->id => ['name' => $item->raw_material_name, 'uom' => $item->unit_of_measure]];
    })->toJson();
    $productsData = $products->mapWithKeys(function($item) {
        return [$item->id => ['name' => $item->product_name, 'uom' => $item->unit_of_measure]];
    })->toJson();
    $materialInwardsData = $materialInwards->mapWithKeys(function($item) {
        return [$item->id => $item->inward_number];
    })->toJson();
    $salesInvoicesData = $salesInvoices->mapWithKeys(function($item) {
        return [$item->id => $item->invoice_number];
    })->toJson();
@endphp

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div>
        <label for="transaction_number" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Transaction Number</label>
        <input type="text" name="transaction_number" id="transaction_number"
               value="{{ old('transaction_number', $editing ? $stockTransaction->transaction_number : '') }}"
               placeholder="Auto Generated if left empty"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('transaction_number')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="transaction_date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Transaction Date <span style="color:red">*</span></label>
        <input type="date" name="transaction_date" id="transaction_date" required
               value="{{ old('transaction_date', $editing ? optional($stockTransaction->transaction_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('transaction_date')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="transaction_type" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Transaction Type <span style="color:red">*</span></label>
        <select name="transaction_type" id="transaction_type" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Type --</option>
            <option value="stock_in" {{ old('transaction_type', $editing ? $stockTransaction->transaction_type : '') === 'stock_in' ? 'selected' : '' }}>Stock In</option>
            <option value="stock_out" {{ old('transaction_type', $editing ? $stockTransaction->transaction_type : '') === 'stock_out' ? 'selected' : '' }}>Stock Out</option>
        </select>
        @error('transaction_type')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="item_type" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Item Type <span style="color:red">*</span></label>
        <select name="item_type" id="item_type" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Item Type --</option>
            <option value="raw_material" {{ old('item_type', $editing ? $stockTransaction->item_type : '') === 'raw_material' ? 'selected' : '' }}>Raw Material</option>
            <option value="product" {{ old('item_type', $editing ? $stockTransaction->item_type : '') === 'product' ? 'selected' : '' }}>Product</option>
        </select>
        @error('item_type')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="item_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Item Name <span style="color:red">*</span></label>
        <select name="item_id" id="item_id" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Item --</option>
            @if(old('item_type', $editing ? $stockTransaction->item_type : '') === 'raw_material')
                @foreach($rawMaterials as $material)
                    <option value="{{ $material->id }}" 
                            data-uom="{{ $material->unit_of_measure }}"
                            {{ old('item_id', $editing && $stockTransaction->item_type === 'raw_material' ? $stockTransaction->item_id : '') == $material->id ? 'selected' : '' }}>
                        {{ $material->raw_material_name }}
                    </option>
                @endforeach
            @elseif(old('item_type', $editing ? $stockTransaction->item_type : '') === 'product')
                @foreach($products as $product)
                    <option value="{{ $product->id }}" 
                            data-uom="{{ $product->unit_of_measure }}"
                            {{ old('item_id', $editing && $stockTransaction->item_type === 'product' ? $stockTransaction->item_id : '') == $product->id ? 'selected' : '' }}>
                        {{ $product->product_name }}
                    </option>
                @endforeach
            @endif
        </select>
        @error('item_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="quantity" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Quantity <span style="color:red">*</span></label>
        <input type="number" step="1" min="0" name="quantity" id="quantity" required
               value="{{ old('quantity', $editing ? (int)$stockTransaction->quantity : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('quantity')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="unit_of_measure" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Unit of Measure</label>
        <input type="text" name="unit_of_measure" id="unit_of_measure" readonly
               value="{{ old('unit_of_measure', $editing ? $stockTransaction->unit_of_measure : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5;">
        @error('unit_of_measure')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="source_document_type" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Source Document Type</label>
        <select name="source_document_type" id="source_document_type"
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Document Type --</option>
            <option value="material_inward" {{ old('source_document_type', $editing ? $stockTransaction->source_document_type : '') === 'material_inward' ? 'selected' : '' }}>Material Inward</option>
            <option value="sales_invoice" {{ old('source_document_type', $editing ? $stockTransaction->source_document_type : '') === 'sales_invoice' ? 'selected' : '' }}>Sales Invoice</option>
        </select>
        @error('source_document_type')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="source_document_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Source Document</label>
        <select name="source_document_id" id="source_document_id"
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Document --</option>
            @if(old('source_document_type', $editing ? $stockTransaction->source_document_type : '') === 'material_inward')
                @foreach($materialInwards as $inward)
                    <option value="{{ $inward->id }}" 
                            data-doc-number="{{ $inward->inward_number }}"
                            {{ old('source_document_id', $editing && $stockTransaction->source_document_type === 'material_inward' ? $stockTransaction->source_document_id : '') == $inward->id ? 'selected' : '' }}>
                        {{ $inward->inward_number }}
                    </option>
                @endforeach
            @elseif(old('source_document_type', $editing ? $stockTransaction->source_document_type : '') === 'sales_invoice')
                @foreach($salesInvoices as $invoice)
                    <option value="{{ $invoice->id }}" 
                            data-doc-number="{{ $invoice->invoice_number }}"
                            {{ old('source_document_id', $editing && $stockTransaction->source_document_type === 'sales_invoice' ? $stockTransaction->source_document_id : '') == $invoice->id ? 'selected' : '' }}>
                        {{ $invoice->invoice_number }}
                    </option>
                @endforeach
            @endif
        </select>
        @error('source_document_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="source_document_number" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Source Document Number</label>
        <input type="text" name="source_document_number" id="source_document_number" readonly
               value="{{ old('source_document_number', $editing ? $stockTransaction->source_document_number : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5;">
        @error('source_document_number')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('scripts')
<script>
    var rawMaterialsData = {!! $rawMaterialsData !!};
    var productsData = {!! $productsData !!};
    var materialInwardsData = {!! $materialInwardsData !!};
    var salesInvoicesData = {!! $salesInvoicesData !!};

    document.getElementById('item_type').addEventListener('change', function() {
        var itemType = this.value;
        var itemSelect = document.getElementById('item_id');
        var uomInput = document.getElementById('unit_of_measure');
        
        itemSelect.innerHTML = '<option value="">-- Select Item --</option>';
        uomInput.value = '';

        if (itemType === 'raw_material') {
            @foreach($rawMaterials as $material)
                var option = document.createElement('option');
                option.value = '{{ $material->id }}';
                option.textContent = '{{ $material->raw_material_name }}';
                option.setAttribute('data-uom', '{{ $material->unit_of_measure }}');
                itemSelect.appendChild(option);
            @endforeach
        } else if (itemType === 'product') {
            @foreach($products as $product)
                var option = document.createElement('option');
                option.value = '{{ $product->id }}';
                option.textContent = '{{ $product->product_name }}';
                option.setAttribute('data-uom', '{{ $product->unit_of_measure }}');
                itemSelect.appendChild(option);
            @endforeach
        }
    });

    document.getElementById('item_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var uom = selectedOption ? selectedOption.getAttribute('data-uom') : '';
        document.getElementById('unit_of_measure').value = uom || '';
    });

    document.getElementById('source_document_type').addEventListener('change', function() {
        var docType = this.value;
        var docSelect = document.getElementById('source_document_id');
        var docNumberInput = document.getElementById('source_document_number');
        
        docSelect.innerHTML = '<option value="">-- Select Document --</option>';
        docNumberInput.value = '';

        if (docType === 'material_inward') {
            @foreach($materialInwards as $inward)
                var option = document.createElement('option');
                option.value = '{{ $inward->id }}';
                option.textContent = '{{ $inward->inward_number }}';
                option.setAttribute('data-doc-number', '{{ $inward->inward_number }}');
                docSelect.appendChild(option);
            @endforeach
        } else if (docType === 'sales_invoice') {
            @foreach($salesInvoices as $invoice)
                var option = document.createElement('option');
                option.value = '{{ $invoice->id }}';
                option.textContent = '{{ $invoice->invoice_number }}';
                option.setAttribute('data-doc-number', '{{ $invoice->invoice_number }}');
                docSelect.appendChild(option);
            @endforeach
        }
    });

    document.getElementById('source_document_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var docNumber = selectedOption ? selectedOption.getAttribute('data-doc-number') : '';
        document.getElementById('source_document_number').value = docNumber || '';
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        var itemIdSelect = document.getElementById('item_id');
        if (itemIdSelect.value) {
            var selectedOption = itemIdSelect.options[itemIdSelect.selectedIndex];
            var uom = selectedOption ? selectedOption.getAttribute('data-uom') : '';
            document.getElementById('unit_of_measure').value = uom || '';
        }

        var sourceDocSelect = document.getElementById('source_document_id');
        if (sourceDocSelect.value) {
            var selectedOption = sourceDocSelect.options[sourceDocSelect.selectedIndex];
            var docNumber = selectedOption ? selectedOption.getAttribute('data-doc-number') : '';
            document.getElementById('source_document_number').value = docNumber || '';
        }
    });
</script>
@endpush

