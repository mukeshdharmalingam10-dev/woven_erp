@extends('layouts.dashboard')

@section('title', 'Quotations - Woven_ERP')

@section('content')
@php
    $user = auth()->user();
    $formName = $user->getFormNameFromRoute('quotations.index');
    $canRead = $user->canRead($formName);
    $canWrite = $user->canWrite($formName);
    $canDelete = $user->canDelete($formName);
@endphp

<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Quotations</h2>
        @if($canWrite)
            <a href="{{ route('quotations.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> New Quotation
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('quotations.index') }}" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by quotation ID or customer..."
                style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            <button type="submit" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('quotations.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>

    @php
        $currentSort = request('sort_by');
        $currentOrder = request('sort_order', 'desc');
    @endphp
    @if($quotations->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">
                            <span class="sort-link" data-sort-by="quotation_id" data-sort-order="{{ $currentSort == 'quotation_id' && $currentOrder == 'asc' ? 'desc' : 'asc' }}" style="color: #333; display: flex; align-items: center; gap: 5px; cursor: pointer; user-select: none;">
                                Quotation ID
                                @php
                                    $isActive = $currentSort == 'quotation_id';
                                    $iconClass = $isActive ? ($currentOrder == 'desc' ? 'fa-sort-down' : 'fa-sort-up') : 'fa-sort';
                                @endphp
                                <i class="fas {{ $iconClass }}" style="opacity: {{ $isActive ? '1' : '0.3' }}; font-size: 12px;"></i>
                            </span>
                        </th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">
                            <span class="sort-link" data-sort-by="customer" data-sort-order="{{ $currentSort == 'customer' && $currentOrder == 'asc' ? 'desc' : 'asc' }}" style="color: #333; display: flex; align-items: center; gap: 5px; cursor: pointer; user-select: none;">
                                Customer
                                @php
                                    $isActive = $currentSort == 'customer';
                                    $iconClass = $isActive ? ($currentOrder == 'desc' ? 'fa-sort-down' : 'fa-sort-up') : 'fa-sort';
                                @endphp
                                <i class="fas {{ $iconClass }}" style="opacity: {{ $isActive ? '1' : '0.3' }}; font-size: 12px;"></i>
                            </span>
                        </th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">
                            <span class="sort-link" data-sort-by="company_name" data-sort-order="{{ $currentSort == 'company_name' && $currentOrder == 'asc' ? 'desc' : 'asc' }}" style="color: #333; display: flex; align-items: center; gap: 5px; cursor: pointer; user-select: none;">
                                Company Name
                                @php
                                    $isActive = $currentSort == 'company_name';
                                    $iconClass = $isActive ? ($currentOrder == 'desc' ? 'fa-sort-down' : 'fa-sort-up') : 'fa-sort';
                                @endphp
                                <i class="fas {{ $iconClass }}" style="opacity: {{ $isActive ? '1' : '0.3' }}; font-size: 12px;"></i>
                            </span>
                        </th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">
                            <span class="sort-link" data-sort-by="total_amount" data-sort-order="{{ $currentSort == 'total_amount' && $currentOrder == 'asc' ? 'desc' : 'asc' }}" style="color: #333; display: flex; align-items: center; gap: 5px; justify-content: flex-end; cursor: pointer; user-select: none;">
                                Total Amount
                                @php
                                    $isActive = $currentSort == 'total_amount';
                                    $iconClass = $isActive ? ($currentOrder == 'desc' ? 'fa-sort-down' : 'fa-sort-up') : 'fa-sort';
                                @endphp
                                <i class="fas {{ $iconClass }}" style="opacity: {{ $isActive ? '1' : '0.3' }}; font-size: 12px;"></i>
                            </span>
                        </th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotations as $quotation)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($quotations->currentPage() - 1) * $quotations->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $quotation->quotation_id }}</td>
                            <td style="padding: 12px; color: #333;">{{ $quotation->customer->customer_name ?? '-' }}</td>
                            <td style="padding: 12px; color: #333;">{{ $quotation->company_name ?? '-' }}</td>
                            <td style="padding: 12px; color: #333; text-align: right;">{{ number_format($quotation->total_amount, 2) }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    @if($canRead)
                                        <a href="{{ route('quotations.show', $quotation->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                    @if($canWrite)
                                        <a href="{{ route('quotations.edit', $quotation->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                    @if($canDelete)
                                        <form action="{{ route('quotations.destroy', $quotation->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this quotation?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @include('partials.pagination', ['paginator' => $quotations, 'routeUrl' => route('quotations.index')])
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No quotations found.</p>
            @if($canWrite)
                <a href="{{ route('quotations.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    Create First Quotation
                </a>
            @endif
        </div>
    @endif
</div>

@push('scripts')
<script>
    (function() {
        'use strict';
        
        const routeUrl = '{{ route("quotations.index") }}';
        let isProcessing = false;
        
        function handleSortClick(e) {
            // Prevent all default behaviors
            if (e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
            }
            
            // Prevent multiple simultaneous requests
            if (isProcessing) {
                return false;
            }
            
            const link = e ? e.currentTarget : this;
            const sortBy = link.getAttribute('data-sort-by');
            const sortOrder = link.getAttribute('data-sort-order');
            
            if (!sortBy || !sortOrder) {
                return false;
            }
            
            isProcessing = true;
            
            const tableBody = document.querySelector('table tbody');
            const tableHead = document.querySelector('table thead');
            
            // Show loading state
            if (tableBody) {
                const colCount = tableBody.querySelector('tr')?.querySelectorAll('td').length || 6;
                tableBody.innerHTML = `<tr><td colspan="${colCount}" style="padding:20px; text-align:center; color:#666;"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>`;
            }
            
            // Build URL with all current parameters
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort_by', sortBy);
            urlParams.set('sort_order', sortOrder);
            
            // Make AJAX request with redirect: 'manual' to prevent automatic following
            fetch(routeUrl + '?' + urlParams.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                    'Cache-Control': 'no-cache'
                },
                credentials: 'same-origin',
                redirect: 'manual' // Don't follow redirects automatically
            })
            .then(response => {
                // Check if response is a redirect
                if (response.type === 'opaqueredirect' || response.status === 0) {
                    throw new Error('Redirect detected');
                }
                
                // Check if response is ok
                if (!response.ok && response.status !== 200) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                
                return response.text();
            })
            .then(html => {
                if (!html || html.trim().length === 0) {
                    throw new Error('Empty response received');
                }
                
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Check if we got a full page (might indicate redirect)
                const title = doc.querySelector('title');
                if (title && title.textContent.includes('Redirect')) {
                    throw new Error('Redirect detected in response');
                }
                
                // Extract table body
                const newTableBody = doc.querySelector('table tbody');
                if (newTableBody && tableBody) {
                    tableBody.innerHTML = newTableBody.innerHTML;
                } else if (!newTableBody && tableBody) {
                    throw new Error('Table body not found in response');
                }
                
                // Extract and update table header with new sort icons
                const newTableHead = doc.querySelector('table thead');
                if (newTableHead && tableHead) {
                    tableHead.innerHTML = newTableHead.innerHTML;
                    // Re-attach event listeners to new header links
                    initSorting();
                }
                
                // Extract pagination
                const newPagination = doc.querySelector('[style*="margin-top: 20px"]') || 
                                      doc.querySelector('[style*="margin-top:20px"]');
                const currentPagination = document.querySelector('[style*="margin-top: 20px"]') || 
                                          document.querySelector('[style*="margin-top:20px"]');
                
                if (newPagination && currentPagination) {
                    currentPagination.outerHTML = newPagination.outerHTML;
                } else if (newPagination && !currentPagination) {
                    const tableDiv = document.querySelector('table')?.closest('div');
                    if (tableDiv && tableDiv.nextElementSibling) {
                        tableDiv.nextElementSibling.outerHTML = newPagination.outerHTML;
                    }
                }
                
                // Update URL without reload
                window.history.pushState({}, '', routeUrl + '?' + urlParams.toString());
                
                isProcessing = false;
            })
            .catch(error => {
                console.error('Error sorting data:', error);
                isProcessing = false;
                
                if (tableBody) {
                    const colCount = tableBody.querySelector('tr')?.querySelectorAll('td').length || 6;
                    tableBody.innerHTML = `<tr><td colspan="${colCount}" style="padding:20px; text-align:center; color:#dc3545;">Error: ${error.message}. <a href="${routeUrl}" onclick="window.location.reload(); return false;">Please refresh the page</a>.</td></tr>`;
                }
            });
            
            return false;
        }
        
        function initSorting() {
            const sortLinks = document.querySelectorAll('.sort-link');
            
            // Remove old listeners by cloning nodes
            sortLinks.forEach(link => {
                const newLink = link.cloneNode(true);
                link.parentNode.replaceChild(newLink, link);
            });
            
            // Attach new listeners
            const newSortLinks = document.querySelectorAll('.sort-link');
            newSortLinks.forEach(link => {
                // Use both click and mousedown to catch all events
                link.addEventListener('click', handleSortClick, true); // Use capture phase
                link.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }, true);
            });
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initSorting();
        });
        
        // Also initialize immediately if DOM is already loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSorting);
        } else {
            initSorting();
        }
    })();
</script>
@if(request('print_id'))
<script>
    (function() {
        var printId = {{ request('print_id') }};
        var printUrl = '{{ route("quotations.print", ":id") }}'.replace(':id', printId);
        var printWindow = window.open(printUrl, '_blank');
        
        // The print will be triggered automatically by the export-pdf view's window.onload
        // This ensures the window opens and the print dialog appears
        if (printWindow) {
            // Focus the print window
            printWindow.focus();
        }
    })();
</script>
@endif
@endpush
@endsection

