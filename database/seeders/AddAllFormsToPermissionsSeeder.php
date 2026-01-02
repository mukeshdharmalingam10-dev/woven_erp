<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class AddAllFormsToPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $forms = [
            // System Admin Forms
            ['form_name' => 'Branches', 'name' => 'branches', 'slug' => 'branches', 'module' => 'System Admin', 'is_active' => true],
            ['form_name' => 'Organizations', 'name' => 'organizations', 'slug' => 'organizations', 'module' => 'System Admin', 'is_active' => true],
            ['form_name' => 'Permissions', 'name' => 'permissions', 'slug' => 'permissions', 'module' => 'System Admin', 'is_active' => true],
            ['form_name' => 'Role Permissions', 'name' => 'role_permissions', 'slug' => 'role-permissions', 'module' => 'System Admin', 'is_active' => true],
            ['form_name' => 'Roles', 'name' => 'roles', 'slug' => 'roles', 'module' => 'System Admin', 'is_active' => true],
            ['form_name' => 'Users', 'name' => 'users', 'slug' => 'users', 'module' => 'System Admin', 'is_active' => true],
            
            // Settings Forms
            ['form_name' => 'Company Information', 'name' => 'company_information', 'slug' => 'company-information', 'module' => 'Settings', 'is_active' => true],
            
            // Company-info Forms (separate module as requested)
            ['form_name' => 'Company Info', 'name' => 'company_info', 'slug' => 'company-info', 'module' => 'Company-info', 'is_active' => true],
            
            // Customer-complaints Forms
            ['form_name' => 'Customer Complaints', 'name' => 'customer_complaints', 'slug' => 'customer-complaints', 'module' => 'Customer-complaints', 'is_active' => true],
            
            // Masters Forms
            ['form_name' => 'Suppliers', 'name' => 'suppliers', 'slug' => 'suppliers', 'module' => 'Masters', 'is_active' => true],
            ['form_name' => 'Raw Materials', 'name' => 'raw_materials', 'slug' => 'raw-materials', 'module' => 'Masters', 'is_active' => true],
            ['form_name' => 'Products', 'name' => 'products', 'slug' => 'products', 'module' => 'Masters', 'is_active' => true],
            ['form_name' => 'Customers', 'name' => 'customers', 'slug' => 'customers', 'module' => 'Masters', 'is_active' => true],
            ['form_name' => 'Employees', 'name' => 'employees', 'slug' => 'employees', 'module' => 'Masters', 'is_active' => true],
            
            // Transaction Forms
            ['form_name' => 'Purchase Orders', 'name' => 'purchase_orders', 'slug' => 'purchase-orders', 'module' => 'Transactions', 'is_active' => true],
            ['form_name' => 'Material Inwards', 'name' => 'material_inwards', 'slug' => 'material-inwards', 'module' => 'Transactions', 'is_active' => true],
            ['form_name' => 'Sales Invoices', 'name' => 'sales_invoices', 'slug' => 'sales-invoices', 'module' => 'Transactions', 'is_active' => true],
            ['form_name' => 'Quotations', 'name' => 'quotations', 'slug' => 'quotations', 'module' => 'Transactions', 'is_active' => true],
            ['form_name' => 'Stock Transactions', 'name' => 'stock_transactions', 'slug' => 'stock-transactions', 'module' => 'Transactions', 'is_active' => true],
            
            // Production Forms
            ['form_name' => 'Work Orders', 'name' => 'work_orders', 'slug' => 'work-orders', 'module' => 'Productions', 'is_active' => true],
            ['form_name' => 'Productions', 'name' => 'productions', 'slug' => 'productions', 'module' => 'Productions', 'is_active' => true],
            
            // CRM Forms
            ['form_name' => 'Notes', 'name' => 'notes', 'slug' => 'notes', 'module' => 'CRM', 'is_active' => true],
            ['form_name' => 'Tasks', 'name' => 'tasks', 'slug' => 'tasks', 'module' => 'CRM', 'is_active' => true],
        ];

        foreach ($forms as $form) {
            Permission::updateOrCreate(
                ['slug' => $form['slug']],
                $form
            );
        }

        $this->command->info('All forms have been added to permissions table.');
    }
}

