<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{

    public function run(): void
    {
        $systemAdmin = Role::where('name', 'system_admin')->first();

        $user = User::firstOrCreate(
            ['email' => 'mahroustamim@gmail.com'],
            [
                'name' => 'Mahrous',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'phone' => '01121665185'
            ]
        );

        if (!$user->admin) {
            $user->admin()->create([
                'social_id' => null,
                'social_type' => null,
                'money_balance' => 0,
                'otp' => null,
                'otp_expires_at' => null,
            ]);
        }

        $user->assignRole($systemAdmin);
    }
}
