<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Create 5 test users with different roles
        $users = [
            [
                'name' => 'أحمد محمد',
                'email' => 'ahmed@test.com',
                'role' => 'user',
                'subscription_tier' => 'free',
            ],
            [
                'name' => 'سارة علي',
                'email' => 'sara@test.com',
                'role' => 'user',
                'subscription_tier' => 'basic',
            ],
            [
                'name' => 'محمد خالد',
                'email' => 'mohamed@test.com',
                'role' => 'advertiser',
                'subscription_tier' => 'pro',
            ],
            [
                'name' => 'فاطمة حسن',
                'email' => 'fatima@test.com',
                'role' => 'user',
                'subscription_tier' => 'enterprise',
            ],
            [
                'name' => 'عمر سعيد',
                'email' => 'omar@test.com',
                'role' => 'advertiser',
                'subscription_tier' => 'pro',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ])
            );
        }

        $this->command->info('Test users created successfully!');
    }
}
