<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CheckAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and create/fix admin user for Filament access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking admin user...');
        
        $adminUser = User::where('email', 'admin@btcs.com')->first();
        
        if (!$adminUser) {
            $this->warn('Admin user does not exist. Creating...');
            $adminUser = User::create([
                'name' => 'Admin User',
                'email' => 'admin@btcs.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            $this->info('✅ Admin user created successfully!');
        } else {
            $this->info("Admin user exists: {$adminUser->name}");
        }
        
        $this->info("Current role: {$adminUser->role}");
        $this->info("Can access panel: " . ($adminUser->canAccessPanel(null) ? 'YES' : 'NO'));
        $this->info("Is admin: " . ($adminUser->isAdmin() ? 'YES' : 'NO'));
        
        if ($adminUser->role !== 'admin') {
            $this->warn('Admin user does not have admin role. Fixing...');
            $adminUser->update(['role' => 'admin']);
            $this->info('✅ Admin role updated!');
        }
        
        $this->info("\n📋 Login credentials:");
        $this->info("Email: admin@btcs.com");
        $this->info("Password: password");
    }
}
