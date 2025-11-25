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
    public function run()
    {
        // Master
        User::create([
            'name' => 'master',
            'email' => 'master@email.com',
            'password' => Hash::make('gtt1234'),
            'role' => 'master',
        ]);

        // Admin
        User::create([
            'name' => 'admin',
            'email' => 'admin@email.com',
            'password' => Hash::make('gtt1234'),
            'role' => 'admin',
        ]);

        // Viewer
        User::create([
            'name' => 'viewer',
            'email' => 'viewer@email.com',
            'password' => Hash::make('gtt1234'),
            'role' => 'viewer',
        ]);
    }
}
