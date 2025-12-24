@extends('layouts.dashboard')

@section('title', 'Edit Payment - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Payment</h2>
        <a href="{{ route('payment-trackings.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('payment-trackings.update', $paymentTracking->id) }}" method="POST" id="paymentForm">
        @csrf
        @method('PUT')

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Payment Information</h3>
            
            <div style="margin-bottom: 20px;">
                <label for="customer_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer <span style="color: red;">*</span></label>
                <select name="customer_id" id="customer_id" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id', $paymentTracking->customer_id) == $customer->id ? 'selected' : '' }}>
                            {{ $customer->customer_name }} ({{ $customer->code }})
                        </option>
                    @endforeach
                </select>
                @error('customer_id')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="sales_invoice_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Invoice Number <span style="color: red;">*</span></label>
                <select name="sales_invoice_id" id="sales_invoice_id" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="">-- Select Customer First --</option>
                </select>
                <div id="invoice-loading" style="margin-top: 10px; padding: 10px; background: #fff3cd; border-radius: 5px; display: none;">
                    <span style="color: #856404; font-size: 13px;">
                        <i class="fas fa-spinner fa-spin"></i> Loading invoices...
                    </span>
                </div>
                <div id="invoice-empty" style="margin-top: 10px; padding: 10px; background: #f8d7da; border-radius: 5px; display: none;">
                    <span style="color: #721c24; font-size: 13px;">
                        <i class="fas fa-info-circle"></i> No unpaid or partially paid invoices found for this customer.
                    </span>
                </div>
                <div id="invoice-info" style="margin-top: 10px; padding: 10px; background: #e7f3ff; border-radius: 5px; display: none;">
                    <div style="font-size: 13px; color: #333;">
                        <strong>Invoice Date:</strong> <span id="invoice-date"></span><br>
                        <strong>Invoice Total:</strong> ₹<span id="invoice-total"></span><br>
                        <strong>Total Paid:</strong> ₹<span id="total-paid"></span><br>
                        <strong>Balance:</strong> ₹<span id="invoice-balance" style="font-weight: 600; color: #dc3545;"></span>
                    </div>
                </div>
                @error('sales_invoice_id')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label for="payment_date" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Payment Date <span style="color: red;">*</span></label>
                    <input type="date" name="payment_date" id="payment_date" required
                           value="{{ old('payment_date', $paymentTracking->payment_date->format('Y-m-d')) }}"
                           max="{{ now()->format('Y-m-d') }}"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    @error('payment_date')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="payment_amount" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Payment Amount <span style="color: red;">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="payment_amount" id="payment_amount" required
                           value="{{ old('payment_amount', $paymentTracking->payment_amount) }}"
                           placeholder="Enter payment amount"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <small id="max-amount-hint" style="color: #666; font-size: 12px; display: none;">Maximum: ₹<span id="max-amount"></span></small>
                    @error('payment_amount')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="payment_method" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Payment Method <span style="color: red;">*</span></label>
                <select name="payment_method" id="payment_method" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="Cash" {{ old('payment_method', $paymentTracking->payment_method) === 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="Cheque" {{ old('payment_method', $paymentTracking->payment_method) === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                    <option value="Bank Transfer" {{ old('payment_method', $paymentTracking->payment_method) === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="UPI" {{ old('payment_method', $paymentTracking->payment_method) === 'UPI' ? 'selected' : '' }}>UPI</option>
                    <option value="Credit Card" {{ old('payment_method', $paymentTracking->payment_method) === 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                    <option value="Debit Card" {{ old('payment_method', $paymentTracking->payment_method) === 'Debit Card' ? 'selected' : '' }}>Debit Card</option>
                    <option value="Other" {{ old('payment_method', $paymentTracking->payment_method) === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('payment_method')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="remarks" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Remarks</label>
                <textarea name="remarks" id="remarks" rows="3"
                          placeholder="Enter any remarks or notes (optional)"
                          style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('remarks', $paymentTracking->remarks) }}</textarea>
                @error('remarks')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="{{ route('payment-trackings.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                <i class="fas fa-save"></i> Update Payment
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Load invoices when customer is selected
    document.getElementById('customer_id').addEventListener('change', function() {
        const customerId = this.value;
        const invoiceSelect = document.getElementById('sales_invoice_id');
        const invoiceInfo = document.getElementById('invoice-info');
        const invoiceLoading = document.getElementById('invoice-loading');
        const invoiceEmpty = document.getElementById('invoice-empty');
        const maxAmountHint = document.getElementById('max-amount-hint');
        
        // Reset invoice dropdown and hide info
        invoiceSelect.innerHTML = '<option value="">-- Select Invoice --</option>';
        invoiceSelect.disabled = true;
        invoiceInfo.style.display = 'none';
        invoiceEmpty.style.display = 'none';
        invoiceLoading.style.display = 'none';
        maxAmountHint.style.display = 'none';
        
        if (customerId) {
            // Show loading indicator
            invoiceLoading.style.display = 'block';
            
            const currentInvoiceId = {{ $paymentTracking->sales_invoice_id }};
            fetch('{{ route('payment-trackings.get-invoices') }}?customer_id=' + customerId + '&current_invoice_id=' + currentInvoiceId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    invoiceLoading.style.display = 'none';
                    
                    if (data && data.length > 0) {
                        // Populate invoice dropdown
                        data.forEach(function(invoice) {
                            const option = document.createElement('option');
                            option.value = invoice.id;
                            option.textContent = invoice.invoice_number + ' (Balance: ₹' + invoice.balance + ')';
                            option.setAttribute('data-balance', invoice.balance_raw);
                            option.setAttribute('data-total', invoice.grand_total);
                            option.setAttribute('data-paid', invoice.total_paid);
                            option.setAttribute('data-date', invoice.invoice_date);
                            
                            // Select current invoice
                            if (invoice.id == {{ $paymentTracking->sales_invoice_id }}) {
                                option.selected = true;
                                // Show invoice info
                                const balance = parseFloat(invoice.balance_raw);
                                const total = invoice.grand_total;
                                const paid = invoice.total_paid;
                                const date = invoice.invoice_date;
                                
                                document.getElementById('invoice-date').textContent = date;
                                document.getElementById('invoice-total').textContent = total;
                                document.getElementById('total-paid').textContent = paid;
                                document.getElementById('invoice-balance').textContent = balance.toFixed(2);
                                document.getElementById('max-amount').textContent = balance.toFixed(2);
                                
                                invoiceInfo.style.display = 'block';
                                maxAmountHint.style.display = 'block';
                                document.getElementById('payment_amount').setAttribute('max', balance);
                            }
                            
                            invoiceSelect.appendChild(option);
                        });
                        invoiceSelect.disabled = false;
                    } else {
                        // Show message if no invoices found
                        invoiceEmpty.style.display = 'block';
                        invoiceSelect.innerHTML = '<option value="">-- No Unpaid Invoices Found --</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading invoices:', error);
                    invoiceLoading.style.display = 'none';
                    invoiceEmpty.style.display = 'block';
                    invoiceEmpty.innerHTML = '<span style="color: #721c24; font-size: 13px;"><i class="fas fa-exclamation-triangle"></i> Error loading invoices. Please try again.</span>';
                    invoiceSelect.innerHTML = '<option value="">-- Error Loading Invoices --</option>';
                });
        } else {
            invoiceSelect.innerHTML = '<option value="">-- Select Customer First --</option>';
        }
    });

    // Show invoice info and set max amount when invoice is selected
    document.getElementById('sales_invoice_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const invoiceInfo = document.getElementById('invoice-info');
        const maxAmountHint = document.getElementById('max-amount-hint');
        
        if (selectedOption.value && selectedOption.hasAttribute('data-balance')) {
            const balance = parseFloat(selectedOption.getAttribute('data-balance'));
            const total = selectedOption.getAttribute('data-total');
            const paid = selectedOption.getAttribute('data-paid');
            const date = selectedOption.getAttribute('data-date');
            
            document.getElementById('invoice-date').textContent = date;
            document.getElementById('invoice-total').textContent = total;
            document.getElementById('total-paid').textContent = paid;
            document.getElementById('invoice-balance').textContent = balance.toFixed(2);
            document.getElementById('max-amount').textContent = balance.toFixed(2);
            
            invoiceInfo.style.display = 'block';
            maxAmountHint.style.display = 'block';
            
            // Set max attribute on payment amount input
            document.getElementById('payment_amount').setAttribute('max', balance);
        } else {
            invoiceInfo.style.display = 'none';
            maxAmountHint.style.display = 'none';
            document.getElementById('payment_amount').removeAttribute('max');
        }
    });

    // Validate payment amount on input
    document.getElementById('payment_amount').addEventListener('input', function() {
        const maxAmount = parseFloat(this.getAttribute('max'));
        const enteredAmount = parseFloat(this.value);
        
        if (maxAmount && enteredAmount > maxAmount) {
            this.setCustomValidity('Payment amount cannot exceed the invoice balance of ₹' + maxAmount.toFixed(2));
        } else {
            this.setCustomValidity('');
        }
    });

    // Load invoices on page load if customer is already selected
    document.addEventListener('DOMContentLoaded', function() {
        const customerId = document.getElementById('customer_id').value;
        const currentInvoiceId = {{ $paymentTracking->sales_invoice_id }};
        
        if (customerId) {
            // Automatically trigger customer change to load invoices
            const customerSelect = document.getElementById('customer_id');
            const changeEvent = new Event('change', { bubbles: true });
            customerSelect.dispatchEvent(changeEvent);
        }
    });
</script>
@endpush
@endsection

