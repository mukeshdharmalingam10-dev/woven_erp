<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Submenu;
use App\Models\Form;

class MenuFormSeeder extends Seeder
{
    public function run(): void
    {
        // Clean up any legacy menus that no longer match the sidebar
        Menu::whereIn('code', ['tenders', 'transactions', 'masters', 'purchase', 'store', 'sales', 'tender_sales', 'enquiry_sales', 'supplier', 'system'])->delete();

        // High-level menus aligned with left sidebar - only keep what we need
        $systemAdminMenu = Menu::firstOrCreate(
            ['code' => 'system_admin'],
            ['name' => 'System Admin', 'is_active' => true]
        );

        $settings = Menu::firstOrCreate(
            ['code' => 'settings'],
            ['name' => 'Settings', 'is_active' => true]
        );

        // Helper to create/update forms
        $makeForm = function (Menu $menu, ?Submenu $submenu, string $name, string $code, ?string $routeName = null) {
            $form = Form::firstOrNew(['code' => $code]);
            $form->menu_id = $menu->id;
            $form->submenu_id = $submenu ? $submenu->id : null;
            $form->name = $name;
            $form->route_name = $routeName;
            $form->is_active = true;
            $form->save();
        };

        // System Admin (matches System Admin group in sidebar)
        $makeForm($systemAdminMenu, null, 'Organizations',   'organizations_form',   'organizations.index');
        $makeForm($systemAdminMenu, null, 'Branches',        'branches_form',        'branches.index');
        $makeForm($systemAdminMenu, null, 'Users',           'users_form',           'users.index');
        $makeForm($systemAdminMenu, null, 'Roles',           'roles_form',           'roles.index');
        $makeForm($systemAdminMenu, null, 'Permissions',     'permissions_form',     'permissions.index');
        $makeForm($systemAdminMenu, null, 'Role Permissions', 'role_permissions_form', 'role-permissions.select');

        // Settings / Company (Settings group)
        $makeForm($settings, null, 'Company Information', 'company_information_form', 'company-information.index');
    }
}


