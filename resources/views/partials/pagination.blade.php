@if($paginator->hasPages())
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; flex-wrap: wrap; gap: 15px;">
        {{-- Page Info --}}
        <div style="display: flex; align-items: center; gap: 10px; color: #666; font-size: 14px;">
            <span>
                Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} results
            </span>
            <span style="color: #999;">|</span>
            <span>
                Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
            </span>
        </div>

        {{-- Pagination Controls --}}
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            {{-- Previous Button --}}
            @if($paginator->onFirstPage())
                <button type="button" disabled style="padding: 8px 12px; background: #e9ecef; color: #6c757d; border: 1px solid #dee2e6; border-radius: 4px; cursor: not-allowed; font-size: 14px;">
                    <i class="fas fa-chevron-left"></i> Previous
                </button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" style="padding: 8px 12px; background: #667eea; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; display: inline-flex; align-items: center; gap: 5px;">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            @endif

            {{-- Page Number Input --}}
            <div style="display: flex; align-items: center; gap: 5px;">
                <label for="page_number_{{ $paginator->currentPage() }}" style="color: #666; font-size: 14px; white-space: nowrap;">Go to page:</label>
                <form method="GET" action="{{ $routeUrl }}" style="display: flex; align-items: center; gap: 5px;" onsubmit="return validatePageNumber(this, {{ $paginator->lastPage() }});">
                    {{-- Preserve all query parameters except page --}}
                    @foreach(request()->except('page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="number" 
                           name="page" 
                           id="page_number_{{ $paginator->currentPage() }}" 
                           value="{{ $paginator->currentPage() }}" 
                           min="1" 
                           max="{{ $paginator->lastPage() }}"
                           style="width: 60px; padding: 6px 8px; border: 1px solid #ddd; border-radius: 4px; text-align: center; font-size: 14px;">
                    <button type="submit" style="padding: 6px 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">
                        Go
                    </button>
                </form>
            </div>

            {{-- Next Button --}}
            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" style="padding: 8px 12px; background: #667eea; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; display: inline-flex; align-items: center; gap: 5px;">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <button type="button" disabled style="padding: 8px 12px; background: #e9ecef; color: #6c757d; border: 1px solid #dee2e6; border-radius: 4px; cursor: not-allowed; font-size: 14px;">
                    Next <i class="fas fa-chevron-right"></i>
                </button>
            @endif
        </div>
    </div>

    <script>
        function validatePageNumber(form, maxPage) {
            const pageInput = form.querySelector('input[name="page"]');
            const pageNumber = parseInt(pageInput.value);
            
            if (isNaN(pageNumber) || pageNumber < 1) {
                alert('Please enter a valid page number (minimum 1)');
                pageInput.focus();
                return false;
            }
            
            if (pageNumber > maxPage) {
                alert('Page number cannot exceed ' + maxPage);
                pageInput.focus();
                return false;
            }
            
            return true;
        }
    </script>
@endif

