<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Test User1',
            'email' => 'testuser111@example.com',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Test User112',
            'email' => 'testuser21@example.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
