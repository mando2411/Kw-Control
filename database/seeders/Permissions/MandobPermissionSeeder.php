<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MandobPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = ['attending','rep-home', 'voters.change-status','candidates.changeVotes','candidates.setVotes','sorting','school-rep','rep.change'];
        $permissions_db = [];
        foreach ($permissions as $permission) {
            $permissions_db[] = Permission::updateOrCreate([
                'name' => $permission
            ])->id;
        }
        $roles = [
            'Administrator',
            'مرشح',
            'مندوب',
            'وكيل المدرسة',
            'التحكم بالموقع'
        ];

        foreach ($roles as $roleName) {
            if ($role = Role::findByName($roleName)) {
                $role->givePermissionTo($permissions_db);
            }
        }
    }
}
