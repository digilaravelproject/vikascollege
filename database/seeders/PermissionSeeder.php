<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Pehle purane permissions clear kar dete hain (clean start ke liye)
        // Aapke screenshot wala 'Course Management' bhi clear ho jayega
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Permissions ki list
        $permissions = [
            // User & Role Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'assign roles',
            'manage permissions',

            // Content Management (Pages & Menus)
            'view pages',
            'create pages',
            'edit pages',
            'delete pages',
            'manage menus',

            // Announcements & Events
            'view announcements',
            'create announcements',
            'edit announcements',
            'delete announcements',
            'publish announcements',
            'view events',
            'create events',
            'edit events',
            'delete events',
            'manage event categories',

            // Gallery
            'view gallery',
            'upload gallery images',
            'delete gallery images',
            'manage gallery categories',

            // Academic & Info Sections
            'manage academic calendar',
            'manage testimonials',
            'approve testimonials',
            'manage trust sections',
            'manage why choose us',

            // General Settings
            'manage settings',
            'view admin dashboard'
        ];

        // Har permission ko create karein
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
