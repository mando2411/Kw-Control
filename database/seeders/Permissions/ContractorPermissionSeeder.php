<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ContractorPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = ['contractors.list', 'contractors.create', 'contractors.edit', 'contractors.delete', 'contractors.restore','mot-up','search-stat-con','delete-stat-con','con-main','results'];
        $permissions_db = [];
        foreach ($permissions as $permission) {
            $permissions_db[] = Permission::updateOrCreate([
                'name' => $permission
            ])->id;
        }

        $roles = [
            'Administrator',
            'مرشح',
            'مسئول كنترول',
            'التحكم بالموقع'
        ];

        foreach ($roles as $roleName) {
            if ($role = Role::findByName($roleName)) {
                $role->givePermissionTo($permissions_db);
            }
        }
        if ($role = Role::findByName('بحث في الكشوف')) {
            $role->givePermissionTo($permissions_db[6]);
        }
        if ($role = Role::findByName('حذف المضامين')) {
            $role->givePermissionTo($permissions_db[7]);
        }

    }
}
