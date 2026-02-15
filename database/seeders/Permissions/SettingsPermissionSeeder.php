<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SettingsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = ['settings.show', 'settings.edit'];
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
