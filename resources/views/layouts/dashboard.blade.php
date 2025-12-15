<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard - Woven_ERP')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Figtree', sans-serif;
            background: #f5f5f5;
        }
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            transition: all 0.3s ease;
            z-index: 1000;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar.closed {
            transform: translateX(-100%);
        }
        .sidebar-header {
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            min-height: 60px;
        }
        .sidebar.collapsed .sidebar-header {
            justify-content: center;
            padding: 18px 0;
        }
        .logo {
            font-size: 18px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s;
            flex: 1;
            line-height: 1.2;
            display: flex;
            align-items: center;
        }
        .sidebar.collapsed .logo {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }
        .menu-toggle {
            background: none;
            border: none;
            color: #ffffff !important;
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 5px;
            transition: all 0.3s;
            flex-shrink: 0;
            margin-left: 10px;
        }
        .sidebar.collapsed .menu-toggle {
            margin-left: 0;
            width: 100%;
            justify-content: center;
        }
        .menu-toggle:hover {
            background: rgba(255,255,255,0.1);
        }
        .menu-toggle i {
            color: #ffffff !important;
            display: block;
            line-height: 1;
        }
        .sidebar-menu {
            padding: 8px 0;
            overflow-y: auto;
            overflow-x: hidden;
            flex: 1;
            max-height: calc(100vh - 60px);
        }
        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar-menu::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }
        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }

        /* Simple inline loader for submit buttons */
        .btn-loading-spinner {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #ffffff;
            border-radius: 50%;
            width: 14px;
            height: 14px;
            margin-right: 6px;
            display: inline-block;
            vertical-align: middle;
            animation: btn-spin 0.6s linear infinite;
        }

        @keyframes btn-spin {
            to { transform: rotate(360deg); }
        }
        .menu-item-header {
            padding: 12px 20px;
            font-size: 13px;
            color: #f9fafb;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            user-select: none;
            transition: background 0.3s, color 0.3s;
            background: rgba(255,255,255,0.03);
        }
        .menu-item-header:hover {
            background: rgba(255,255,255,0.12);
            color: #ffffff;
        }
        .menu-item-header .menu-header-icon {
            font-size: 16px;
            margin-right: 8px;
        }
        .menu-item-header .arrow {
            transition: transform 0.3s ease;
            font-size: 10px;
            margin-left: 8px;
        }
        .menu-item-header.collapsed .arrow {
            transform: rotate(-90deg);
        }
        .menu-sub-items {
            overflow: hidden;
            transition: max-height 0.3s ease;
            max-height: 1000px;
        }
        .menu-sub-items.collapsed {
            max-height: 0;
        }
        .menu-item {
            padding: 14px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s;
            position: relative;
            line-height: 1.5;
        }
        .menu-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .menu-item i {
            width: 20px;
            text-align: left;
            font-size: 18px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }
        .menu-item span {
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s;
            line-height: 1.5;
            display: flex;
            align-items: center;
        }
        .sidebar.collapsed .menu-item span {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }
        .sidebar.collapsed .menu-item {
            justify-content: center;
            padding: 14px 0;
            gap: 0;
        }
        /* In collapsed mode: show only the section icon, hide text and arrow */
        .sidebar.collapsed .menu-item-header span {
            display: none;
        }
        .sidebar.collapsed .menu-item-header .arrow {
            display: none;
        }
        .sidebar.collapsed .menu-item-header {
            justify-content: center;
        }
        .sidebar.collapsed .menu-item i {
            justify-content: center;
            text-align: center;
            width: 20px;
            margin: 0 auto;
        }
        .main-content {
            margin-left: 250px;
            flex: 1;
            transition: margin-left 0.3s ease;
        }
        .main-content.expanded {
            margin-left: 0;
        }
        .main-content.sidebar-collapsed {
            margin-left: 70px;
        }
        .top-header {
            background: #2c3e50;
            padding: 15px 30px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .role-badge {
            background: #667eea;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .entity-badge {
            background: #48bb78;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .top-header .user-info {
            color: white;
        }
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }
        .logout-btn:hover {
            background: #c82333;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
            transform: translateY(-1px);
        }
        .logout-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
        }
        .logout-btn i {
            font-size: 16px;
        }
        .content-area {
            padding: 30px;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">Woven_ERP</div>
                <button class="menu-toggle" onclick="toggleSidebar()" title="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <nav class="sidebar-menu">
                <a href="{{ route('dashboard') }}" class="menu-item" title="Dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                
                {{-- Account Settings --}}
                <a href="{{ route('account.change-password') }}" class="menu-item" title="Change Password">
                    <i class="fas fa-user-cog"></i>
                    <span>Account Settings</span>
                </a>
                
                {{-- System Admin Menu (Super Admin only) --}}
                @if(auth()->user()->isSuperAdmin())
                    <div class="menu-item-header" onclick="toggleSystemAdminMenu()" id="systemAdminHeader" style="margin-top: 10px;" title="System Admin">
                        <i class="fas fa-tools menu-header-icon"></i>
                        <span>System Admin</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <div class="menu-sub-items" id="systemAdminMenu">
                        <a href="{{ route('organizations.index') }}" class="menu-item" title="Organizations">
                            <i class="fas fa-building"></i>
                            <span>Organizations</span>
                        </a>
                        
                        {{-- Branches menu item - Hidden for all users including superadmin --}}
                        <a href="{{ route('branches.index') }}" class="menu-item" title="Branches" style="display: none;">
                            <i class="fas fa-sitemap"></i>
                            <span>Branches</span>
                        </a>
                        
                        <a href="{{ route('users.index') }}" class="menu-item" title="Users">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                        
                        <a href="{{ route('roles.index') }}" class="menu-item" title="Roles">
                            <i class="fas fa-user-shield"></i>
                            <span>Roles</span>
                        </a>
                        
                        <a href="{{ route('permissions.index') }}" class="menu-item" title="Permissions">
                            <i class="fas fa-key"></i>
                            <span>Permissions</span>
                        </a>
                        
                        <a href="{{ route('role-permissions.select') }}" class="menu-item" title="Role Permissions">
                            <i class="fas fa-user-lock"></i>
                            <span>Role Permissions</span>
                        </a>
                    </div>
                @endif

                {{-- Settings Menu (Super Admin only) --}}
                @if(auth()->user()->isSuperAdmin())
                     <div class="menu-item-header" onclick="toggleSettingsMenu()" id="settingsHeader" style="margin-top: 10px;" title="Settings">
                         <i class="fas fa-cog menu-header-icon"></i>
                        <span>Settings</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <div class="menu-sub-items" id="settingsMenu">
                        <a href="{{ route('company-information.index') }}" class="menu-item" title="Company Information">
                            <i class="fas fa-building"></i>
                            <span>Company Information</span>
                        </a>
                    </div>
                @endif
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Top Header -->
            <header class="top-header">
                <div class="user-info" style="display: flex; align-items: center; gap: 15px;">
                    
                    @if(auth()->user()->role)
                        <span class="role-badge">{{ auth()->user()->role->name }}</span>
                    @endif
                    
                    @php
                        $user = auth()->user();
                        $activeBranchId = session('active_branch_id');
                        $activeBranchName = session('active_branch_name');
                        // For Super Admin show all active branches; for others show only their active branches
                        $branchesForSelector = $user->isSuperAdmin()
                            ? \App\Models\Branch::where('is_active', true)->get()
                            : $user->branches()->where('is_active', true)->get();
                    @endphp

                    {{-- Branch Selector (top-right) - Hidden for all users including superadmin --}}
                    @if(false && $branchesForSelector->count() > 1)
                        <div style="display: none; position: relative;">
                            <select id="branch-selector" onchange="switchBranch(this.value)" 
                                style="padding: 8px 30px 8px 12px; border-radius: 5px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.2); color: white; font-size: 14px; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\"white\" height=\"20\" viewBox=\"0 0 24 24\" width=\"20\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>'); background-repeat: no-repeat; background-position: right 8px center;">
                                @foreach($branchesForSelector as $branch)
                                    <option value="{{ $branch->id }}" {{ $activeBranchId == $branch->id ? 'selected' : '' }} style="background-color: #2c3e50; color: white;">
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @elseif(false && $activeBranchName)
                        <span class="entity-badge" style="display: none; background: #f59e0b;">{{ $activeBranchName }}</span>
                    @endif
                    
                    <button class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </header>
            
            <script>
                function switchBranch(branchId) {
                    if (branchId) {
                        window.location.href = '{{ url("/branches") }}/' + branchId + '/switch';
                    }
                }
            </script>

            <!-- Content Area -->
            <main class="content-area">
                @if(session('success'))
                    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.querySelector('.menu-toggle i');
            
            // Toggle collapsed state (show icons only)
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
            
            // Update toggle icon based on state
            if (sidebar.classList.contains('collapsed')) {
                toggleIcon.classList.remove('fa-bars');
                toggleIcon.classList.add('fa-chevron-right');
            } else {
                toggleIcon.classList.remove('fa-chevron-right');
                toggleIcon.classList.add('fa-bars');
            }
            
            // Remove closed class if present (for mobile)
            sidebar.classList.remove('closed');
            mainContent.classList.remove('expanded');
        }
        
        // Handle mobile view (and restore sidebar when back to desktop)
        function handleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile) {
                sidebar.classList.add('closed');
                sidebar.classList.remove('collapsed');
                mainContent.classList.add('expanded');
                mainContent.classList.remove('sidebar-collapsed');
            } else {
                // On desktop widths always show sidebar (unless user manually collapses it)
                sidebar.classList.remove('closed');
                mainContent.classList.remove('expanded');
            }
        }
        
        // Check on load and resize
        window.addEventListener('load', handleMobileSidebar);
        window.addEventListener('resize', handleMobileSidebar);


        // Toggle Settings menu
        function toggleSettingsMenu() {
            const settingsMenu = document.getElementById('settingsMenu');
            const settingsHeader = document.getElementById('settingsHeader');
            
            if (settingsMenu && settingsHeader) {
                settingsMenu.classList.toggle('collapsed');
                settingsHeader.classList.toggle('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('settingsMenuCollapsed', settingsMenu.classList.contains('collapsed'));
            }
        }

        // Toggle System Admin menu
        function toggleSystemAdminMenu() {
            const systemAdminMenu = document.getElementById('systemAdminMenu');
            const systemAdminHeader = document.getElementById('systemAdminHeader');
            
            if (systemAdminMenu && systemAdminHeader) {
                systemAdminMenu.classList.toggle('collapsed');
                systemAdminHeader.classList.toggle('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('systemAdminMenuCollapsed', systemAdminMenu.classList.contains('collapsed'));
            }
        }

        // Initialize all collapsible menus state on page load
        document.addEventListener('DOMContentLoaded', function() {

            // Settings menu
            const settingsSavedState = localStorage.getItem('settingsMenuCollapsed');
            if (settingsSavedState === 'true') {
                const settingsMenu = document.getElementById('settingsMenu');
                const settingsHeader = document.getElementById('settingsHeader');
                if (settingsMenu && settingsHeader) {
                    settingsMenu.classList.add('collapsed');
                    settingsHeader.classList.add('collapsed');
                }
            }

            // System Admin menu
            const systemAdminSavedState = localStorage.getItem('systemAdminMenuCollapsed');
            if (systemAdminSavedState === 'true') {
                const systemAdminMenu = document.getElementById('systemAdminMenu');
                const systemAdminHeader = document.getElementById('systemAdminHeader');
                if (systemAdminMenu && systemAdminHeader) {
                    systemAdminMenu.classList.add('collapsed');
                    systemAdminHeader.classList.add('collapsed');
                }
            }

            // Restore sidebar scroll position so it doesn't jump to top on navigation
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                const savedScroll = localStorage.getItem('sidebarScrollTop');
                if (savedScroll !== null) {
                    sidebar.scrollTop = parseInt(savedScroll, 10) || 0;
                }
                
                // Persist scroll position while user scrolls
                sidebar.addEventListener('scroll', function () {
                    localStorage.setItem('sidebarScrollTop', sidebar.scrollTop);
                });
            }

            // Global form submit loader to prevent double submits and show progress
            document.querySelectorAll('form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    // Prevent double submission
                    if (form.dataset.submitting === 'true') {
                        e.preventDefault();
                        return;
                    }
                    form.dataset.submitting = 'true';

                    const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                    submitButtons.forEach(function (btn) {
                        // Skip if already processed
                        if (btn.dataset.loadingApplied === 'true') {
                            return;
                        }
                        btn.dataset.loadingApplied = 'true';
                        btn.disabled = true;

                        if (btn.tagName === 'BUTTON') {
                            btn.dataset.originalHtml = btn.innerHTML;
                            btn.innerHTML = '<span class="btn-loading-spinner"></span>Submitting...';
                        } else if (btn.tagName === 'INPUT') {
                            btn.dataset.originalValue = btn.value;
                            btn.value = 'Submitting...';
                        }
                    });
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>

