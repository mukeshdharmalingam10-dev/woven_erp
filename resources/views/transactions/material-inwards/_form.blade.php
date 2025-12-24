@php
    $editing = isset($materialInward);
@endphp

<div style="margin-bottom: 25px;">
    <div style="margin-bottom: 20px;">
        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Inward Number</label>
        <input type="text" value="{{ $editing ? $materialInward->inward_number : 'Auto Generated' }}" disabled
               style="width: 100%; max-width: 300px; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5;">
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
        <div>
            <label for="received_date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Received Date <span style="color:red">*</span></label>
            <input type="date" name="received_date" id="received_date" required
                   value="{{ old('received_date', $editing ? optional($materialInward->received_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                   style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            @error('received_date')
                <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="supplier_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Supplier <span style="color:red">*</span></label>
            <select name="supplier_id" id="supplier_id" required
                    style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                <option value="">-- Select Supplier --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}"
                        {{ old('supplier_id', $editing ? $materialInward->supplier_id : '') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->supplier_name }} ({{ $supplier->code }})
                    </option>
                @endforeach
            </select>
            @error('supplier_id')
                <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="purchase_order_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Purchase Order</label>
            <select name="purchase_order_id" id="purchase_order_id"
                    style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                <option value="">-- Select Purchase Order --</option>
                @foreach($purchaseOrders as $purchaseOrder)
                    <option value="{{ $purchaseOrder->id }}"
                        {{ old('purchase_order_id', $editing ? $materialInward->purchase_order_id : '') == $purchaseOrder->id ? 'selected' : '' }}>
                        {{ $purchaseOrder->po_number }}
                    </option>
                @endforeach
            </select>
            @error('purchase_order_id')
                <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<h3 style="margin-bottom: 12px; font-size: 18px; color: #333;">Material Items</h3>

<div style="overflow-x: auto; margin-bottom: 20px;">
    <table id="itemsTable" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 10px; text-align: left;">Material</th>
                <th style="padding: 10px; text-align: right;">Qty Received</th>
                <th style="padding: 10px; text-align: left;">UOM</th>
                <th style="padding: 10px; text-align: right;">Unit Price</th>
                <th style="padding: 10px; text-align: right;">Total Amount</th>
                <th style="padding: 10px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $oldItems = old('items', $editing ? $materialInward->items->toArray() : [['raw_material_id' => '', 'quantity_received' => '', 'unit_of_measure' => '', 'unit_price' => '']]);
                $rawMaterialsJson = $rawMaterials->map(function($rm) {
                    return [
                        'id' => $rm->id,
                        'unit_of_measure' => $rm->unit_of_measure,
                        'price_per_unit' => $rm->price_per_unit,
                    ];
                })->values()->toJson();
            @endphp
            @foreach($oldItems as $index => $item)
                <tr>
                    <td style="padding: 8px;">
                        <select name="items[{{ $index }}][raw_material_id]" class="item-raw-material" required
                                style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                            <option value="">-- Select --</option>
                            @foreach($rawMaterials as $rm)
                                <option value="{{ $rm->id }}"
                                    {{ (int)($item['raw_material_id'] ?? 0) === $rm->id ? 'selected' : '' }}>
                                    {{ $rm->raw_material_name }} ({{ $rm->code }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td style="padding: 8px;">
                        <input type="number" step="1" min="0" name="items[{{ $index }}][quantity_received]"
                               value="{{ isset($item['quantity_received']) ? (int)$item['quantity_received'] : '' }}"
                               class="item-quantity"
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="text" name="items[{{ $index }}][unit_of_measure]"
                               value="{{ $item['unit_of_measure'] ?? '' }}"
                               class="item-uom"
                               readonly
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; background:#f5f5f5;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_price]"
                               value="{{ $item['unit_price'] ?? '' }}"
                               class="item-unit-price"
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="text" readonly
                               class="item-total-amount"
                               value="{{ isset($item['quantity_received'], $item['unit_price']) ? number_format((float)$item['quantity_received'] * (float)$item['unit_price'], 2) : '' }}"
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right; background: #f5f5f5;">
                    </td>
                    <td style="padding: 8px; text-align: center;">
                        <button type="button" onclick="removeItemRow(this)"
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

<div style="display: flex; justify-content: space-between; gap: 20px; margin-top: 20px; align-items: flex-start; flex-wrap: wrap;">
    <div style="flex: 1; min-width: 260px;">
        <label for="remarks" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Remarks</label>
        <textarea name="remarks" id="remarks" rows="3"
                  style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">{{ old('remarks', $editing ? $materialInward->remarks : '') }}</textarea>
        @error('remarks')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div style="max-width: 320px; width: 100%; background: #f9fafb; padding: 16px 18px; border-radius: 8px; border: 1px solid #e5e7eb;">
        <div style="display: flex; justify-content: space-between;">
            <span style="font-weight: 700; color: #111827;">Total Amount:</span>
            <span id="grand_total_view" style="font-weight: 700; color: #111827;">0.00</span>
        </div>
    </div>
</div>

<input type="hidden" name="total_amount" id="grand_total">

@push('scripts')
<script>
    const rawMaterialsLookup = {!! $rawMaterialsJson !!};

    function findRawMaterial(id) {
        return rawMaterialsLookup.find(rm => rm.id === parseInt(id));
    }

    function parseNumber(value) {
        var n = parseFloat(value);
        return isNaN(n) ? 0 : n;
    }

    function recalcRow(row) {
        var qty = parseNumber(row.querySelector('.item-quantity').value);
        var price = parseNumber(row.querySelector('.item-unit-price').value);
        var total = qty * price;
        row.querySelector('.item-total-amount').value = total ? total.toFixed(2) : '';
    }

    function recalcTotals() {
        var rows = document.querySelectorAll('#itemsTable tbody tr');
        var total = 0;
        rows.forEach(function (row) {
            total += parseNumber(row.querySelector('.item-total-amount').value);
        });
        document.getElementById('grand_total_view').innerText = total.toFixed(2);
        document.getElementById('grand_total').value = total.toFixed(2);
    }

    function attachRowEvents(row) {
        var materialSelect = row.querySelector('.item-raw-material');
        var uomInput = row.querySelector('.item-uom');
        var priceInput = row.querySelector('.item-unit-price');

        if (materialSelect) {
            materialSelect.addEventListener('change', function () {
                var rm = findRawMaterial(materialSelect.value);
                if (rm) {
                    uomInput.value = rm.unit_of_measure || '';
                    if (!priceInput.value) {
                        priceInput.value = rm.price_per_unit || '';
                    }
                } else {
                    uomInput.value = '';
                }
                recalcRow(row);
                recalcTotals();
            });
        }

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
                <select name="items[${index}][raw_material_id]" class="item-raw-material" required
                        style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                    <option value="">-- Select --</option>
                    @foreach($rawMaterials as $rm)
                        <option value="{{ $rm->id }}">{{ $rm->raw_material_name }} ({{ $rm->code }})</option>
                    @endforeach
                </select>
            </td>
            <td style="padding: 8px;">
                <input type="number" step="1" min="0" name="items[${index}][quantity_received]"
                       class="item-quantity"
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
            </td>
            <td style="padding: 8px;">
                <input type="text" name="items[${index}][unit_of_measure]"
                       class="item-uom"
                       readonly
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; background:#f5f5f5;">
            </td>
            <td style="padding: 8px;">
                <input type="number" step="0.01" min="0" name="items[${index}][unit_price]"
                       class="item-unit-price"
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
            </td>
            <td style="padding: 8px;">
                <input type="text" readonly
                       class="item-total-amount"
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right; background: #f5f5f5;">
            </td>
            <td style="padding: 8px; text-align: center;">
                <button type="button" onclick="removeItemRow(this)"
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

    document.addEventListener('DOMContentLoaded', function () {
        var rows = document.querySelectorAll('#itemsTable tbody tr');
        rows.forEach(function (row) {
            attachRowEvents(row);
            recalcRow(row);
        });
        recalcTotals();
    });
</script>
@endpush


