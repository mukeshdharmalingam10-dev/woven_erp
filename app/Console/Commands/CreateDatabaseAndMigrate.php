<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;

class CreateDatabaseAndMigrate extends Command
{
    protected $signature = 'db:create-and-setup';
    protected $description = 'Create database, run migrations and seeders';

    public function handle()
    {
        $connection = config('database.default', 'mysql');
        $config = config("database.connections.{$connection}", []);
        
        $databaseName = env('DB_DATABASE') ?? $config['database'] ?? 'basic_template';
        $host = env('DB_HOST', $config['host'] ?? '127.0.0.1');
        $port = env('DB_PORT', $config['port'] ?? '3306');
        $username = env('DB_USERNAME', $config['username'] ?? 'root');
        $password = env('DB_PASSWORD', $config['password'] ?? '');
        
        $this->info("==========================================");
        $this->info("Woven_ERP - Complete Database Setup");
        $this->info("==========================================");
        $this->newLine();
        $this->info("Database: {$databaseName}");
        $this->info("Host: {$host}:{$port}");
        $this->info("User: {$username}");
        $this->newLine();
        
        // Step 1: Create Database
        $this->info("Step 1: Creating database '{$databaseName}'...");
        try {
            // Create PDO connection without database name
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);
            
            // Create database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            $this->info("✅ Database '{$databaseName}' created successfully!");
        } catch (Exception $e) {
            $this->error("❌ Failed to create database: " . $e->getMessage());
            $this->warn("Please check:");
            $this->line("1. MySQL service is running");
            $this->line("2. Username and password in .env are correct");
            $this->line("3. User has CREATE DATABASE privilege");
            $this->newLine();
            $this->warn("Or create the database manually:");
            $this->line("CREATE DATABASE `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            return 1;
        }
        
        $this->newLine();
        
        // Step 2: Run Migrations
        $this->info("Step 2: Running migrations...");
        try {
            $this->call('migrate', ['--force' => true]);
            $this->info("✅ Migrations completed successfully!");
        } catch (Exception $e) {
            $this->error("❌ Migration failed: " . $e->getMessage());
            return 1;
        }
        
        $this->newLine();
        
        // Step 3: Seed Database
        $this->info("Step 3: Seeding database...");
        try {
            $this->call('db:seed', ['--force' => true]);
            $this->info("✅ Database seeded successfully!");
        } catch (Exception $e) {
            $this->error("❌ Seeding failed: " . $e->getMessage());
            return 1;
        }
        
        $this->newLine();
        $this->info("==========================================");
        $this->info("✅ Database setup completed successfully!");
        $this->info("==========================================");
        $this->newLine();
        $this->info("You can now login with:");
        $this->line("  Email: admin@erp.com");
        $this->line("  Password: password");
        $this->newLine();
        
        return 0;
    }
}

