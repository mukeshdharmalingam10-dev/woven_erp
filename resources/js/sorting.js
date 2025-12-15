// Reusable AJAX sorting functionality for data tables
function initTableSorting(routeName, tableSelector = 'table', paginationSelector = '#pagination-container') {
    document.addEventListener('DOMContentLoaded', function() {
        const sortLinks = document.querySelectorAll('.sort-link');
        const tableBody = document.querySelector(tableSelector + ' tbody');
        const paginationContainer = document.querySelector(paginationSelector);
        
        sortLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const sortBy = this.getAttribute('data-sort-by');
                const sortOrder = this.getAttribute('data-sort-order');
                
                if (tableBody) {
                    const colCount = tableBody.querySelector('tr')?.querySelectorAll('td').length || 1;
                    tableBody.innerHTML = `<tr><td colspan="${colCount}" style="padding:20px; text-align:center; color:#666;"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>`;
                }
                
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('sort_by', sortBy);
                urlParams.set('sort_order', sortOrder);
                
                fetch(routeName + '?' + urlParams.toString(), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newTableBody = doc.querySelector(tableSelector + ' tbody');
                    const newPagination = doc.querySelector(paginationSelector) || doc.querySelector('[style*="margin-top"]');
                    
                    if (newTableBody && tableBody) {
                        tableBody.innerHTML = newTableBody.innerHTML;
                    }
                    
                    if (newPagination && paginationContainer) {
                        paginationContainer.innerHTML = newPagination.innerHTML;
                    }
                    
                    window.history.pushState({}, '', routeName + '?' + urlParams.toString());
                    updateSortIcons(sortBy, sortOrder);
                })
                .catch(error => {
                    console.error('Error sorting data:', error);
                    if (tableBody) {
                        const colCount = tableBody.querySelector('tr')?.querySelectorAll('td').length || 1;
                        tableBody.innerHTML = `<tr><td colspan="${colCount}" style="padding:20px; text-align:center; color:#dc3545;">Error loading data. Please refresh the page.</td></tr>`;
                    }
                });
            });
        });
        
        function updateSortIcons(activeSortBy, activeSortOrder) {
            sortLinks.forEach(link => {
                const sortBy = link.getAttribute('data-sort-by');
                const icon = link.querySelector('i');
                
                if (sortBy === activeSortBy) {
                    if (activeSortOrder === 'desc') {
                        icon.className = 'fas fa-sort-down';
                    } else {
                        icon.className = 'fas fa-sort-up';
                    }
                    icon.style.opacity = '1';
                    link.setAttribute('data-sort-order', activeSortOrder === 'desc' ? 'asc' : 'desc');
                } else {
                    icon.className = 'fas fa-sort';
                    icon.style.opacity = '0.3';
                }
            });
        }
    });
}

