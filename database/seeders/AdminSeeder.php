<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Mahrous',
            'email' => 'mahroustamim@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('12345678'),
            'phone' => '01121665185',
            'role' => 'saas_admin',
        ]);
    }
}
