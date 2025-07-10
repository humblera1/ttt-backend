<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InitAdminUser extends Command
{
    protected const string DEFAULT_USERNAME = 'humblerat';

    protected const string DEFAULT_PASSWORD = 'trial';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the first user and assign the admin role';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $username = $this->ask('Enter admin username', self::DEFAULT_USERNAME);
        $password = $this->secret('Enter admin password') ?: self::DEFAULT_PASSWORD;

        if (User::where('username', $username)->exists()) {
            $this->error("A user with username '{$username}' already exists.");

            return 1;
        }

        $user = User::create([
            'username' => $username,
            'password' => Hash::make($password),
        ]);

        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            $this->error("The 'admin' role was not found. Please initialize roles first.");

            return 1;
        }

        $user->assignRole($adminRole);

        $this->info('Admin user created successfully!');
        $this->line('-----------------------------');
        $this->line('Username: ' . $username);
        $this->line('Password: ' . $password);
        $this->line('-----------------------------');
        $this->warn('Please save these credentials!');

        return 0;
    }
}
