@php
    $editing = isset($salesInvoice);
@endphp

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div>
        <label for="invoice_number" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Invoice Number</label>
        <input type="text" name="invoice_number" id="invoice_number"
               value="{{ old('invoice_number', $editing ? $salesInvoice->invoice_number : '') }}"
               placeholder="Auto Generated if left empty"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('invoice_number')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="invoice_date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Invoice Date <span style="color:red">*</span></label>
        <input type="date" name="invoice_date" id="invoice_date" required
               value="{{ old('invoice_date', $editing ? optional($salesInvoice->invoice_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('invoice_date')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="customer_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Customer Name <span style="color:red">*</span></label>
        <select name="customer_id" id="customer_id" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Customer --</option>
            @foreach($customers as $customer)
                @php
                    $billingParts = [];
                    if ($customer->billing_address_line_1) $billingParts[] = $customer->billing_address_line_1;
                    if ($customer->billing_address_line_2) $billingParts[] = $customer->billing_address_line_2;
                    if ($customer->billing_city) $billingParts[] = $customer->billing_city;
                    if ($customer->billing_state) $billingParts[] = $customer->billing_state;
                    if ($customer->billing_postal_code) $billingParts[] = $customer->billing_postal_code;
                    if ($customer->billing_country) $billingParts[] = $customer->billing_country;
                    $billingAddress = implode(', ', $billingParts);
                    
                    $shippingParts = [];
                    if ($customer->shipping_address_line_1) $shippingParts[] = $customer->shipping_address_line_1;
                    if ($customer->shipping_address_line_2) $shippingParts[] = $customer->shipping_address_line_2;
                    if ($customer->shipping_city) $shippingParts[] = $customer->shipping_city;
                    if ($customer->shipping_state) $shippingParts[] = $customer->shipping_state;
                    if ($customer->shipping_postal_code) $shippingParts[] = $customer->shipping_postal_code;
                    if ($customer->shipping_country) $shippingParts[] = $customer->shipping_country;
                    $shippingAddress = implode(', ', $shippingParts);
                @endphp
                <option value="{{ $customer->id }}"
                    data-billing="{{ htmlspecialchars($billingAddress, ENT_QUOTES) }}"
                    data-shipping="{{ htmlspecialchars($shippingAddress, ENT_QUOTES) }}"
                    data-billing-state="{{ $customer->billing_state ?? '' }}"
                    data-billing-address-line-1="{{ $customer->billing_address_line_1 ?? '' }}"
                    data-billing-address-line-2="{{ $customer->billing_address_line_2 ?? '' }}"
                    data-billing-city="{{ $customer->billing_city ?? '' }}"
                    data-billing-state-value="{{ $customer->billing_state ?? '' }}"
                    data-billing-postal-code="{{ $customer->billing_postal_code ?? '' }}"
                    data-billing-country="{{ $customer->billing_country ?? 'India' }}"
                    data-shipping-address-line-1="{{ $customer->shipping_address_line_1 ?? '' }}"
                    data-shipping-address-line-2="{{ $customer->shipping_address_line_2 ?? '' }}"
                    data-shipping-city="{{ $customer->shipping_city ?? '' }}"
                    data-shipping-state="{{ $customer->shipping_state ?? '' }}"
                    data-shipping-postal-code="{{ $customer->shipping_postal_code ?? '' }}"
                    data-shipping-country="{{ $customer->shipping_country ?? 'India' }}"
                    {{ old('customer_id', $editing ? $salesInvoice->customer_id : '') == $customer->id ? 'selected' : '' }}>
                    {{ $customer->customer_name }} ({{ $customer->code }})
                </option>
            @endforeach
        </select>
        @error('customer_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>


    <div>
        <label for="mode_of_order" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Mode of Order</label>
        <input type="text" name="mode_of_order" id="mode_of_order"
               value="{{ old('mode_of_order', $editing ? $salesInvoice->mode_of_order : 'IMMEDIATE') }}"
               placeholder="Enter Mode of Order"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('mode_of_order')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="buyer_order_number" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Buyer Order Number</label>
        <input type="text" name="buyer_order_number" id="buyer_order_number"
               value="{{ old('buyer_order_number', $editing ? $salesInvoice->buyer_order_number : '') }}"
               placeholder="Enter Buyer Order Number"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('buyer_order_number')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>
</div>

@php
    $states = [
        'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana',
        'Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur',
        'Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu',
        'Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal',
        'Andaman and Nicobar Islands','Chandigarh','Dadra and Nagar Haveli and Daman and Diu',
        'Delhi','Jammu and Kashmir','Ladakh','Lakshadweep','Puducherry'
    ];
    $countries = [
        'India', 'United States', 'United Kingdom', 'Canada', 'Australia', 'Germany', 'France',
        'Japan', 'China', 'Brazil', 'Russia', 'South Korea', 'Italy', 'Spain', 'Mexico',
        'Indonesia', 'Netherlands', 'Saudi Arabia', 'Turkey', 'Switzerland', 'Singapore',
        'Malaysia', 'Thailand', 'Philippines', 'Vietnam', 'Bangladesh', 'Pakistan', 'Sri Lanka',
        'Nepal', 'Myanmar', 'Other'
    ];
    
    // Parse billing address for editing - try to get from customer first, then parse stored address
    $billingParts = [];
    if ($editing) {
        if ($salesInvoice->customer && $salesInvoice->customer->billing_address_line_1) {
            // Use customer's billing address
            $billingParts = [
                $salesInvoice->customer->billing_address_line_1 ?? '',
                $salesInvoice->customer->billing_address_line_2 ?? '',
                $salesInvoice->customer->billing_city ?? '',
                $salesInvoice->customer->billing_state ?? '',
                $salesInvoice->customer->billing_postal_code ?? '',
                $salesInvoice->customer->billing_country ?? 'India'
            ];
        } elseif ($salesInvoice->billing_address) {
            // Parse stored address string
            $billingParts = explode(', ', $salesInvoice->billing_address);
        }
    }
    
    // Parse shipping address for editing - try to get from customer first, then parse stored address
    $shippingParts = [];
    if ($editing) {
        if ($salesInvoice->customer && $salesInvoice->customer->shipping_address_line_1) {
            // Use customer's shipping address
            $shippingParts = [
                $salesInvoice->customer->shipping_address_line_1 ?? '',
                $salesInvoice->customer->shipping_address_line_2 ?? '',
                $salesInvoice->customer->shipping_city ?? '',
                $salesInvoice->customer->shipping_state ?? '',
                $salesInvoice->customer->shipping_postal_code ?? '',
                $salesInvoice->customer->shipping_country ?? 'India'
            ];
        } elseif ($salesInvoice->shipping_address) {
            // Parse stored address string
            $shippingParts = explode(', ', $salesInvoice->shipping_address);
        }
    }
@endphp

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 25px;">
    <!-- Billing Address Section -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Billing Address</h3>
        
        <div style="margin-bottom: 15px;">
            <label for="billing_address_line_1" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 1</label>
            <input type="text" name="billing_address_line_1" id="billing_address_line_1" readonly
                   value="{{ old('billing_address_line_1', $editing && isset($billingParts[0]) ? $billingParts[0] : '') }}"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f5f5f5;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="billing_address_line_2" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 2</label>
            <input type="text" name="billing_address_line_2" id="billing_address_line_2" readonly
                   value="{{ old('billing_address_line_2', $editing && isset($billingParts[1]) ? $billingParts[1] : '') }}"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f5f5f5;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div>
                <label for="billing_city" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">City</label>
                <input type="text" name="billing_city" id="billing_city" readonly
                       value="{{ old('billing_city', $editing && isset($billingParts[2]) ? $billingParts[2] : '') }}"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f5f5f5;">
            </div>
            <div>
                <label for="billing_state" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">State</label>
                <select name="billing_state" id="billing_state" disabled
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f5f5f5;">
                    <option value="">Select state</option>
                    @foreach($states as $state)
                        <option value="{{ $state }}" {{ old('billing_state', $editing && isset($billingParts[3]) && $billingParts[3] === $state ? $state : '') === $state ? 'selected' : '' }}>{{ $state }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="billing_postal_code" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Postal Code</label>
                <input type="text" name="billing_postal_code" id="billing_postal_code" readonly
                       value="{{ old('billing_postal_code', $editing && isset($billingParts[4]) ? $billingParts[4] : '') }}"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f5f5f5;">
            </div>
        </div>

        <div>
            <label for="billing_country" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Country</label>
            <select name="billing_country" id="billing_country" disabled
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f5f5f5;">
                <option value="">Select country</option>
                @foreach($countries as $country)
                    <option value="{{ $country }}" {{ old('billing_country', $editing && isset($billingParts[5]) && $billingParts[5] === $country ? $country : 'India') === $country ? 'selected' : '' }}>{{ $country }}</option>
                @endforeach
            </select>
    </div>
</div>

    <!-- Shipping Address Section -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Shipping Address</h3>
        
        <div style="margin-bottom: 15px;">
            <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; cursor: pointer;">
                <input type="checkbox" id="same_as_billing" style="width: 18px; height: 18px; cursor: pointer;">
                <span style="font-weight: 500; color: #333;">Same as Billing Address</span>
            </label>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="shipping_address_line_1" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 1</label>
            <input type="text" name="shipping_address_line_1" id="shipping_address_line_1"
                   value="{{ old('shipping_address_line_1', $editing && isset($shippingParts[0]) ? $shippingParts[0] : '') }}"
                   placeholder="Enter shipping address line 1"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="shipping_address_line_2" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 2</label>
            <input type="text" name="shipping_address_line_2" id="shipping_address_line_2"
                   value="{{ old('shipping_address_line_2', $editing && isset($shippingParts[1]) ? $shippingParts[1] : '') }}"
                   placeholder="Enter shipping address line 2"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div>
                <label for="shipping_city" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">City</label>
                <input type="text" name="shipping_city" id="shipping_city"
                       value="{{ old('shipping_city', $editing && isset($shippingParts[2]) ? $shippingParts[2] : '') }}"
                       placeholder="Enter city"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>
            <div>
                <label for="shipping_state" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">State</label>
                <select name="shipping_state" id="shipping_state"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="">Select state</option>
                    @foreach($states as $state)
                        <option value="{{ $state }}" {{ old('shipping_state', $editing && isset($shippingParts[3]) && $shippingParts[3] === $state ? $state : '') === $state ? 'selected' : '' }}>{{ $state }}</option>
                    @endforeach
                </select>
            </div>
    <div>
                <label for="shipping_postal_code" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Postal Code</label>
                <input type="text" name="shipping_postal_code" id="shipping_postal_code"
                       value="{{ old('shipping_postal_code', $editing && isset($shippingParts[4]) ? $shippingParts[4] : '') }}"
                       placeholder="Enter postal code"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>
    </div>

    <div>
            <label for="shipping_country" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Country</label>
            <select name="shipping_country" id="shipping_country"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                <option value="">Select country</option>
                @foreach($countries as $country)
                    <option value="{{ $country }}" {{ old('shipping_country', $editing && isset($shippingParts[5]) && $shippingParts[5] === $country ? $country : 'India') === $country ? 'selected' : '' }}>{{ $country }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<input type="hidden" name="billing_address" id="billing_address">
<input type="hidden" name="shipping_address" id="shipping_address">

<h3 style="margin-bottom: 12px; font-size: 18px; color: #333;">Products</h3>

<div style="overflow-x: auto; margin-bottom: 20px;">
    <table id="itemsTable" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 10px; text-align: left;">Product</th>
                <th style="padding: 10px; text-align: left;">Description</th>
                <th style="padding: 10px; text-align: right;">Quantity Sold</th>
                <th style="padding: 10px; text-align: right;">Unit Price</th>
                <th style="padding: 10px; text-align: right;">Total Amount</th>
                <th style="padding: 10px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $oldItems = old('items', $editing ? $salesInvoice->items->toArray() : [['product_id' => '', 'description' => '', 'quantity_sold' => '', 'unit_price' => '']]);
            @endphp
            @foreach($oldItems as $index => $item)
                <tr>
                    <td style="padding: 8px;">
                        <select name="items[{{ $index }}][product_id]" required
                                style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                            <option value="">-- Select --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                    {{ (int)($item['product_id'] ?? 0) === $product->id ? 'selected' : '' }}>
                                    {{ $product->product_name }} ({{ $product->code }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td style="padding: 8px;">
                        <textarea name="items[{{ $index }}][description]" rows="2"
                                  style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; resize: vertical;">{{ $item['description'] ?? '' }}</textarea>
                    </td>
                    <td style="padding: 8px;">
                        <input type="number" step="1" min="0" name="items[{{ $index }}][quantity_sold]"
                               value="{{ isset($item['quantity_sold']) ? (int)$item['quantity_sold'] : '' }}"
                               class="item-quantity"
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
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
                               value="{{ isset($item['total_amount']) ? number_format($item['total_amount'], 2) : '' }}"
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right; background: #f5f5f5;">
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
                       {{ old('tax_type', $editing && $salesInvoice->gst_classification ? $salesInvoice->gst_classification : 'CGST_SGST') === 'CGST_SGST' ? 'checked' : '' }}
                       style="width: 18px; height: 18px; cursor: pointer;">
                <span style="font-weight: 500; color: #333;">CGST and SGST</span>
            </label>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="radio" name="tax_type" value="IGST" id="tax_type_igst"
                       {{ old('tax_type', $editing && $salesInvoice->gst_classification ? $salesInvoice->gst_classification : 'CGST_SGST') === 'IGST' ? 'checked' : '' }}
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
                <input type="number" step="0.01" min="0" name="gst_percentage_overall" id="gst_percentage" readonly
                       value="{{ old('gst_percentage_overall', $editing ? $salesInvoice->gst_percentage_overall : '18') }}"
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
            <span style="font-weight: 500; color: #374151;">Total Sales Amount:</span>
            <span id="total_sales_amount_view" style="font-weight: 600; color: #111827;">0.00</span>
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

<input type="hidden" name="total_sales_amount" id="total_sales_amount">
<input type="hidden" name="total_gst_amount" id="total_gst_amount">
<input type="hidden" name="grand_total" id="grand_total">

@push('scripts')
<script>
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
        var totalSales = 0;

        rows.forEach(function (row) {
            totalSales += parseNumber(row.querySelector('.item-total-amount').value);
        });

        var gstPercentage = parseNumber(document.getElementById('gst_percentage').value);
        var gstAmount = gstPercentage > 0 ? (totalSales * gstPercentage) / 100 : 0;
        
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

        var grandTotal = totalSales + gstAmount;

        document.getElementById('total_sales_amount_view').innerText = totalSales.toFixed(2);
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

        document.getElementById('total_sales_amount').value = totalSales.toFixed(2);
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
                <select name="items[${index}][product_id]" required
                        style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                    <option value="">-- Select --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->product_name }} ({{ $product->code }})</option>
                    @endforeach
                </select>
            </td>
            <td style="padding: 8px;">
                <textarea name="items[${index}][description]" rows="2"
                          style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; resize: vertical;"></textarea>
            </td>
            <td style="padding: 8px;">
                <input type="number" step="1" min="0" name="items[${index}][quantity_sold]"
                       class="item-quantity"
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
            </td>
            <td style="padding: 8px;">
                <input type="number" step="0.01" min="0" name="items[${index}][unit_price]"
                       class="item-unit-price"
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
            </td>
            <td style="padding: 8px;">
                <input type="text" readonly
                       class="item-total-amount"
                       value=""
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right; background: #f5f5f5;">
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

    // Get company state from server
    const companyState = @json($companyInfo->state ?? null);

    // Function to format address from individual fields
    function formatAddress(line1, line2, city, state, postalCode, country) {
        var parts = [];
        if (line1) parts.push(line1);
        if (line2) parts.push(line2);
        if (city) parts.push(city);
        if (state) parts.push(state);
        if (postalCode) parts.push(postalCode);
        if (country) parts.push(country);
        return parts.join(', ');
    }

    // Function to populate billing address fields
    function populateBillingAddress(option) {
        document.getElementById('billing_address_line_1').value = option.getAttribute('data-billing-address-line-1') || '';
        document.getElementById('billing_address_line_2').value = option.getAttribute('data-billing-address-line-2') || '';
        document.getElementById('billing_city').value = option.getAttribute('data-billing-city') || '';
        document.getElementById('billing_state').value = option.getAttribute('data-billing-state-value') || '';
        document.getElementById('billing_postal_code').value = option.getAttribute('data-billing-postal-code') || '';
        document.getElementById('billing_country').value = option.getAttribute('data-billing-country') || 'India';
        
        // Update hidden billing_address field
        var billingAddress = formatAddress(
            document.getElementById('billing_address_line_1').value,
            document.getElementById('billing_address_line_2').value,
            document.getElementById('billing_city').value,
            document.getElementById('billing_state').value,
            document.getElementById('billing_postal_code').value,
            document.getElementById('billing_country').value
        );
        document.getElementById('billing_address').value = billingAddress;
    }

    // Function to populate shipping address fields
    function populateShippingAddress(option) {
        document.getElementById('shipping_address_line_1').value = option.getAttribute('data-shipping-address-line-1') || '';
        document.getElementById('shipping_address_line_2').value = option.getAttribute('data-shipping-address-line-2') || '';
        document.getElementById('shipping_city').value = option.getAttribute('data-shipping-city') || '';
        document.getElementById('shipping_state').value = option.getAttribute('data-shipping-state') || '';
        document.getElementById('shipping_postal_code').value = option.getAttribute('data-shipping-postal-code') || '';
        document.getElementById('shipping_country').value = option.getAttribute('data-shipping-country') || 'India';
        
        // Update hidden shipping_address field
        var shippingAddress = formatAddress(
            document.getElementById('shipping_address_line_1').value,
            document.getElementById('shipping_address_line_2').value,
            document.getElementById('shipping_city').value,
            document.getElementById('shipping_state').value,
            document.getElementById('shipping_postal_code').value,
            document.getElementById('shipping_country').value
        );
        document.getElementById('shipping_address').value = shippingAddress;
    }

    // Handle "Same as Billing Address" checkbox
    document.getElementById('same_as_billing').addEventListener('change', function() {
        if (this.checked) {
            // Copy billing address to shipping address
            document.getElementById('shipping_address_line_1').value = document.getElementById('billing_address_line_1').value;
            document.getElementById('shipping_address_line_2').value = document.getElementById('billing_address_line_2').value;
            document.getElementById('shipping_city').value = document.getElementById('billing_city').value;
            document.getElementById('shipping_state').value = document.getElementById('billing_state').value;
            document.getElementById('shipping_postal_code').value = document.getElementById('billing_postal_code').value;
            document.getElementById('shipping_country').value = document.getElementById('billing_country').value;
            
            // Update hidden shipping_address field
            var shippingAddress = formatAddress(
                document.getElementById('shipping_address_line_1').value,
                document.getElementById('shipping_address_line_2').value,
                document.getElementById('shipping_city').value,
                document.getElementById('shipping_state').value,
                document.getElementById('shipping_postal_code').value,
                document.getElementById('shipping_country').value
            );
            document.getElementById('shipping_address').value = shippingAddress;
        } else {
            // Clear shipping address fields when unchecked
            document.getElementById('shipping_address_line_1').value = '';
            document.getElementById('shipping_address_line_2').value = '';
            document.getElementById('shipping_city').value = '';
            document.getElementById('shipping_state').value = '';
            document.getElementById('shipping_postal_code').value = '';
            document.getElementById('shipping_country').value = 'India';
            
            // Clear hidden shipping_address field
            document.getElementById('shipping_address').value = '';
        }
    });

    // Update hidden address fields when shipping address fields change
    ['shipping_address_line_1', 'shipping_address_line_2', 'shipping_city', 'shipping_state', 'shipping_postal_code', 'shipping_country'].forEach(function(fieldId) {
        var field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', function() {
                var shippingAddress = formatAddress(
                    document.getElementById('shipping_address_line_1').value,
                    document.getElementById('shipping_address_line_2').value,
                    document.getElementById('shipping_city').value,
                    document.getElementById('shipping_state').value,
                    document.getElementById('shipping_postal_code').value,
                    document.getElementById('shipping_country').value
                );
                document.getElementById('shipping_address').value = shippingAddress;
            });
        }
    });

    // Auto-fill billing and shipping addresses when customer is selected
    document.getElementById('customer_id').addEventListener('change', function() {
        var option = this.options[this.selectedIndex];
        var customerState = option.getAttribute('data-billing-state') || '';
        
        // Populate individual address fields
        populateBillingAddress(option);
        
        // Clear shipping address fields and uncheck "Same as Billing Address" checkbox
        document.getElementById('shipping_address_line_1').value = '';
        document.getElementById('shipping_address_line_2').value = '';
        document.getElementById('shipping_city').value = '';
        document.getElementById('shipping_state').value = '';
        document.getElementById('shipping_postal_code').value = '';
        document.getElementById('shipping_country').value = 'India';
        document.getElementById('same_as_billing').checked = false;
        
        // Clear hidden shipping address field
        document.getElementById('shipping_address').value = '';
        
        // Auto-select tax type based on company and customer state
        if (companyState && customerState) {
            var taxTypeCGST = document.getElementById('tax_type_cgst_sgst');
            var taxTypeIGST = document.getElementById('tax_type_igst');
            
            if (companyState === customerState) {
                // Same state - CGST and SGST
                if (taxTypeCGST) {
                    taxTypeCGST.checked = true;
                }
            } else {
                // Different state - IGST
                if (taxTypeIGST) {
                    taxTypeIGST.checked = true;
                }
            }
            // Trigger change event to update calculations
            var taxTypeElement = document.querySelector('input[name="tax_type"]:checked');
            if (taxTypeElement) {
                taxTypeElement.dispatchEvent(new Event('change'));
            }
        }
        
        recalcTotals();
    });

    // Function to update tax type based on customer state
    function updateTaxTypeFromCustomer() {
        var customerSelect = document.getElementById('customer_id');
        if (customerSelect && customerSelect.value) {
            var option = customerSelect.options[customerSelect.selectedIndex];
            var customerState = option.getAttribute('data-billing-state') || '';
            
            // Populate addresses if customer is selected
            populateBillingAddress(option);
            populateShippingAddress(option);
            
            if (companyState && customerState) {
                var taxTypeCGST = document.getElementById('tax_type_cgst_sgst');
                var taxTypeIGST = document.getElementById('tax_type_igst');
                
                if (companyState === customerState) {
                    // Same state - CGST and SGST
                    if (taxTypeCGST) {
                        taxTypeCGST.checked = true;
                    }
                } else {
                    // Different state - IGST
                    if (taxTypeIGST) {
                        taxTypeIGST.checked = true;
                    }
                }
                // Trigger change event to update calculations
                var taxTypeElement = document.querySelector('input[name="tax_type"]:checked');
                if (taxTypeElement) {
                    taxTypeElement.dispatchEvent(new Event('change'));
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize GST percentage to 18 if empty
        var gstPercentageField = document.getElementById('gst_percentage');
        if (!gstPercentageField.value || gstPercentageField.value === '') {
            gstPercentageField.value = '18';
        }
        
        // Initialize hidden address fields from individual fields
        var billingAddress = formatAddress(
            document.getElementById('billing_address_line_1').value,
            document.getElementById('billing_address_line_2').value,
            document.getElementById('billing_city').value,
            document.getElementById('billing_state').value,
            document.getElementById('billing_postal_code').value,
            document.getElementById('billing_country').value
        );
        document.getElementById('billing_address').value = billingAddress;
        
        var shippingAddress = formatAddress(
            document.getElementById('shipping_address_line_1').value,
            document.getElementById('shipping_address_line_2').value,
            document.getElementById('shipping_city').value,
            document.getElementById('shipping_state').value,
            document.getElementById('shipping_postal_code').value,
            document.getElementById('shipping_country').value
        );
        document.getElementById('shipping_address').value = shippingAddress;
        
        // Initialize tax type based on existing data or customer state
        @if($editing && $salesInvoice->gst_classification)
            var existingGstType = '{{ $salesInvoice->gst_classification }}';
            if (existingGstType === 'CGST_SGST') {
                document.getElementById('tax_type_cgst_sgst').checked = true;
            } else if (existingGstType === 'IGST') {
                document.getElementById('tax_type_igst').checked = true;
            }
        @else
            // Auto-select tax type based on customer state if not editing
            updateTaxTypeFromCustomer();
        @endif
        
        var rows = document.querySelectorAll('#itemsTable tbody tr');
        rows.forEach(function (row) {
            attachRowEvents(row);
            recalcRow(row);
        });
        recalcTotals();
    });
    
    // Update hidden address fields before form submission
    document.querySelector('form').addEventListener('submit', function() {
        var billingAddress = formatAddress(
            document.getElementById('billing_address_line_1').value,
            document.getElementById('billing_address_line_2').value,
            document.getElementById('billing_city').value,
            document.getElementById('billing_state').value,
            document.getElementById('billing_postal_code').value,
            document.getElementById('billing_country').value
        );
        document.getElementById('billing_address').value = billingAddress;
        
        var shippingAddress = formatAddress(
            document.getElementById('shipping_address_line_1').value,
            document.getElementById('shipping_address_line_2').value,
            document.getElementById('shipping_city').value,
            document.getElementById('shipping_state').value,
            document.getElementById('shipping_postal_code').value,
            document.getElementById('shipping_country').value
        );
        document.getElementById('shipping_address').value = shippingAddress;
    });
</script>
@endpush

