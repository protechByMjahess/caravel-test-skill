<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a specific admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+1234567890',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'address' => '123 Admin Street',
            'city' => 'Admin City',
            'country' => 'United States',
            'postal_code' => '12345',
            'is_active' => true,
        ]);

        // Create 50 random users using the factory
        User::factory(50)->create();
    }
}
