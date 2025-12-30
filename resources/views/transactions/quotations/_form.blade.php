@php
    $editing = isset($quotation);
@endphp

<div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
    <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Header Information</h3>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <div>
            <label for="quotation_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Quotation ID</label>
            <input type="text" name="quotation_id" id="quotation_id" 
                   value="{{ old('quotation_id', $editing ? $quotation->quotation_id : '') }}"
                   placeholder="Auto-generated if left empty"
                   readonly
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
            @error('quotation_id')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="customer_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer Name <span style="color: red;">*</span></label>
            <select name="customer_id" id="customer_id" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                <option value="">-- Select Customer --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}"
                        data-contact-name="{{ $customer->contact_name_1 ?? '' }}"
                        data-contact-number="{{ $customer->phone_number ?? '' }}"
                        data-postal-code="{{ $customer->billing_postal_code ?? '' }}"
                        data-address-line-1="{{ $customer->billing_address_line_1 ?? '' }}"
                        data-address-line-2="{{ $customer->billing_address_line_2 ?? '' }}"
                        data-city="{{ $customer->billing_city ?? '' }}"
                        data-state="{{ $customer->billing_state ?? '' }}"
                        {{ old('customer_id', $editing ? $quotation->customer_id : '') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->customer_name }}
                    </option>
                @endforeach
            </select>
            @error('customer_id')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <div>
            <label for="contact_person_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Contact Person Name</label>
            <input type="text" name="contact_person_name" id="contact_person_name"
                   value="{{ old('contact_person_name', $editing ? $quotation->contact_person_name : '') }}"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            @error('contact_person_name')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="contact_number" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Contact Number</label>
            <input type="text" name="contact_number" id="contact_number"
                   value="{{ old('contact_number', $editing ? $quotation->contact_number : '') }}"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            @error('contact_number')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Address Information</h3>
        
        @php
            $states = [
                'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana',
                'Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur',
                'Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu',
                'Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal',
                'Andaman and Nicobar Islands','Chandigarh','Dadra and Nagar Haveli and Daman and Diu',
                'Delhi','Jammu and Kashmir','Ladakh','Lakshadweep','Puducherry'
            ];
        @endphp

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Left Column -->
            <div>
                <div style="margin-bottom: 15px;">
                    <label for="address_line_1" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 1</label>
                    <input type="text" name="address_line_1" id="address_line_1" readonly
                           value="{{ old('address_line_1', $editing ? $quotation->address_line_1 : '') }}"
                           placeholder="Enter address line 1"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
                    @error('address_line_1')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="city" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">City</label>
                    <input type="text" name="city" id="city" readonly
                           value="{{ old('city', $editing ? $quotation->city : '') }}"
                           placeholder="Enter city"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
                    @error('city')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="postal_code" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Postal Code</label>
                    <input type="text" name="postal_code" id="postal_code" readonly
                           value="{{ old('postal_code', $editing ? $quotation->postal_code : '') }}"
                           placeholder="Enter postal code"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
                    @error('postal_code')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <div style="margin-bottom: 15px;">
                    <label for="address_line_2" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 2</label>
                    <input type="text" name="address_line_2" id="address_line_2" readonly
                           value="{{ old('address_line_2', $editing ? $quotation->address_line_2 : '') }}"
                           placeholder="Enter address line 2"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #e9ecef;">
                    @error('address_line_2')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="state" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">State <span style="color: red;">*</span></label>
                    <select name="state" id="state" required
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                        <option value="">Select state</option>
                        @foreach($states as $stateOption)
                            <option value="{{ $stateOption }}" {{ old('state', $editing ? $quotation->state : '') === $stateOption ? 'selected' : '' }}>
                                {{ $stateOption }}
                            </option>
                        @endforeach
                    </select>
                    @error('state')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="country" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Country</label>
                    <select name="country" id="country"
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                        <option value="India" {{ old('country', $editing ? $quotation->country : 'India') === 'India' ? 'selected' : '' }}>India</option>
                        <option value="United States" {{ old('country', $editing ? $quotation->country : '') === 'United States' ? 'selected' : '' }}>United States</option>
                        <option value="United Kingdom" {{ old('country', $editing ? $quotation->country : '') === 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                        <option value="Canada" {{ old('country', $editing ? $quotation->country : '') === 'Canada' ? 'selected' : '' }}>Canada</option>
                        <option value="Australia" {{ old('country', $editing ? $quotation->country : '') === 'Australia' ? 'selected' : '' }}>Australia</option>
                        <option value="Other" {{ old('country', $editing ? $quotation->country : '') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('country')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
    <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Item Details</h3>

    <div style="overflow-x: auto; margin-bottom: 20px;">
        <table id="itemsTable" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 10px; text-align: left;">Product Name</th>
                    <th style="padding: 10px; text-align: left;">Item Description</th>
                    <th style="padding: 10px; text-align: right;">Quantity</th>
                    <th style="padding: 10px; text-align: left;">UOM</th>
                    <th style="padding: 10px; text-align: right;">Price</th>
                    <th style="padding: 10px; text-align: right;">Amount</th>
                    <th style="padding: 10px; text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $oldItems = old('items', $editing ? $quotation->items->toArray() : [['product_id' => '', 'quantity' => '', 'price' => '']]);
                @endphp
                @foreach($oldItems as $index => $item)
                    <tr>
                        <td style="padding: 8px;">
                            <select name="items[{{ $index }}][product_id]" required class="product-select"
                                    style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; background: #fff;">
                                <option value="">-- Select --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                        data-description="{{ $product->description ?? '' }}"
                                        data-uom="{{ $product->unit_of_measure ?? '' }}"
                                        {{ (int)($item['product_id'] ?? 0) === $product->id ? 'selected' : '' }}>
                                        {{ $product->product_name }} ({{ $product->code }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td style="padding: 8px;">
                            <input type="text" name="items[{{ $index }}][item_description]" 
                                   class="item-description" readonly
                                   value="{{ $item['item_description'] ?? '' }}"
                                   style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; background: #e9ecef;">
                        </td>
                        <td style="padding: 8px;">
                            <input type="number" step="0.01" min="0" name="items[{{ $index }}][quantity]"
                                   value="{{ isset($item['quantity']) ? $item['quantity'] : '' }}"
                                   class="item-quantity"
                                   style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; text-align: right;">
                        </td>
                        <td style="padding: 8px;">
                            <input type="text" name="items[{{ $index }}][uom]" 
                                   class="item-uom" readonly
                                   value="{{ $item['uom'] ?? '' }}"
                                   style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; background: #e9ecef;">
                        </td>
                        <td style="padding: 8px;">
                            <input type="number" step="0.01" min="0" name="items[{{ $index }}][price]"
                                   value="{{ $item['price'] ?? '' }}"
                                   class="item-price"
                                   style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; text-align: right;">
                        </td>
                        <td style="padding: 8px;">
                            <input type="text" readonly
                                   class="item-amount"
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

    <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
        <div style="max-width: 300px; width: 100%; background: #f9fafb; padding: 16px 18px; border-radius: 8px; border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between;">
                <span style="font-weight: 700; color: #111827;">Total Amount:</span>
                <span id="total_amount_view" style="font-weight: 700; color: #111827;">0.00</span>
            </div>
        </div>
    </div>
</div>

<div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
    <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Terms and Conditions</h3>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <label for="validity" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Validity</label>
            <input type="text" name="validity" id="validity"
                   value="{{ old('validity', $editing ? $quotation->validity : '') }}"
                   placeholder="e.g., 30 Days from quotation date"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            @error('validity')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="payment_terms" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Payment Terms <span style="color: red;">*</span></label>
            <input type="text" name="payment_terms" id="payment_terms" required
                   value="{{ old('payment_terms', $editing ? $quotation->payment_terms : '') }}"
                   placeholder="e.g., 50% Advance, Balance on Delivery"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            @error('payment_terms')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="taxes" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Taxes <span style="color: red;">*</span></label>
            <input type="text" name="taxes" id="taxes" required
                   value="{{ old('taxes', $editing ? $quotation->taxes : '') }}"
                   placeholder="e.g., GST Extra as applicable"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            @error('taxes')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

@push('scripts')
<script>
    function parseNumber(value) {
        var n = parseFloat(value);
        return isNaN(n) ? 0 : n;
    }

    function recalcRow(row) {
        var qty = parseNumber(row.querySelector('.item-quantity').value);
        var price = parseNumber(row.querySelector('.item-price').value);
        var amount = qty * price;
        row.querySelector('.item-amount').value = amount.toFixed(2);
        recalcTotals();
    }

    function recalcTotals() {
        var rows = document.querySelectorAll('#itemsTable tbody tr');
        var total = 0;

        rows.forEach(function (row) {
            total += parseNumber(row.querySelector('.item-amount').value);
        });

        document.getElementById('total_amount_view').innerText = total.toFixed(2);
    }

    function attachRowEvents(row) {
        // Product select change
        var productSelect = row.querySelector('.product-select');
        if (productSelect) {
            productSelect.addEventListener('change', function() {
                var option = this.options[this.selectedIndex];
                if (option && option.value) {
                    row.querySelector('.item-description').value = option.getAttribute('data-description') || '';
                    row.querySelector('.item-uom').value = option.getAttribute('data-uom') || '';
                } else {
                    row.querySelector('.item-description').value = '';
                    row.querySelector('.item-uom').value = '';
                }
            });
        }

        // Quantity and price change
        ['item-quantity', 'item-price'].forEach(function (cls) {
            var input = row.querySelector('.' + cls);
            if (input) {
                input.addEventListener('input', function () {
                    recalcRow(row);
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
                <select name="items[${index}][product_id]" required class="product-select"
                        style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; background: #fff;">
                    <option value="">-- Select --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                            data-description="{{ $product->description ?? '' }}"
                            data-uom="{{ $product->unit_of_measure ?? '' }}">
                            {{ $product->product_name }} ({{ $product->code }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td style="padding: 8px;">
                <input type="text" name="items[${index}][item_description]" 
                       class="item-description" readonly
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; background: #e9ecef;">
            </td>
            <td style="padding: 8px;">
                <input type="number" step="0.01" min="0" name="items[${index}][quantity]"
                       class="item-quantity"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; text-align: right;">
            </td>
            <td style="padding: 8px;">
                <input type="text" name="items[${index}][uom]" 
                       class="item-uom" readonly
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; background: #e9ecef;">
            </td>
            <td style="padding: 8px;">
                <input type="number" step="0.01" min="0" name="items[${index}][price]"
                       class="item-price"
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; text-align: right;">
            </td>
            <td style="padding: 8px;">
                <input type="text" readonly
                       class="item-amount"
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

    // Customer selection change
    document.getElementById('customer_id').addEventListener('change', function() {
        var option = this.options[this.selectedIndex];
        if (option && option.value) {
            document.getElementById('contact_person_name').value = option.getAttribute('data-contact-name') || '';
            document.getElementById('contact_number').value = option.getAttribute('data-contact-number') || '';
            document.getElementById('postal_code').value = option.getAttribute('data-postal-code') || '';
            document.getElementById('address_line_1').value = option.getAttribute('data-address-line-1') || '';
            document.getElementById('address_line_2').value = option.getAttribute('data-address-line-2') || '';
            document.getElementById('city').value = option.getAttribute('data-city') || '';
            document.getElementById('state').value = option.getAttribute('data-state') || '';
        } else {
            document.getElementById('contact_person_name').value = '';
            document.getElementById('contact_number').value = '';
            document.getElementById('postal_code').value = '';
            document.getElementById('address_line_1').value = '';
            document.getElementById('address_line_2').value = '';
            document.getElementById('city').value = '';
            document.getElementById('state').value = '';
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize item rows
        var rows = document.querySelectorAll('#itemsTable tbody tr');
        rows.forEach(function (row) {
            attachRowEvents(row);
            recalcRow(row);
        });
        
        // Initialize totals
        recalcTotals();
        
        // Auto-populate fields if customer is already selected (when editing)
        var customerSelect = document.getElementById('customer_id');
        if (customerSelect && customerSelect.value) {
            customerSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush

