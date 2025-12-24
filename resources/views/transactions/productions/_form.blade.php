@php
    $editing = isset($production);
@endphp

<div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
    <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Production Information</h3>
    
    <!-- First Row: Work Order Number (full width) -->
    <div style="margin-bottom: 20px;">
        <label for="work_order_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Work Order Number</label>
        <select name="work_order_id" id="work_order_id"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
            <option value="">-- Select Work Order --</option>
            @foreach($workOrders as $wo)
                <option value="{{ $wo->id }}"
                    {{ old('work_order_id', $editing ? $production->work_order_id : optional($selectedWorkOrder ?? null)->id) == $wo->id ? 'selected' : '' }}>
                    {{ $wo->work_order_number }}
                </option>
            @endforeach
        </select>
        @error('work_order_id')
            <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
        @enderror
    </div>

    <!-- Second Row: Product Name and Produced Quantity (side by side) -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <div>
            <label for="product_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Product Name <span style="color: red;">*</span></label>
            <select name="product_id" id="product_id" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                <option value="">-- Select Product --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}"
                        {{ old('product_id', $editing ? $production->product_id : optional($selectedWorkOrder ?? null)->product_id) == $product->id ? 'selected' : '' }}>
                        {{ $product->product_name }} ({{ $product->code }})
                    </option>
                @endforeach
            </select>
            @error('product_id')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="produced_quantity" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Produced Quantity <span style="color: red;">*</span></label>
            <input type="number" step="1" min="0" name="produced_quantity" id="produced_quantity" required
                   value="{{ old('produced_quantity', $editing ? (int)$production->produced_quantity : '') }}"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            @error('produced_quantity')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Third Row: Weight of 1 Bag/Unit and Total Weight Produced (side by side) -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <label for="weight_per_unit" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Weight of 1 Bag/Unit <span style="color: red;">*</span></label>
            <input type="number" step="0.01" min="0" name="weight_per_unit" id="weight_per_unit" required
                   value="{{ old('weight_per_unit') !== null ? old('weight_per_unit') : ($editing ? number_format($production->weight_per_unit, 2, '.', '') : '') }}"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            @error('weight_per_unit')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Total Weight Produced</label>
            <input type="text" id="total_weight_view" disabled
                   value="{{ $editing ? number_format($production->total_weight, 0) : '' }}"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
        </div>
    </div>
</div>

<div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
    <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Additional Information</h3>
    
    <div style="margin-bottom: 20px;">
        <label for="remarks" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Remarks</label>
        <textarea name="remarks" id="remarks" rows="3"
                  style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('remarks', $editing ? $production->remarks : '') }}</textarea>
        @error('remarks')
            <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
        @enderror
    </div>
</div>

<input type="hidden" name="total_weight" id="total_weight">

@push('scripts')
<script>
    function parseNumber(value) {
        var n = parseFloat(value);
        return isNaN(n) ? 0 : n;
    }

    function recalcTotalWeight() {
        var qty = parseNumber(document.getElementById('produced_quantity').value);
        var wpu = parseNumber(document.getElementById('weight_per_unit').value);
        var total = qty * wpu;
        if (!isNaN(total) && total > 0) {
            var roundedTotal = Math.round(total);
            document.getElementById('total_weight_view').value = roundedTotal;
            document.getElementById('total_weight').value = roundedTotal;
        } else {
            document.getElementById('total_weight_view').value = '';
            document.getElementById('total_weight').value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var qtyInput = document.getElementById('produced_quantity');
        var wpuInput = document.getElementById('weight_per_unit');

        if (qtyInput) qtyInput.addEventListener('input', recalcTotalWeight);
        if (wpuInput) wpuInput.addEventListener('input', recalcTotalWeight);

        recalcTotalWeight();
    });
</script>
@endpush


