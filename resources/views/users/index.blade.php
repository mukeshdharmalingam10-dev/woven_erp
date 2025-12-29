@extends('layouts.dashboard')

@section('title', 'Users - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Users</h2>
        <a href="{{ route('users.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Add New User
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <div style="margin-bottom: 10px;">
                <strong><i class="fas fa-check-circle"></i> {{ session('success') }}</strong>
            </div>
            @if(session('user_password') && session('user_email'))
                <div style="background: #fff; padding: 15px; border-radius: 5px; margin-top: 10px; border: 1px solid #c3e6cb;">
                    <strong style="color: #004085; display: block; margin-bottom: 10px;">
                        <i class="fas fa-key"></i> User Login Credentials (Please share these externally):
                    </strong>
                    <div style="display: grid; grid-template-columns: auto 1fr; gap: 10px 15px; font-size: 14px;">
                        <strong style="color: #666;">Name:</strong>
                        <span style="color: #333;">{{ session('user_name') }}</span>
                        <strong style="color: #666;">Email:</strong>
                        <span style="color: #333; font-family: monospace;">{{ session('user_email') }}</span>
                        <strong style="color: #666;">Password:</strong>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span id="userPassword" style="color: #333; font-family: monospace; font-weight: bold; font-size: 16px; letter-spacing: 1px;">{{ session('user_password') }}</span>
                            <button type="button" onclick="copyPassword()" style="padding: 6px 12px; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #c3e6cb; color: #856404; font-size: 12px;">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> Please share these credentials with the user externally. Email will not be sent automatically.
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if($users->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; white-space:nowrap;">
                            <span style="display:inline-flex; align-items:center; gap:5px;">
                                S.No
                                @php
                                    $currentSortBy = request('sort_by', 'id');
                                    $currentSortOrder = request('sort_order', 'desc');
                                    $newSortOrder = ($currentSortBy == 'id' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                @endphp
                                <a href="#" class="sort-link" data-sort-by="id" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                    @if($currentSortBy == 'id')
                                        @if($currentSortOrder == 'desc')
                                            <i class="fas fa-sort-down"></i>
                                        @else
                                            <i class="fas fa-sort-up"></i>
                                        @endif
                                    @else
                                        <i class="fas fa-sort" style="opacity:0.3;"></i>
                                    @endif
                                </a>
                            </span>
                        </th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; white-space:nowrap;">
                            <span style="display:inline-flex; align-items:center; gap:5px;">
                                Name
                                @php
                                    $newSortOrder = ($currentSortBy == 'name' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                @endphp
                                <a href="#" class="sort-link" data-sort-by="name" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                    @if($currentSortBy == 'name')
                                        @if($currentSortOrder == 'desc')
                                            <i class="fas fa-sort-down"></i>
                                        @else
                                            <i class="fas fa-sort-up"></i>
                                        @endif
                                    @else
                                        <i class="fas fa-sort" style="opacity:0.3;"></i>
                                    @endif
                                </a>
                            </span>
                        </th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; white-space:nowrap;">
                            <span style="display:inline-flex; align-items:center; gap:5px;">
                                Email
                                @php
                                    $newSortOrder = ($currentSortBy == 'email' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                @endphp
                                <a href="#" class="sort-link" data-sort-by="email" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                    @if($currentSortBy == 'email')
                                        @if($currentSortOrder == 'desc')
                                            <i class="fas fa-sort-down"></i>
                                        @else
                                            <i class="fas fa-sort-up"></i>
                                        @endif
                                    @else
                                        <i class="fas fa-sort" style="opacity:0.3;"></i>
                                    @endif
                                </a>
                            </span>
                        </th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Mobile</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; white-space:nowrap;">
                            <span style="display:inline-flex; align-items:center; gap:5px;">
                                Role
                                @php
                                    $newSortOrder = ($currentSortBy == 'role' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                @endphp
                                <a href="#" class="sort-link" data-sort-by="role" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                    @if($currentSortBy == 'role')
                                        @if($currentSortOrder == 'desc')
                                            <i class="fas fa-sort-down"></i>
                                        @else
                                            <i class="fas fa-sort-up"></i>
                                        @endif
                                    @else
                                        <i class="fas fa-sort" style="opacity:0.3;"></i>
                                    @endif
                                </a>
                            </span>
                        </th>
                        <th style="display: none; padding: 12px; text-align: left; color: #333; font-weight: 600;">Branches</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Status</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $user->name }}</td>
                            <td style="padding: 12px; color: #666;">{{ $user->email }}</td>
                            <td style="padding: 12px; color: #666;">{{ $user->mobile ?? 'N/A' }}</td>
                            <td style="padding: 12px;">
                                @if($user->role)
                                    <span style="background: #667eea; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                                        {{ $user->role->name }}
                                    </span>
                                @else
                                    <span style="color: #999;">No Role</span>
                                @endif
                            </td>
                            <td style="display: none; padding: 12px;">
                                @if($user->branches && $user->branches->count() > 0)
                                    <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                                        @foreach($user->branches as $branch)
                                            <span style="background: #f59e0b; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                                                {{ $branch->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color: #999;">No Branches</span>
                                @endif
                            </td>
                            <td style="padding: 12px;">
                                @php
                                    $status = $user->status ?? 'inactive';
                                    $bg = '#6c757d';
                                    $color = '#fff';
                                    if ($status === 'active') {
                                        $bg = '#28a745';
                                    } elseif ($status === 'locked') {
                                        $bg = '#e53e3e';
                                    }
                                @endphp
                                <span style="background: {{ $bg }}; color: {{ $color }}; padding: 4px 12px; border-radius: 12px; font-size: 12px; text-transform: capitalize;">
                                    {{ $status }}
                                </span>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('users.show', $user->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                        View
                                    </a>
                                    <a href="{{ route('users.edit', $user->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                        Edit
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;" id="pagination-container">
            @include('partials.pagination', ['paginator' => $users, 'routeUrl' => route('users.index')])
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No users found.</p>
            <a href="{{ route('users.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Create First User
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function copyPassword() {
        const password = document.getElementById('userPassword').textContent;
        navigator.clipboard.writeText(password).then(function() {
            alert('Password copied to clipboard!');
        }, function(err) {
            const textArea = document.createElement('textarea');
            textArea.value = password;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            alert('Password copied to clipboard!');
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const sortLinks = document.querySelectorAll('.sort-link');
        const tableBody = document.querySelector('table tbody');
        const paginationContainer = document.getElementById('pagination-container');
        
        sortLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const sortBy = this.getAttribute('data-sort-by');
                const sortOrder = this.getAttribute('data-sort-order');
                
                if (tableBody) {
                    tableBody.innerHTML = '<tr><td colspan="8" style="padding:20px; text-align:center; color:#666;"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
                }
                
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('sort_by', sortBy);
                urlParams.set('sort_order', sortOrder);
                
                fetch('{{ route("users.index") }}?' + urlParams.toString(), {
                    method: 'GET',
                    headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html'},
                    credentials: 'same-origin'
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTableBody = doc.querySelector('table tbody');
                    const newPagination = doc.querySelector('#pagination-container') || doc.querySelector('[style*="margin-top:20px"]');
                    
                    if (newTableBody && tableBody) tableBody.innerHTML = newTableBody.innerHTML;
                    if (newPagination && paginationContainer) paginationContainer.innerHTML = newPagination.innerHTML;
                    
                    window.history.pushState({}, '', '{{ route("users.index") }}?' + urlParams.toString());
                    updateSortIcons(sortBy, sortOrder);
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (tableBody) tableBody.innerHTML = '<tr><td colspan="8" style="padding:20px; text-align:center; color:#dc3545;">Error loading data.</td></tr>';
                });
            });
        });
        
        function updateSortIcons(activeSortBy, activeSortOrder) {
            sortLinks.forEach(link => {
                const sortBy = link.getAttribute('data-sort-by');
                const icon = link.querySelector('i');
                if (sortBy === activeSortBy) {
                    icon.className = activeSortOrder === 'desc' ? 'fas fa-sort-down' : 'fas fa-sort-up';
                    icon.style.opacity = '1';
                    link.setAttribute('data-sort-order', activeSortOrder === 'desc' ? 'asc' : 'desc');
                } else {
                    icon.className = 'fas fa-sort';
                    icon.style.opacity = '0.3';
                }
            });
        }
    });
</script>
@endpush

