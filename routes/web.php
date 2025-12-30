<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

// Password Reset Routes
Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Logout
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Dashboard Routes (Protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Debug route to check permissions (remove after testing)
    Route::get('/debug-permissions', function() {
        $user = auth()->user();
        $user->load('roles.permissions');
        
        $debug = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'has_roles' => $user->roles->isNotEmpty(),
            'roles' => [],
            'permission_check' => []
        ];
        
        foreach ($user->roles as $role) {
            $roleData = [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'permissions_count' => $role->permissions->count(),
                'permissions' => []
            ];
            
            foreach ($role->permissions as $perm) {
                $roleData['permissions'][] = [
                    'id' => $perm->id,
                    'form_name' => $perm->form_name,
                    'name' => $perm->name,
                    'pivot_read' => $perm->pivot->read ?? 'N/A',
                    'pivot_write' => $perm->pivot->write ?? 'N/A',
                    'pivot_delete' => $perm->pivot->delete ?? 'N/A',
                ];
            }
            
            $debug['roles'][] = $roleData;
        }
        
        // Test permission checks
        $testForms = ['suppliers', 'customers', 'products', 'raw-materials', 'employees'];
        foreach ($testForms as $form) {
            $debug['permission_check'][$form] = $user->hasPermission($form, 'read');
        }
        
        return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
    })->name('debug.permissions');
    
    // User Management Routes
    Route::resource('users', App\Http\Controllers\UserController::class);
    
    // Account Settings Routes (for users to change their own password)
    Route::get('/account/change-password', [App\Http\Controllers\UserController::class, 'showChangePasswordForm'])->name('account.change-password');
    Route::post('/account/change-password', [App\Http\Controllers\UserController::class, 'changePassword']);
    
    // Admin Password Change Route (for admins to change any user's password)
    Route::post('/users/{id}/change-password', [App\Http\Controllers\UserController::class, 'adminChangePassword'])->name('users.change-password');
    
    // Organization Switching Routes (Super Admin only) - Must come before resource routes
    Route::get('/organizations/switch/clear', [App\Http\Controllers\OrganizationSwitchController::class, 'clear'])->name('organization.switch.clear');
    Route::get('/organizations/{organization}/switch', [App\Http\Controllers\OrganizationSwitchController::class, 'switch'])->name('organization.switch');
    
    // Organization Management Routes (Super Admin only)
    Route::resource('organizations', App\Http\Controllers\OrganizationController::class);
    
    // Branch Management Routes
    Route::resource('branches', App\Http\Controllers\BranchController::class);
     
    // Branch Selection Routes
    Route::get('/branch/select', [App\Http\Controllers\BranchSelectionController::class, 'show'])->name('branch.select');
    Route::post('/branch/select', [App\Http\Controllers\BranchSelectionController::class, 'select'])->name('branch.select.post');
    Route::get('/branches/{branch}/switch', [App\Http\Controllers\BranchSelectionController::class, 'switch'])->name('branch.switch');
    Route::get('/branches/switch/clear', [App\Http\Controllers\BranchSelectionController::class, 'clear'])->name('branch.switch.clear');
    
    // Role & Permission Management Routes (Super Admin only)
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::resource('permissions', App\Http\Controllers\PermissionController::class);
    Route::get('role-permissions/select', [App\Http\Controllers\RolePermissionController::class, 'select'])->name('role-permissions.select');
    Route::get('role-permissions/create', [App\Http\Controllers\RolePermissionController::class, 'create'])->name('role-permissions.create');
    Route::post('role-permissions', [App\Http\Controllers\RolePermissionController::class, 'store'])->name('role-permissions.store');
    Route::get('role-permissions/{role}/edit', [App\Http\Controllers\RolePermissionController::class, 'edit'])->name('role-permissions.edit');
    Route::post('role-permissions/{role}/update', [App\Http\Controllers\RolePermissionController::class, 'update'])->name('role-permissions.update');
    // Legacy routes for backward compatibility
    Route::get('roles/{role}/permissions', [App\Http\Controllers\RolePermissionController::class, 'edit'])->name('roles.permissions.edit');
    Route::post('roles/{role}/permissions', [App\Http\Controllers\RolePermissionController::class, 'update'])->name('roles.permissions.update');
    Route::get('roles/audit', [App\Http\Controllers\RolePermissionAuditController::class, 'index'])->name('roles.audit.index');
    Route::get('roles/{role}/audit', [App\Http\Controllers\RolePermissionAuditController::class, 'showRole'])->name('roles.audit.show');
    Route::get('roles/report/permissions', [App\Http\Controllers\RolePermissionAuditController::class, 'report'])->name('roles.report.permissions');
    
    // Settings Routes (Super Admin only)
    Route::resource('company-information', App\Http\Controllers\CompanyInformationController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    
    // Masters Routes
    Route::resource('suppliers', App\Http\Controllers\SupplierController::class);
    Route::resource('raw-materials', App\Http\Controllers\RawMaterialController::class);
    Route::resource('products', App\Http\Controllers\ProductController::class);
    Route::resource('customers', App\Http\Controllers\CustomerController::class);
    Route::resource('employees', App\Http\Controllers\EmployeeController::class);
    
    // Purchase Orders
    Route::resource('purchase-orders', App\Http\Controllers\PurchaseOrderController::class);

    // Material Inwards
    Route::resource('material-inwards', App\Http\Controllers\MaterialInwardController::class);

    // Work Orders
    Route::resource('work-orders', App\Http\Controllers\WorkOrderController::class);

    // Productions
    Route::resource('productions', App\Http\Controllers\ProductionController::class);
    
    // Sales Invoices
    Route::resource('sales-invoices', App\Http\Controllers\SalesInvoiceController::class);
    Route::get('sales-invoices/{sales_invoice}/print', [App\Http\Controllers\SalesInvoiceController::class, 'print'])->name('sales-invoices.print');
    
    // Debit Notes - Specific routes must come BEFORE resource route
    Route::get('debit-notes/reference-documents', [App\Http\Controllers\DebitNoteController::class, 'getReferenceDocuments'])->name('debit-notes.reference-documents');
    Route::get('debit-notes/reference-document-details', [App\Http\Controllers\DebitNoteController::class, 'getReferenceDocumentDetails'])->name('debit-notes.reference-document-details');
    Route::resource('debit-notes', App\Http\Controllers\DebitNoteController::class);
    Route::post('debit-notes/{debit_note}/submit', [App\Http\Controllers\DebitNoteController::class, 'submit'])->name('debit-notes.submit');
    Route::post('debit-notes/{debit_note}/cancel', [App\Http\Controllers\DebitNoteController::class, 'cancel'])->name('debit-notes.cancel');
    
    // Credit Notes - Specific routes must come BEFORE resource route
    Route::get('credit-notes/reference-documents', [App\Http\Controllers\CreditNoteController::class, 'getReferenceDocuments'])->name('credit-notes.reference-documents');
    Route::get('credit-notes/reference-document-details', [App\Http\Controllers\CreditNoteController::class, 'getReferenceDocumentDetails'])->name('credit-notes.reference-document-details');
    Route::resource('credit-notes', App\Http\Controllers\CreditNoteController::class);
    Route::post('credit-notes/{credit_note}/submit', [App\Http\Controllers\CreditNoteController::class, 'submit'])->name('credit-notes.submit');
    Route::post('credit-notes/{credit_note}/cancel', [App\Http\Controllers\CreditNoteController::class, 'cancel'])->name('credit-notes.cancel');
    
    // Quotations
    Route::resource('quotations', App\Http\Controllers\QuotationController::class);
    Route::get('quotations/{quotation}/print', [App\Http\Controllers\QuotationController::class, 'print'])->name('quotations.print');
    
    // Payment Tracking
    Route::get('payment-trackings/get-invoices', [App\Http\Controllers\PaymentTrackingController::class, 'getInvoices'])->name('payment-trackings.get-invoices');
    Route::get('payment-trackings/get-transaction-history/{invoiceId}', [App\Http\Controllers\PaymentTrackingController::class, 'getTransactionHistory'])->name('payment-trackings.get-transaction-history');
    Route::resource('payment-trackings', App\Http\Controllers\PaymentTrackingController::class);
    
    // Salary Master
    Route::get('salary-masters', [App\Http\Controllers\SalaryMasterController::class, 'index'])->name('salary-masters.index');
    Route::get('salary-masters/get-employee-salary-setup', [App\Http\Controllers\SalaryMasterController::class, 'getEmployeeSalarySetup'])->name('salary-masters.get-employee-salary-setup');
    
    // Salary Setup
    Route::get('salary-masters/salary-setup', [App\Http\Controllers\SalaryMasterController::class, 'salarySetupIndex'])->name('salary-masters.salary-setup.index');
    Route::get('salary-masters/salary-setup/create', [App\Http\Controllers\SalaryMasterController::class, 'salarySetupCreate'])->name('salary-masters.salary-setup.create');
    Route::post('salary-masters/salary-setup', [App\Http\Controllers\SalaryMasterController::class, 'salarySetupStore'])->name('salary-masters.salary-setup.store');
    Route::get('salary-masters/salary-setup/{salarySetup}', [App\Http\Controllers\SalaryMasterController::class, 'salarySetupShow'])->name('salary-masters.salary-setup.show');
    Route::get('salary-masters/salary-setup/{salarySetup}/edit', [App\Http\Controllers\SalaryMasterController::class, 'salarySetupEdit'])->name('salary-masters.salary-setup.edit');
    Route::put('salary-masters/salary-setup/{salarySetup}', [App\Http\Controllers\SalaryMasterController::class, 'salarySetupUpdate'])->name('salary-masters.salary-setup.update');
    Route::delete('salary-masters/salary-setup/{salarySetup}', [App\Http\Controllers\SalaryMasterController::class, 'salarySetupDestroy'])->name('salary-masters.salary-setup.destroy');
    
    // Salary Advance
    Route::get('salary-masters/salary-advance', [App\Http\Controllers\SalaryMasterController::class, 'salaryAdvanceIndex'])->name('salary-masters.salary-advance.index');
    Route::get('salary-masters/salary-advance/create', [App\Http\Controllers\SalaryMasterController::class, 'salaryAdvanceCreate'])->name('salary-masters.salary-advance.create');
    Route::post('salary-masters/salary-advance', [App\Http\Controllers\SalaryMasterController::class, 'salaryAdvanceStore'])->name('salary-masters.salary-advance.store');
    Route::get('salary-masters/salary-advance/{salaryAdvance}', [App\Http\Controllers\SalaryMasterController::class, 'salaryAdvanceShow'])->name('salary-masters.salary-advance.show');
    Route::get('salary-masters/salary-advance/{salaryAdvance}/edit', [App\Http\Controllers\SalaryMasterController::class, 'salaryAdvanceEdit'])->name('salary-masters.salary-advance.edit');
    Route::put('salary-masters/salary-advance/{salaryAdvance}', [App\Http\Controllers\SalaryMasterController::class, 'salaryAdvanceUpdate'])->name('salary-masters.salary-advance.update');
    Route::delete('salary-masters/salary-advance/{salaryAdvance}', [App\Http\Controllers\SalaryMasterController::class, 'salaryAdvanceDestroy'])->name('salary-masters.salary-advance.destroy');
    
    // Salary Processing
    Route::get('salary-masters/salary-processing', [App\Http\Controllers\SalaryMasterController::class, 'salaryProcessingIndex'])->name('salary-masters.salary-processing.index');
    Route::get('salary-masters/salary-processing/create', [App\Http\Controllers\SalaryMasterController::class, 'salaryProcessingCreate'])->name('salary-masters.salary-processing.create');
    Route::post('salary-masters/salary-processing', [App\Http\Controllers\SalaryMasterController::class, 'salaryProcessingStore'])->name('salary-masters.salary-processing.store');
    Route::get('salary-masters/salary-processing/{salaryProcessing}', [App\Http\Controllers\SalaryMasterController::class, 'salaryProcessingShow'])->name('salary-masters.salary-processing.show');
    Route::get('salary-masters/salary-processing/{salaryProcessing}/edit', [App\Http\Controllers\SalaryMasterController::class, 'salaryProcessingEdit'])->name('salary-masters.salary-processing.edit');
    Route::put('salary-masters/salary-processing/{salaryProcessing}', [App\Http\Controllers\SalaryMasterController::class, 'salaryProcessingUpdate'])->name('salary-masters.salary-processing.update');
    Route::post('salary-masters/salary-processing/{salaryProcessing}/mark-paid', [App\Http\Controllers\SalaryMasterController::class, 'markPaid'])->name('salary-masters.salary-processing.mark-paid');
    Route::delete('salary-masters/salary-processing/{salaryProcessing}', [App\Http\Controllers\SalaryMasterController::class, 'salaryProcessingDestroy'])->name('salary-masters.salary-processing.destroy');
    
    
    // Daily Expense - Specific routes must come BEFORE resource route
    Route::get('petty-cash/{petty_cash}/receipt', [App\Http\Controllers\PettyCashController::class, 'showReceipt'])->name('petty-cash.receipt');
    Route::post('petty-cash/{petty_cash}/delete-receipt', [App\Http\Controllers\PettyCashController::class, 'deleteReceipt'])->name('petty-cash.delete-receipt');
    Route::resource('petty-cash', App\Http\Controllers\PettyCashController::class);
    Route::get('petty-cash-report', [App\Http\Controllers\PettyCashController::class, 'report'])->name('petty-cash.report');
    Route::get('petty-cash-export/pdf', [App\Http\Controllers\PettyCashController::class, 'exportPdf'])->name('petty-cash.export.pdf');
    Route::get('petty-cash-export/excel', [App\Http\Controllers\PettyCashController::class, 'exportExcel'])->name('petty-cash.export.excel');
    
    // Attendance
    Route::get('attendances', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('attendances/create', [App\Http\Controllers\AttendanceController::class, 'create'])->name('attendances.create');
    Route::post('attendances', [App\Http\Controllers\AttendanceController::class, 'store'])->name('attendances.store');
    Route::get('attendances/{date}/edit', [App\Http\Controllers\AttendanceController::class, 'edit'])->name('attendances.edit');
    Route::put('attendances/{date}', [App\Http\Controllers\AttendanceController::class, 'update'])->name('attendances.update');
    Route::delete('attendances/{date}', [App\Http\Controllers\AttendanceController::class, 'destroy'])->name('attendances.destroy');
    Route::get('attendance-report', [App\Http\Controllers\AttendanceController::class, 'report'])->name('attendances.report');
    Route::get('attendance-export/pdf', [App\Http\Controllers\AttendanceController::class, 'exportPdf'])->name('attendances.export.pdf');
    Route::get('attendance-export/excel', [App\Http\Controllers\AttendanceController::class, 'exportExcel'])->name('attendances.export.excel');
    
    // Leaves
    Route::resource('leaves', App\Http\Controllers\LeaveController::class);
    
    // Stock Reports
    Route::get('stock/raw-material', [App\Http\Controllers\StockController::class, 'rawMaterialStock'])->name('stock.raw-material');
    Route::get('stock/finished-goods', [App\Http\Controllers\StockController::class, 'finishedGoodsStock'])->name('stock.finished-goods');
    
    // Stock Transactions
    Route::resource('stock-transactions', App\Http\Controllers\StockTransactionController::class);
    
    // CRM - Notes
    Route::resource('notes', App\Http\Controllers\NoteController::class);
    Route::delete('notes/attachments/{attachment}', [App\Http\Controllers\NoteController::class, 'deleteAttachment'])->name('notes.attachments.destroy');
    
    // CRM - Tasks
    Route::resource('tasks', App\Http\Controllers\TaskController::class);
});
