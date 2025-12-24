<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PermissionSyncService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\View;
use App\Models\CompanyInformation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Auto-sync permissions when running in development or when explicitly enabled
        // This can be disabled in production for performance
        if (config('app.auto_sync_permissions', false)) {
            // Sync permissions on application boot (development only)
            if (app()->environment('local', 'development')) {
                // Only sync if permissions table exists
                try {
                    $syncService = app(PermissionSyncService::class);
                    $syncService->syncFromRoutes();
                } catch (\Exception $e) {
                    // Silently fail if database is not ready
                }
            }
        }

        // Share company information with all views
        View::composer('*', function ($view) {
            $companyInfo = null;
            $companyName = 'Woven_ERP'; // Default fallback
            
            try {
                $activeBranchId = session('active_branch_id');
                if ($activeBranchId) {
                    $companyInfo = CompanyInformation::where('branch_id', $activeBranchId)->first();
                }
                
                // If no company info for active branch, get the first available one
                if (!$companyInfo) {
                    $companyInfo = CompanyInformation::first();
                }
                
                if ($companyInfo) {
                    $companyName = $companyInfo->company_name;
                }
            } catch (\Exception $e) {
                // Silently fail if database is not ready or table doesn't exist
            }
            
            $view->with('companyInfo', $companyInfo);
            $view->with('companyName', $companyName);
        });
    }
}
