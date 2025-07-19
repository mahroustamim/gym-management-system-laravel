<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $systemAdmin = Role::firstOrCreate(
            ['name' => 'system_admin'],
            ['guard_name' => 'web']
        );

        $gymAdmin = Role::firstOrCreate(
            ['name' => 'gym_admin'],
            ['guard_name' => 'web']
        );

    }
}
