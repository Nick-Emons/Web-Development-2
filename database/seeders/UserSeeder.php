<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Maakt een standaard admin-gebruiker aan
        User::create([
            'name' => 'Nick',
            'email' => 'admin@account.com',
            'password' => Hash::make('Test123'), 
            'role' => 'admin',
        ]);

        // Maakt een standaard normale gebruiker aan
        User::create([
            'name' => 'Peter',
            'email' => 'user@account.com',
            'password' => Hash::make('Test123'), 
            'role' => 'user',
        ]);
    }
}
