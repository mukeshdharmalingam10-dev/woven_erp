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
});
