<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@khattit.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'subscription_tier' => 'enterprise',
                'email_verified_at' => now(),
            ]
        );

        if (!$admin->wasRecentlyCreated) {
            $admin->update([
                'role' => 'admin',
                'password' => Hash::make('password123'), // Reset password to known value
            ]);
        }

        $this->command->info('Admin user created successfully.');
        $this->command->info('Email: admin@khattit.com');
        $this->command->info('Password: password123');
    }
}
