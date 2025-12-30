<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard - ' . ($companyName ?? 'Woven_ERP'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0d6efd">
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
            background: linear-gradient(180deg, #1a1f35 0%, #1e2339 50%, #1a1f35 100%);
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
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.3);
            border-right: 1px solid rgba(102, 126, 234, 0.2);
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
            border-bottom: 1px solid rgba(102, 126, 234, 0.3);
            min-height: 60px;
            background: rgba(0, 0, 0, 0.2);
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
            padding: 14px 20px;
            font-size: 12px;
            color: #b8c5d1;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            user-select: none;
            transition: all 0.3s ease;
            background: rgba(102, 126, 234, 0.08);
            border-left: 3px solid transparent;
            margin: 4px 8px;
            border-radius: 8px;
            position: relative;
        }
        .menu-item-header:hover {
            background: rgba(102, 126, 234, 0.15);
            color: #ffffff;
            border-left-color: #667eea;
            transform: translateX(2px);
        }
        .menu-item-header.active {
            background: rgba(102, 126, 234, 0.2);
            color: #ffffff;
            border-left-color: #667eea;
        }
        .menu-item-header .menu-header-icon {
            font-size: 16px;
            margin-right: 10px;
            color: #667eea;
            transition: all 0.3s ease;
        }
        .menu-item-header:hover .menu-header-icon {
            color: #ffffff;
            transform: scale(1.1);
        }
        .menu-item-header .arrow {
            transition: transform 0.3s ease;
            font-size: 11px;
            margin-left: auto;
            color: #a0aec0;
        }
        .menu-item-header:hover .arrow {
            color: #ffffff;
        }
        .menu-item-header.collapsed .arrow {
            transform: rotate(-90deg);
        }
        .menu-sub-items {
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            max-height: 1000px;
            background: rgba(102, 126, 234, 0.05);
            margin: 0 8px 4px 8px;
            border-radius: 8px;
            padding: 4px 0;
            border-left: 2px solid rgba(102, 126, 234, 0.2);
        }
        .menu-sub-items.collapsed {
            max-height: 0;
            padding: 0;
            margin: 0 8px;
        }
        .menu-item {
            padding: 12px 20px 12px 45px;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            position: relative;
            line-height: 1.5;
            font-size: 14px;
            font-weight: 500;
            border-left: 2px solid transparent;
            margin: 2px 4px;
            border-radius: 6px;
        }
        .menu-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #667eea;
            transform: scaleY(0);
            transition: transform 0.3s ease;
            border-radius: 0 3px 3px 0;
        }
        .menu-item:hover {
            background: rgba(102, 126, 234, 0.2);
            color: #ffffff;
            border-left-color: #667eea;
            transform: translateX(4px);
            padding-left: 48px;
        }
        .menu-item:hover::before {
            transform: scaleY(1);
        }
        .menu-item.active {
            background: rgba(102, 126, 234, 0.25);
            color: #ffffff;
            border-left-color: #667eea;
        }
        .menu-item.active::before {
            transform: scaleY(1);
        }
        .menu-item i {
            width: 20px;
            text-align: left;
            font-size: 16px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            transition: all 0.3s ease;
        }
        .menu-item:hover i {
            color: #667eea;
            transform: scale(1.1);
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
        /* Additional styling for sub-menu items */
        .menu-sub-items .menu-item,
        .menu-submenu .menu-item {
            padding-left: 45px;
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
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .top-header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .top-header-right {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: auto;
        }
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: #ffffff;
            font-size: 22px;
            cursor: pointer;
            padding: 8px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .mobile-menu-toggle:hover {
            background: rgba(255,255,255,0.1);
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        .sidebar-overlay.active {
            display: block;
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
                width: 280px;
                box-shadow: 2px 0 10px rgba(0,0,0,0.3);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .menu-item-header {
                margin: 4px 4px;
                padding: 12px 16px;
            }
            .menu-sub-items {
                margin: 0 4px 4px 4px;
            }
            .menu-item {
                padding: 12px 16px 12px 40px;
                margin: 2px 2px;
            }
            .menu-item:hover {
                padding-left: 43px;
            }
            .sidebar.collapsed {
                width: 280px;
            }
            .main-content {
                margin-left: 0;
            }
            .mobile-menu-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            .sidebar-header .menu-toggle {
                display: flex;
            }
            .content-area {
                padding: 15px;
            }
            .top-header {
                padding: 12px 15px;
            }
            .role-badge, .logout-btn span {
                font-size: 12px;
                padding: 6px 12px;
            }
        }
        @media (min-width: 769px) {
            .mobile-menu-toggle {
                display: none !important;
            }
            .sidebar-overlay {
                display: none !important;
            }
        }
    </style>
    @stack('styles')
    
    <!-- Global Styles for Number Input Fields -->
    <style>
        /* Hide spinner arrows in number input fields across all forms */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        /* Firefox */
        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleMobileSidebar()"></div>
        
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">{{ $companyName ?? 'Woven_ERP' }}</div>
                <button class="menu-toggle" id="sidebarToggleBtn" onclick="handleSidebarToggle()" title="Toggle Sidebar">
                    <i class="fas fa-bars" id="sidebarToggleIcon"></i>
                </button>
            </div>
            <nav class="sidebar-menu">
                @php
                    // Ensure user roles and permissions are loaded for menu checks
                    $user = auth()->user();
                    if ($user) {
                        // Always reload roles with permissions to ensure fresh data
                        $user->load('roles.permissions');
                    }
                @endphp
                
                {{-- Dashboard - Always visible --}}
                <a href="{{ route('dashboard') }}" class="menu-item" title="Dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                
                {{-- Account Settings - Always visible --}}
                <a href="{{ route('account.change-password') }}" class="menu-item" title="Change Password">
                    <i class="fas fa-user-cog"></i>
                    <span>Account Settings</span>
                </a>
                
                {{-- System Admin Menu (Super Admin only) --}}
                @php
                    $hasSystemAdminAccess = $user->isSuperAdmin() && (
                        $user->canAccessPage('organizations.index') ||
                        $user->canAccessPage('users.index') ||
                        $user->canAccessPage('roles.index') ||
                        $user->canAccessPage('permissions.index') ||
                        $user->canAccessPage('role-permissions.select')
                    );
                @endphp
                @if($hasSystemAdminAccess)
                    <div class="menu-item-header" onclick="toggleSystemAdminMenu()" id="systemAdminHeader" style="margin-top: 10px;" title="System Admin">
                        <i class="fas fa-tools menu-header-icon"></i>
                        <span>System Admin</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <div class="menu-sub-items" id="systemAdminMenu">
                        @if($user->canAccessPage('organizations.index'))
                        <a href="{{ route('organizations.index') }}" class="menu-item" title="Organizations">
                            <i class="fas fa-building"></i>
                            <span>Organizations</span>
                        </a>
                        @endif
                        
                        {{-- Branches menu item - Hidden for all users including superadmin --}}
                        <a href="{{ route('branches.index') }}" class="menu-item" title="Branches" style="display: none;">
                            <i class="fas fa-sitemap"></i>
                            <span>Branches</span>
                        </a>
                        
                        @if($user->canAccessPage('users.index'))
                        <a href="{{ route('users.index') }}" class="menu-item" title="Users">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                        @endif
                        
                        @if($user->canAccessPage('roles.index'))
                        <a href="{{ route('roles.index') }}" class="menu-item" title="Roles">
                            <i class="fas fa-user-shield"></i>
                            <span>Roles</span>
                        </a>
                        @endif
                        
                        @if($user->canAccessPage('permissions.index'))
                        <a href="{{ route('permissions.index') }}" class="menu-item" title="Permissions">
                            <i class="fas fa-key"></i>
                            <span>Permissions</span>
                        </a>
                        @endif
                        
                        @if($user->canAccessPage('role-permissions.select'))
                        <a href="{{ route('role-permissions.select') }}" class="menu-item" title="Role Permissions">
                            <i class="fas fa-user-lock"></i>
                            <span>Role Permissions</span>
                        </a>
                        @endif
                    </div>
                @endif

                {{-- Masters Menu --}}
                @php
                    $hasMastersAccess = $user->canAccessPage('suppliers.index') ||
                        $user->canAccessPage('raw-materials.index') ||
                        $user->canAccessPage('products.index') ||
                        $user->canAccessPage('customers.index') ||
                        $user->canAccessPage('employees.index');
                @endphp
                @if($hasMastersAccess)
                <div class="menu-item-header" onclick="toggleMastersMenu()" id="mastersHeader" style="margin-top: 10px;" title="Masters">
                    <i class="fas fa-database menu-header-icon"></i>
                    <span>Masters</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <div class="menu-sub-items" id="mastersMenu">
                        @if($user->canAccessPage('suppliers.index'))
                    <a href="{{ route('suppliers.index') }}" class="menu-item" title="Suppliers">
                        <i class="fas fa-truck"></i>
                        <span>Suppliers</span>
                    </a>
                        @endif
                        @if($user->canAccessPage('raw-materials.index'))
                    <a href="{{ route('raw-materials.index') }}" class="menu-item" title="Raw Materials">
                        <i class="fas fa-boxes"></i>
                        <span>Raw Materials</span>
                    </a>
                        @endif
                        @if($user->canAccessPage('products.index'))
                    <a href="{{ route('products.index') }}" class="menu-item" title="Products">
                        <i class="fas fa-cube"></i>
                        <span>Products</span>
                    </a>
                        @endif
                        @if($user->canAccessPage('customers.index'))
                    <a href="{{ route('customers.index') }}" class="menu-item" title="Customers">
                        <i class="fas fa-users"></i>
                        <span>Customers</span>
                    </a>
                        @endif
                        @if($user->canAccessPage('employees.index'))
                    <a href="{{ route('employees.index') }}" class="menu-item" title="Employees">
                        <i class="fas fa-user-tie"></i>
                        <span>Employees</span>
                    </a>
                        @endif
                </div>
                @endif

                {{-- Daily Expense Master Menu --}}
                @php
                    $hasPettyCashMasterAccess = $user->canAccessPage('petty-cash.index');
                @endphp
                @if($hasPettyCashMasterAccess)
                <div class="menu-item-header" onclick="togglePettyCashMasterMenu()" id="pettyCashMasterHeader" style="margin-top: 10px;" title="Daily Expense Master">
                    <i class="fas fa-wallet menu-header-icon"></i>
                    <span>Daily Expense Master</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <div class="menu-sub-items" id="pettyCashMasterMenu">
                        @if($user->canAccessPage('petty-cash.index'))
                    <a href="{{ route('petty-cash.index') }}" class="menu-item" title="Daily Expense Form">
                        <i class="fas fa-file-alt"></i>
                        <span>Daily Expense Form</span>
                    </a>
                        @endif
                        @if($user->canAccessPage('petty-cash.index'))
                    <a href="{{ route('petty-cash.report') }}" class="menu-item" title="Daily Expense Report">
                        <i class="fas fa-chart-line"></i>
                        <span>Daily Expense Report</span>
                    </a>
                        @endif
                </div>
                @endif

                {{-- Attendance Menu --}}
                @php
                    $hasAttendanceAccess = $user->canAccessPage('attendances.index');
                @endphp
                @if($hasAttendanceAccess)
                <div class="menu-item-header" onclick="toggleAttendanceMenu()" id="attendanceHeader" style="margin-top: 10px;" title="Attendance">
                    <i class="fas fa-calendar-check menu-header-icon"></i>
                    <span>Attendance</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <div class="menu-sub-items" id="attendanceMenu">
                        @if($user->canAccessPage('attendances.index'))
                    <a href="{{ route('attendances.index') }}" class="menu-item" title="Attendance Form">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Attendance Form</span>
                    </a>
                        @endif
                        @if($user->canAccessPage('leaves.index'))
                    <a href="{{ route('leaves.index') }}" class="menu-item" title="Leave Form">
                        <i class="fas fa-calendar-times"></i>
                        <span>Leave Form</span>
                    </a>
                        @endif
                        @if($user->canAccessPage('attendances.index'))
                    <a href="{{ route('attendances.report') }}" class="menu-item" title="Attendance Report">
                        <i class="fas fa-chart-bar"></i>
                        <span>Attendance Report</span>
                    </a>
                        @endif
                </div>
                @endif

                {{-- Transaction Forms (Direct Menu Items) --}}
                @if($user->canAccessPage('purchase-orders.index'))
                <a href="{{ route('purchase-orders.index') }}" class="menu-item" title="Purchase Orders" style="margin-top: 10px;">
                    <i class="fas fa-file-invoice"></i>
                    <span>Purchase Orders</span>
                </a>
                @endif
                @if($user->canAccessPage('material-inwards.index'))
                <a href="{{ route('material-inwards.index') }}" class="menu-item" title="Material Inwards">
                    <i class="fas fa-arrow-down"></i>
                    <span>Material Inwards</span>
                </a>
                @endif
                @if($user->canAccessPage('sales-invoices.index'))
                <a href="{{ route('sales-invoices.index') }}" class="menu-item" title="Sales Invoices">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Sales Invoices</span>
                </a>
                @endif
                @if($user->canAccessPage('debit-notes.index'))
                <a href="{{ route('debit-notes.index') }}" class="menu-item" title="Debit Notes">
                    <i class="fas fa-file-invoice"></i>
                    <span>Debit Notes</span>
                </a>
                @endif
                @if($user->canAccessPage('credit-notes.index'))
                <a href="{{ route('credit-notes.index') }}" class="menu-item" title="Credit Notes">
                    <i class="fas fa-file-invoice"></i>
                    <span>Credit Notes</span>
                </a>
                @endif
                @if($user->canAccessPage('quotations.index'))
                <a href="{{ route('quotations.index') }}" class="menu-item" title="Quotations">
                    <i class="fas fa-file-contract"></i>
                    <span>Quotations</span>
                </a>
                @endif
                @if($user->canAccessPage('payment-trackings.index'))
                <a href="{{ route('payment-trackings.index') }}" class="menu-item" title="Payment Tracking">
                    <i class="fas fa-money-check-alt"></i>
                    <span>Payment Tracking</span>
                </a>
                @endif
                @if($user->canAccessPage('salary-masters.salary-setup.index'))
                <a href="{{ route('salary-masters.index') }}" class="menu-item" title="Salary Master">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Salary Master</span>
                </a>
                @endif
                {{-- Store Menu --}}
                @php
                    $hasStockAccess = $user->canAccessPage('stock-transactions.index');
                @endphp
                @if($hasStockAccess)
                <div class="menu-item-header" onclick="toggleStockMenu()" id="stockHeader" style="margin-top: 10px;" title="Store">
                    <i class="fas fa-boxes menu-header-icon"></i>
                    <span>Store</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <div class="menu-sub-items" id="stockMenu">
                        @if($user->canAccessPage('raw-materials.index'))
                    <a href="{{ route('stock.raw-material') }}" class="menu-item" title="Raw Material Stock">
                        <i class="fas fa-boxes"></i>
                        <span>Raw Material Stock</span>
                    </a>
                        @endif
                        @if($user->canAccessPage('products.index'))
                    <a href="{{ route('stock.finished-goods') }}" class="menu-item" title="Finished Goods Stock">
                        <i class="fas fa-warehouse"></i>
                        <span>Finished Goods Stock</span>
                    </a>
                        @endif
                        @if($user->canAccessPage('stock-transactions.index'))
                    <a href="{{ route('stock-transactions.index') }}" class="menu-item" title="Stock Transactions">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Stock Transactions</span>
                    </a>
                        @endif
                </div>
                @endif

                {{-- Productions Menu --}}
                @php
                    $hasProductionsAccess = $user->canAccessPage('work-orders.index') ||
                        $user->canAccessPage('productions.index');
                @endphp
                @if($hasProductionsAccess)
                <div class="menu-item-header" onclick="toggleProductionsMenu()" id="productionsHeader" style="margin-top: 10px;" title="Productions">
                    <i class="fas fa-industry menu-header-icon"></i>
                    <span>Productions</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <div class="menu-sub-items" id="productionsMenu">
                        @if($user->canAccessPage('work-orders.index'))
                    <a href="{{ route('work-orders.index') }}" class="menu-item" title="Work Orders">
                        <i class="fas fa-tasks"></i>
                        <span>Work Orders</span>
                    </a>
                        @endif
                        @if($user->canAccessPage('productions.index'))
                    <a href="{{ route('productions.index') }}" class="menu-item" title="Productions">
                        <i class="fas fa-industry"></i>
                        <span>Productions</span>
                    </a>
                        @endif
                </div>
                @endif

                {{-- CRM Menu --}}
                @php
                    $hasCrmAccess = $user->canAccessPage('notes.index') ||
                        $user->canAccessPage('tasks.index');
                @endphp
                @if($hasCrmAccess)
                <div class="menu-item-header" onclick="toggleCrmMenu()" id="crmHeader" style="margin-top: 10px;" title="CRM">
                    <i class="fas fa-users menu-header-icon"></i>
                    <span>CRM</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <div class="menu-submenu" id="crmMenu" style="display: none;">
                        @if($user->canAccessPage('notes.index'))
                    <a href="{{ route('notes.index') }}" class="menu-item" title="Notes">
                        <i class="fas fa-sticky-note"></i>
                        <span>Notes</span>
                    </a>
                        @endif
                        @if($user->canAccessPage('tasks.index'))
                    <a href="{{ route('tasks.index') }}" class="menu-item" title="Tasks">
                        <i class="fas fa-tasks"></i>
                        <span>Tasks</span>
                    </a>
                        @endif
                </div>
                @endif

                {{-- Settings Menu (Super Admin only) --}}
                @php
                    $hasSettingsAccess = $user->isSuperAdmin() && $user->canAccessPage('company-information.index');
                @endphp
                @if($hasSettingsAccess)
                     <div class="menu-item-header" onclick="toggleSettingsMenu()" id="settingsHeader" style="margin-top: 10px;" title="Settings">
                         <i class="fas fa-cog menu-header-icon"></i>
                        <span>Settings</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <div class="menu-sub-items" id="settingsMenu">
                        @if($user->canAccessPage('company-information.index'))
                        <a href="{{ route('company-information.index') }}" class="menu-item" title="Company Information">
                            <i class="fas fa-building"></i>
                            <span>Company Information</span>
                        </a>
                        @endif
                    </div>
                @endif
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Top Header -->
            <header class="top-header">
                <div class="top-header-left">
                <button class="mobile-menu-toggle" onclick="toggleMobileSidebar()" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
                </div>
                <div class="top-header-right">
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
        // Handle sidebar toggle - different behavior for mobile vs desktop
        function handleSidebarToggle() {
            const isMobile = window.innerWidth <= 768;
            if (isMobile) {
                toggleMobileSidebar();
            } else {
                toggleSidebar();
            }
        }
        
        // Desktop: Toggle collapsed state (show icons only)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('sidebarToggleIcon');
            
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
        }
        
        // Mobile: Toggle drawer (open/close)
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleIcon = document.getElementById('sidebarToggleIcon');
            const mobileToggleIcon = document.querySelector('.mobile-menu-toggle i');
            const isMobile = window.innerWidth <= 768;

            if (!isMobile) {
                return;
            }

            if (sidebar.classList.contains('closed') || !sidebar.classList.contains('open')) {
                // Open drawer
                sidebar.classList.remove('closed');
                sidebar.classList.add('open');
                mainContent.classList.remove('expanded');
                if (overlay) overlay.classList.add('active');
                // Change sidebar toggle icon to arrow/close
                if (toggleIcon) {
                    toggleIcon.classList.remove('fa-bars');
                    toggleIcon.classList.add('fa-times');
                }
            } else {
                // Close drawer
                sidebar.classList.add('closed');
                sidebar.classList.remove('open');
                mainContent.classList.add('expanded');
                if (overlay) overlay.classList.remove('active');
                // Change sidebar toggle icon back to hamburger
                if (toggleIcon) {
                    toggleIcon.classList.remove('fa-times');
                    toggleIcon.classList.add('fa-bars');
                }
            }
        }
        
        // Handle mobile view (and restore sidebar when back to desktop)
        function handleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const overlay = document.getElementById('sidebarOverlay');
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile) {
                // On mobile: start with drawer closed
                if (!sidebar.classList.contains('open')) {
                    sidebar.classList.add('closed');
                    sidebar.classList.remove('collapsed');
                    sidebar.classList.remove('open');
                    mainContent.classList.add('expanded');
                    if (overlay) overlay.classList.remove('active');
                }
            } else {
                // On desktop: show sidebar normally, remove mobile classes
                sidebar.classList.remove('closed');
                sidebar.classList.remove('open');
                mainContent.classList.remove('expanded');
                if (overlay) overlay.classList.remove('active');
            }
        }
        
        // Check on load and resize
        window.addEventListener('load', handleMobileSidebar);
        window.addEventListener('resize', handleMobileSidebar);


        // Toggle Masters menu
        function toggleMastersMenu() {
            const mastersMenu = document.getElementById('mastersMenu');
            const mastersHeader = document.getElementById('mastersHeader');
            
            if (mastersMenu && mastersHeader) {
                mastersMenu.classList.toggle('collapsed');
                mastersHeader.classList.toggle('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('mastersMenuCollapsed', mastersMenu.classList.contains('collapsed'));
            }
        }

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

        // Toggle Transactions menu
        function toggleCrmMenu() {
            const menu = document.getElementById('crmMenu');
            const header = document.getElementById('crmHeader');
            const arrow = header.querySelector('.arrow');
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
                arrow.classList.remove('fa-chevron-down');
                arrow.classList.add('fa-chevron-up');
                localStorage.setItem('crmMenuOpen', 'true');
            } else {
                menu.style.display = 'none';
                arrow.classList.remove('fa-chevron-up');
                arrow.classList.add('fa-chevron-down');
                localStorage.setItem('crmMenuOpen', 'false');
            }
        }

        function toggleProductionsMenu() {
            const productionsMenu = document.getElementById('productionsMenu');
            const productionsHeader = document.getElementById('productionsHeader');
            
            if (productionsMenu && productionsHeader) {
                productionsMenu.classList.toggle('collapsed');
                productionsHeader.classList.toggle('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('productionsMenuCollapsed', productionsMenu.classList.contains('collapsed'));
            }
        }

        // Toggle Daily Expense Master menu
        function togglePettyCashMasterMenu() {
            const pettyCashMasterMenu = document.getElementById('pettyCashMasterMenu');
            const pettyCashMasterHeader = document.getElementById('pettyCashMasterHeader');
            
            if (pettyCashMasterMenu && pettyCashMasterHeader) {
                pettyCashMasterMenu.classList.toggle('collapsed');
                pettyCashMasterHeader.classList.toggle('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('pettyCashMasterMenuCollapsed', pettyCashMasterMenu.classList.contains('collapsed'));
            }
        }

        // Toggle Attendance menu
        function toggleAttendanceMenu() {
            const attendanceMenu = document.getElementById('attendanceMenu');
            const attendanceHeader = document.getElementById('attendanceHeader');
            
            if (attendanceMenu && attendanceHeader) {
                attendanceMenu.classList.toggle('collapsed');
                attendanceHeader.classList.toggle('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('attendanceMenuCollapsed', attendanceMenu.classList.contains('collapsed'));
            }
        }

        // Toggle Store menu
        function toggleStockMenu() {
            const stockMenu = document.getElementById('stockMenu');
            const stockHeader = document.getElementById('stockHeader');
            
            if (stockMenu && stockHeader) {
                stockMenu.classList.toggle('collapsed');
                stockHeader.classList.toggle('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('stockMenuCollapsed', stockMenu.classList.contains('collapsed'));
            }
        }

        // Initialize CRM menu state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const crmMenuOpen = localStorage.getItem('crmMenuOpen');
            const crmMenu = document.getElementById('crmMenu');
            const crmHeader = document.getElementById('crmHeader');
            if (crmMenu && crmHeader) {
                if (crmMenuOpen === 'true') {
                    crmMenu.style.display = 'block';
                    const arrow = crmHeader.querySelector('.arrow');
                    if (arrow) {
                        arrow.classList.remove('fa-chevron-down');
                        arrow.classList.add('fa-chevron-up');
                    }
                }
            }
        });

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

            // Masters menu
            const mastersSavedState = localStorage.getItem('mastersMenuCollapsed');
            if (mastersSavedState === 'true') {
                const mastersMenu = document.getElementById('mastersMenu');
                const mastersHeader = document.getElementById('mastersHeader');
                if (mastersMenu && mastersHeader) {
                    mastersMenu.classList.add('collapsed');
                    mastersHeader.classList.add('collapsed');
                }
            }

            // Productions menu
            const productionsSavedState = localStorage.getItem('productionsMenuCollapsed');
            if (productionsSavedState === 'true') {
                const productionsMenu = document.getElementById('productionsMenu');
                const productionsHeader = document.getElementById('productionsHeader');
                if (productionsMenu && productionsHeader) {
                    productionsMenu.classList.add('collapsed');
                    productionsHeader.classList.add('collapsed');
                }
            }

            // Daily Expense Master menu
            const pettyCashMasterSavedState = localStorage.getItem('pettyCashMasterMenuCollapsed');
            if (pettyCashMasterSavedState === 'true') {
                const pettyCashMasterMenu = document.getElementById('pettyCashMasterMenu');
                const pettyCashMasterHeader = document.getElementById('pettyCashMasterHeader');
                if (pettyCashMasterMenu && pettyCashMasterHeader) {
                    pettyCashMasterMenu.classList.add('collapsed');
                    pettyCashMasterHeader.classList.add('collapsed');
                }
            }

            // Attendance menu
            const attendanceSavedState = localStorage.getItem('attendanceMenuCollapsed');
            if (attendanceSavedState === 'true') {
                const attendanceMenu = document.getElementById('attendanceMenu');
                const attendanceHeader = document.getElementById('attendanceHeader');
                if (attendanceMenu && attendanceHeader) {
                    attendanceMenu.classList.add('collapsed');
                    attendanceHeader.classList.add('collapsed');
                }
            }

            // Store menu
            const stockSavedState = localStorage.getItem('stockMenuCollapsed');
            if (stockSavedState === 'true') {
                const stockMenu = document.getElementById('stockMenu');
                const stockHeader = document.getElementById('stockHeader');
                if (stockMenu && stockHeader) {
                    stockMenu.classList.add('collapsed');
                    stockHeader.classList.add('collapsed');
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

        
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('{{ asset("sw.js") }}')
            .then(() => console.log('Service Worker Registered'))
            .catch(err => console.log('SW Failed', err));
    });
}


    </script>
    @stack('scripts')
</body>
</html>

