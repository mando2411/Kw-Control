<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class CommitteePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = ['committees.list', 'committees.create', 'committees.edit', 'committees.delete', 'committees.restore','committee.home','committees.multi','committees.generate','candidates.index','candidates.list','attending-admin'];
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
