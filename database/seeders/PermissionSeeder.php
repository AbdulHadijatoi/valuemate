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


