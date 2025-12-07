<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view clients',
            'create clients',
            'update clients',
            'delete clients',
            'view accounts',
            'create accounts',
            'update accounts',
            'delete accounts',
            'manage users',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // تعريف الأدوار (Roles) وتعيين الصلاحيات
        $clientRole = Role::firstOrCreate(['name' => 'Client']);
        $clientRole->syncPermissions([
            'view accounts',
            'view clients',
        ]);

        $tellerRole = Role::firstOrCreate(['name' => 'Teller']);
        $tellerRole->syncPermissions([
            'view clients',
            'view accounts',
            'create accounts',
            'update accounts',
        ]);

        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $managerRole->syncPermissions([
            'view clients',
            'create clients',
            'update clients',
            'view accounts',
            'create accounts',
            'update accounts',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions(Permission::all());
    }
}
