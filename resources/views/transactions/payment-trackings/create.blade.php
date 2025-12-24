@extends('layouts.dashboard')

@section('title', 'Record Payment - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Record Payment</h2>
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

    <form action="{{ route('payment-trackings.store') }}" method="POST" id="paymentForm">
        @csrf

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Payment Information</h3>
            
            <div style="margin-bottom: 20px;">
                <label for="customer_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer <span style="color: red;">*</span></label>
                <select name="customer_id" id="customer_id" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
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
                <div id="invoice-info" style="margin-top: 10px; padding: 15px; background: #e7f3ff; border-radius: 5px; display: none;">
                    <div style="display: flex; gap: 30px; align-items: center; font-size: 13px; color: #333; flex-wrap: wrap;">
                        <div>
                            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Invoice Date</label>
                            <p style="color: #333; font-size: 14px; margin: 0; font-weight: 500;"><span id="invoice-date"></span></p>
                        </div>
                        <div>
                            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Invoice Total</label>
                            <p style="color: #333; font-size: 14px; margin: 0; font-weight: 500;">₹<span id="invoice-total"></span></p>
                        </div>
                        <div>
                            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Total Paid</label>
                            <p style="color: #333; font-size: 14px; margin: 0; font-weight: 500;">₹<span id="total-paid"></span></p>
                        </div>
                        <div>
                            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Balance</label>
                            <p style="color: #dc3545; font-size: 14px; margin: 0; font-weight: 600;">₹<span id="invoice-balance"></span></p>
                        </div>
                        <div style="display: flex; align-items: flex-end;">
                            <button type="button" id="transaction-history-btn" style="padding: 8px 16px; background: #17a2b8; color: white; border: none; border-radius: 5px; font-size: 13px; font-weight: 500; cursor: pointer; display: none; margin-top: 20px;" onclick="showTransactionHistory()">
                                <i class="fas fa-history"></i> Transaction History
                            </button>
                        </div>
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
                           value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                           max="{{ now()->format('Y-m-d') }}"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    @error('payment_date')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="payment_amount" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Payment Amount <span style="color: red;">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="payment_amount" id="payment_amount" required
                           value="{{ old('payment_amount') }}"
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
                    <option value="Cash" {{ old('payment_method', 'Cash') === 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="Cheque" {{ old('payment_method') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                    <option value="Bank Transfer" {{ old('payment_method') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="UPI" {{ old('payment_method') === 'UPI' ? 'selected' : '' }}>UPI</option>
                    <option value="Credit Card" {{ old('payment_method') === 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                    <option value="Debit Card" {{ old('payment_method') === 'Debit Card' ? 'selected' : '' }}>Debit Card</option>
                    <option value="Other" {{ old('payment_method') === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('payment_method')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="remarks" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Remarks</label>
                <textarea name="remarks" id="remarks" rows="3"
                          placeholder="Enter any remarks or notes (optional)"
                          style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('remarks') }}</textarea>
                @error('remarks')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="button" onclick="clearForm()" style="padding: 12px 24px; background: #17a2b8; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Cancel
            </button>
            <button type="submit" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                <i class="fas fa-save"></i> Record Payment
            </button>
        </div>
    </form>
</div>

<!-- Transaction History Modal -->
<div id="transaction-history-modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: #fefefe; margin: 5% auto; padding: 0; border-radius: 10px; width: 90%; max-width: 600px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
        <div style="padding: 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; background: #667eea; color: white; border-radius: 10px 10px 0 0;">
            <h3 style="margin: 0; font-size: 20px; font-weight: 600;">Transaction History</h3>
            <button onclick="closeTransactionHistoryModal()" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 5px; transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="padding: 20px;">
            <div id="transaction-history-loading" style="text-align: center; padding: 40px; display: none;">
                <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #667eea;"></i>
                <p style="margin-top: 15px; color: #666;">Loading transaction history...</p>
            </div>
            <div id="transaction-history-content" style="display: none;">
                <p id="transaction-history-invoice" style="margin-bottom: 20px; font-size: 16px; font-weight: 600; color: #333;"></p>
                <div id="transaction-history-list" style="max-height: 400px; overflow-y: auto;">
                    <!-- Transactions will be loaded here -->
                </div>
            </div>
            <div id="transaction-history-empty" style="text-align: center; padding: 40px; display: none; color: #666;">
                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                <p>No transaction history found for this invoice.</p>
            </div>
            <div id="transaction-history-error" style="text-align: center; padding: 40px; display: none; color: #dc3545;">
                <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 15px;"></i>
                <p id="transaction-history-error-message">Error loading transaction history.</p>
            </div>
        </div>
        <div style="padding: 15px 20px; border-top: 1px solid #dee2e6; text-align: right; background: #f8f9fa; border-radius: 0 0 10px 10px;">
            <button onclick="closeTransactionHistoryModal()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; font-size: 14px; font-weight: 500; cursor: pointer;">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function clearForm() {
        document.getElementById('paymentForm').reset();
        document.getElementById('customer_id').value = '';
        document.getElementById('sales_invoice_id').innerHTML = '<option value="">-- Select Invoice --</option>';
        document.getElementById('payment_date').value = '{{ now()->format('Y-m-d') }}';
        document.getElementById('payment_method').value = 'Cash';
        document.getElementById('invoice-info').style.display = 'none';
        document.getElementById('transaction-history-btn').style.display = 'none';
        document.getElementById('max-amount-hint').style.display = 'none';
    }

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
        document.getElementById('transaction-history-btn').style.display = 'none';
        invoiceEmpty.style.display = 'none';
        invoiceLoading.style.display = 'none';
        maxAmountHint.style.display = 'none';
        
        if (customerId) {
            // Show loading indicator
            invoiceLoading.style.display = 'block';
            
            const url = '{{ route('payment-trackings.get-invoices') }}?customer_id=' + encodeURIComponent(customerId);
            
            console.log('Fetching invoices from:', url);
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.error || 'Network response was not ok');
                        }).catch(() => {
                            throw new Error('Network response was not ok: ' + response.status);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    invoiceLoading.style.display = 'none';
                    
                    // Check if data is an array
                    if (Array.isArray(data) && data.length > 0) {
                        // Populate invoice dropdown
                        data.forEach(function(invoice) {
                            const option = document.createElement('option');
                            option.value = invoice.id;
                            option.textContent = invoice.invoice_number + ' (Balance: ₹' + invoice.balance + ')';
                            option.setAttribute('data-balance', invoice.balance_raw);
                            option.setAttribute('data-total', invoice.grand_total);
                            option.setAttribute('data-paid', invoice.total_paid);
                            option.setAttribute('data-date', invoice.invoice_date);
                            invoiceSelect.appendChild(option);
                        });
                        invoiceSelect.disabled = false;
                    } else {
                        // Show message if no invoices found
                        invoiceEmpty.style.display = 'block';
                        invoiceSelect.innerHTML = '<option value="">-- No Unpaid Invoices Found --</option>';
                        invoiceSelect.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error loading invoices:', error);
                    invoiceLoading.style.display = 'none';
                    invoiceEmpty.style.display = 'block';
                    invoiceEmpty.innerHTML = '<span style="color: #721c24; font-size: 13px;"><i class="fas fa-exclamation-triangle"></i> Error loading invoices: ' + error.message + '. Please try again.</span>';
                    invoiceSelect.innerHTML = '<option value="">-- Error Loading Invoices --</option>';
                    invoiceSelect.disabled = false;
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
            const dateStr = selectedOption.getAttribute('data-date');
            
            // Format date from YYYY-MM-DD to DD MMM YYYY (e.g., 23 Dec 2025)
            let formattedDate = dateStr;
            if (dateStr) {
                const date = new Date(dateStr + 'T00:00:00');
                if (!isNaN(date.getTime())) {
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    const day = date.getDate();
                    const month = months[date.getMonth()];
                    const year = date.getFullYear();
                    formattedDate = day + ' ' + month + ' ' + year;
                }
            }
            
            document.getElementById('invoice-date').textContent = formattedDate;
            document.getElementById('invoice-total').textContent = total;
            document.getElementById('total-paid').textContent = paid;
            document.getElementById('invoice-balance').textContent = balance.toFixed(2);
            document.getElementById('max-amount').textContent = balance.toFixed(2);
            
            invoiceInfo.style.display = 'block';
            maxAmountHint.style.display = 'block';
            
            // Show transaction history button
            document.getElementById('transaction-history-btn').style.display = 'block';
            
            // Set max attribute on payment amount input
            document.getElementById('payment_amount').setAttribute('max', balance);
        } else {
            invoiceInfo.style.display = 'none';
            maxAmountHint.style.display = 'none';
            document.getElementById('transaction-history-btn').style.display = 'none';
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


    // Show transaction history modal
    function showTransactionHistory() {
        const invoiceId = document.getElementById('sales_invoice_id').value;
        if (!invoiceId) {
            alert('Please select an invoice first.');
            return;
        }

        const modal = document.getElementById('transaction-history-modal');
        const loading = document.getElementById('transaction-history-loading');
        const content = document.getElementById('transaction-history-content');
        const empty = document.getElementById('transaction-history-empty');
        const error = document.getElementById('transaction-history-error');
        const list = document.getElementById('transaction-history-list');
        const invoiceInfo = document.getElementById('transaction-history-invoice');

        // Show modal and loading
        modal.style.display = 'block';
        loading.style.display = 'block';
        content.style.display = 'none';
        empty.style.display = 'none';
        error.style.display = 'none';

        // Fetch transaction history
        const url = '{{ route("payment-trackings.get-transaction-history", ":id") }}'.replace(':id', invoiceId);
        console.log('Fetching transaction history from:', url);
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.error || 'Network response was not ok');
                }).catch(() => {
                    throw new Error('Network response was not ok: ' + response.status);
                });
            }
            return response.json();
        })
        .then(data => {
            loading.style.display = 'none';

            if (data.transactions && data.transactions.length > 0) {
                invoiceInfo.textContent = 'Invoice: ' + (data.invoice_number || 'N/A');
                
                // Clear previous content
                list.innerHTML = '';
                
                // Create table for transactions
                let tableHTML = '<table style="width: 100%; border-collapse: collapse;">';
                tableHTML += '<thead><tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">';
                tableHTML += '<th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Transaction Date</th>';
                tableHTML += '<th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Amount</th>';
                tableHTML += '</tr></thead><tbody>';
                
                data.transactions.forEach(function(transaction) {
                    tableHTML += '<tr style="border-bottom: 1px solid #dee2e6;">';
                    tableHTML += '<td style="padding: 12px; color: #333;">' + transaction.payment_date_formatted + '</td>';
                    tableHTML += '<td style="padding: 12px; text-align: right; color: #333; font-weight: 500;">₹' + transaction.payment_amount + '</td>';
                    tableHTML += '</tr>';
                });
                
                tableHTML += '</tbody></table>';
                list.innerHTML = tableHTML;
                
                content.style.display = 'block';
            } else {
                empty.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading transaction history:', error);
            loading.style.display = 'none';
            error.style.display = 'block';
            document.getElementById('transaction-history-error-message').textContent = 'Error: ' + error.message;
        });
    }

    // Close transaction history modal
    function closeTransactionHistoryModal() {
        document.getElementById('transaction-history-modal').style.display = 'none';
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('transaction-history-modal');
        if (event.target == modal) {
            closeTransactionHistoryModal();
        }
    }
</script>
@endpush
@endsection

