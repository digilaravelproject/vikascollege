<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'], // Email check
            [
                'name' => 'Admin User',
                'password' => Hash::make('12345678'), // Change password if you want
            ]
        );

        // Assign Admin role to user
        $adminUser->assignRole($adminRole);
    }
}
