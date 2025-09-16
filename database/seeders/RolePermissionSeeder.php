<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat permissions
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'edit roles']); // tambahin biar sesuai masalah lo

        // Buat roles
        $adminRole = Role::create(['name' => 'Admin']);
        $userRole = Role::create(['name' => 'Karyawan']);

        // Assign permission
        $adminRole->givePermissionTo(Permission::all()); // Admin punya semua
        $userRole->givePermissionTo('manage articles');
    }
}
