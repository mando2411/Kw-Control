<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class VoterPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = ['voters.list', 'voters.create', 'voters.edit','import.contractor.voters' , 'voters.delete', 'voters.restore','madameen','history','delete'];
        $permissions_db = [];
        foreach ($permissions as $permission) {
            $permissions_db[] = Permission::updateOrCreate([
                'name' => $permission
            ])->id;
        }

        $roles = [
            'Administrator',
            'التحكم بالموقع',
            'مرشح',
            'مسئول كنترول'
        ];

        foreach ($roles as $roleName) {
            if ($role = Role::findByName($roleName)) {
                $role->givePermissionTo($permissions_db);
            }
        }
    }
}
