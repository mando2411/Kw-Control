<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ElectionPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = ['elections.list', 'elections.create', 'elections.edit', 'elections.delete', 'elections.restore','cards' ,"user-change"];
        $permissions_db = [];
        foreach ($permissions as $permission) {
            $permissions_db[] = Permission::updateOrCreate([
                'name' => $permission
            ])->id;
        }

        $roles = [
            'Administrator',
            'مرشح',
            'التحكم بالموقع'
        ];

        foreach ($roles as $roleName) {
            if ($role = Role::findByName($roleName)) {
                $role->givePermissionTo($permissions_db);
            }
        }
    }
}
