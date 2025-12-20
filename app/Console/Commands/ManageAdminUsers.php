<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class ManageAdminUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:manage {action} {email?} {--list}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage admin users - promote, demote, or list admin users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $email = $this->argument('email');

        switch ($action) {
            case 'promote':
                return $this->promoteUser($email);
            case 'demote':
                return $this->demoteUser($email);
            case 'list':
                return $this->listAdminUsers();
            case 'check':
                return $this->checkUserAdmin($email);
            default:
                $this->error('Invalid action. Available actions: promote, demote, list, check');
                $this->line('');
                $this->line('Usage examples:');
                $this->line('  php artisan admin:manage promote user@example.com');
                $this->line('  php artisan admin:manage demote user@example.com');
                $this->line('  php artisan admin:manage check user@example.com');
                $this->line('  php artisan admin:manage list');
                return 1;
        }
    }

    /**
     * Promote a user to admin
     */
    protected function promoteUser($email)
    {
        if (!$email) {
            $email = $this->ask('Enter the email address of the user to promote to admin');
        }

        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            $this->error('Invalid email format');
            return 1;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found");
            return 1;
        }

        if ($user->isAdmin()) {
            $this->info("User '{$user->name}' ({$user->email}) is already an admin");
            return 0;
        }

        $confirmed = $this->confirm("Are you sure you want to promote '{$user->name}' ({$user->email}) to admin?");

        if (!$confirmed) {
            $this->info('Operation cancelled');
            return 0;
        }

        $user->setAdminStatus(true);

        $this->info("✅ User '{$user->name}' ({$user->email}) has been promoted to admin");
        $this->line("Account Type: {$user->account_type}");

        return 0;
    }

    /**
     * Demote a user from admin
     */
    protected function demoteUser($email)
    {
        if (!$email) {
            $email = $this->ask('Enter the email address of the user to demote from admin');
        }

        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            $this->error('Invalid email format');
            return 1;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found");
            return 1;
        }

        if (!$user->isAdmin()) {
            $this->info("User '{$user->name}' ({$user->email}) is not an admin");
            return 0;
        }

        $confirmed = $this->confirm("Are you sure you want to demote '{$user->name}' ({$user->email}) from admin?");

        if (!$confirmed) {
            $this->info('Operation cancelled');
            return 0;
        }

        $user->setAdminStatus(false);

        $this->info("✅ User '{$user->name}' ({$user->email}) has been demoted from admin");
        $this->line("Account Type: {$user->account_type}");

        return 0;
    }

    /**
     * List all admin users
     */
    protected function listAdminUsers()
    {
        $adminUsers = User::where('is_admin', true)->get();

        if ($adminUsers->isEmpty()) {
            $this->info('No admin users found');
            return 0;
        }

        $this->line('Admin Users:');
        $this->line('');

        $headers = ['ID', 'Name', 'Email', 'Account Type', 'Created At'];
        $rows = [];

        foreach ($adminUsers as $user) {
            $rows[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->account_type,
                $user->created_at->format('Y-m-d H:i:s')
            ];
        }

        $this->table($headers, $rows);
        $this->line('');
        $this->info("Total admin users: {$adminUsers->count()}");

        return 0;
    }

    /**
     * Check if a user is admin
     */
    protected function checkUserAdmin($email)
    {
        if (!$email) {
            $email = $this->ask('Enter the email address of the user to check');
        }

        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            $this->error('Invalid email format');
            return 1;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found");
            return 1;
        }

        $this->line("User Information:");
        $this->line("Name: {$user->name}");
        $this->line("Email: {$user->email}");
        $this->line("Account Type: {$user->account_type}");
        $this->line("Admin Status: " . ($user->isAdmin() ? '✅ Yes' : '❌ No'));
        $this->line("Created: {$user->created_at->format('Y-m-d H:i:s')}");
        $this->line("Email Verified: " . ($user->hasVerifiedEmail() ? '✅ Yes' : '❌ No'));

        return 0;
    }
}
