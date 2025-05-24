<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Add more permissions inside this array
        $permissions = [
            'manage chats',
            'manage users',
            'manage locations',
            'manage files',
            'manage invoices',
            'manage banner ads',
            'manage document-requirements',
            'manage notifications',
            'manage areas',
            'manage roles',
            'manage permissions',
            'manage settings',
            'manage reports',
            'manage companies',
            'manage property-types',
            'manage app-settings',
            'manage service-types',
            'manage property-service-types',
            'manage service-pricing',
            'manage valuation-requests',
            'manage payments',
        ];

        $this->processPermissions($permissions);
    }

    public function processPermissions($permissions){
        foreach ($permissions as $permissionName) {
            // create it for api guard
            $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'api']);
              if (!$permission->hasRole('admin')) {
                  $permission->assignRole('admin');
              }
        }
    }
}


