<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;

class SetupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:setup {--database= : Database name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database if it does not exist';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $connection = config('database.default', 'mysql');
        $config = config("database.connections.{$connection}", []);
        
        $databaseName = $this->option('database') ?? env('DB_DATABASE') ?? $config['database'] ?? 'basic_template';
        $host = env('DB_HOST', $config['host'] ?? '127.0.0.1');
        $port = env('DB_PORT', $config['port'] ?? '3306');
        $username = env('DB_USERNAME', $config['username'] ?? 'root');
        $password = env('DB_PASSWORD', $config['password'] ?? '');
        
        $this->info("Attempting to create database: {$databaseName}");
        $this->info("Host: {$host}, Port: {$port}, User: {$username}");

        try {
            // Create PDO connection without database name
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);
            
            // Create database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            $this->info("✅ Database '{$databaseName}' created successfully!");
            $this->info("Now run: php artisan migrate");
            
            return 0;
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
    }
}

