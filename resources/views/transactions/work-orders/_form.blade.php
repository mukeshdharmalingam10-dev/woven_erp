@php
    $editing = isset($workOrder);
@endphp

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div>
        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Work Order Number</label>
        <input type="text" value="{{ $editing ? $workOrder->work_order_number : 'Auto-generated (WON001, WON002, etc.)' }}" disabled
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5;">
    </div>

    <div>
        <label for="work_order_date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Work Order Date <span style="color:red">*</span></label>
        <input type="date" name="work_order_date" id="work_order_date" required
               value="{{ old('work_order_date', $editing ? optional($workOrder->work_order_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('work_order_date')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="customer_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Customer Name <span style="color:red">*</span></label>
        <select name="customer_id" id="customer_id" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Customer --</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}"
                    {{ old('customer_id', $editing ? $workOrder->customer_id : '') == $customer->id ? 'selected' : '' }}>
                    {{ $customer->customer_name }} ({{ $customer->code }})
                </option>
            @endforeach
        </select>
        @error('customer_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="product_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Product to be Manufactured <span style="color:red">*</span></label>
        <select name="product_id" id="product_id" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Product --</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}"
                    {{ old('product_id', $editing ? $workOrder->product_id : '') == $product->id ? 'selected' : '' }}>
                    {{ $product->product_name }} ({{ $product->code }})
                </option>
            @endforeach
        </select>
        @error('product_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="quantity_to_produce" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Quantity to be Produced <span style="color:red">*</span></label>
        <input type="number" step="1" min="0" name="quantity_to_produce" id="quantity_to_produce" required
               value="{{ old('quantity_to_produce', $editing ? (int)$workOrder->quantity_to_produce : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('quantity_to_produce')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="per_kg_weight" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Per Kg Weight</label>
        <input type="number" step="0.001" min="0" name="per_kg_weight" id="per_kg_weight"
               value="{{ old('per_kg_weight', $editing && $workOrder->per_kg_weight !== null ? $workOrder->per_kg_weight : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        <small style="color: #666; font-size: 12px; display: block; margin-top: 4px;">Optional: specify weight per kg for this work order.</small>
        @error('per_kg_weight')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    @if($editing)
    <div>
        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Work Order Status</label>
        <input type="text" value="{{ ucfirst($workOrder->status) }}" disabled
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5;">
        <small style="color: #666; font-size: 12px; display: block; margin-top: 4px;">
            Status will automatically change to "Completed" when production quantity matches the work order quantity.
        </small>
    </div>
    @endif
</div>


