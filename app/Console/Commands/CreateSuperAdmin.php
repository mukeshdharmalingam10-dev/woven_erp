<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-super {--email=admin@erp.com} {--password=Admin@123} {--name=Super Admin} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all users and create a new Super Admin user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('==========================================');
        $this->info('Creating New Super Admin');
        $this->info('==========================================');
        $this->newLine();

        // Confirm before proceeding (unless --force flag is used)
        if (!$this->option('force')) {
            if (!$this->confirm('This will DELETE ALL USERS in the system. Are you sure you want to continue?')) {
                $this->warn('Operation cancelled.');
                return 0;
            }
        } else {
            $this->warn('⚠️  Force mode: Proceeding without confirmation...');
        }

        try {
            DB::beginTransaction();

            // Step 1: Delete all users
            $this->info('Step 1: Clearing all users...');
            $userCount = User::count();
            User::query()->delete();
            $this->info("✅ Deleted {$userCount} user(s)");

            $this->newLine();

            // Step 2: Get or create Super Admin role
            $this->info('Step 2: Ensuring Super Admin role exists...');
            $superAdminRole = Role::firstOrCreate(
                ['slug' => 'super-admin'],
                [
                    'name' => 'Super Admin',
                    'description' => 'Highest level admin with full system access',
                    'is_active' => true,
                ]
            );
            $this->info("✅ Super Admin role ready (ID: {$superAdminRole->id})");

            $this->newLine();

            // Step 3: Create new Super Admin user
            $this->info('Step 3: Creating new Super Admin user...');
            
            $email = $this->option('email');
            $password = $this->option('password');
            $name = $this->option('name');

            $superAdmin = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role_id' => $superAdminRole->id,
                'mobile' => '0000000000', // Default mobile
            ]);

            // Assign Super Admin to Main Branch (default branch)
            $mainBranch = Branch::where('code', 'MB001')->first();
            if ($mainBranch) {
                $superAdmin->branches()->sync([$mainBranch->id]);
                $this->info("✅ Super Admin user created successfully!");
                $this->info("✅ Assigned to Main Branch: {$mainBranch->name}");
            } else {
            $this->info("✅ Super Admin user created successfully!");
                $this->warn("⚠️  Main Branch (MB001) not found. Please run BranchSeeder first.");
            }

            DB::commit();

            $this->newLine();
            $this->info('==========================================');
            $this->info('✅ Super Admin Created Successfully!');
            $this->info('==========================================');
            $this->newLine();
            $this->info('📧 Login Credentials:');
            $this->line('   Email: ' . $email);
            $this->line('   Password: ' . $password);
            $this->newLine();
            $this->info('🔐 Please save these credentials securely!');
            $this->newLine();

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
