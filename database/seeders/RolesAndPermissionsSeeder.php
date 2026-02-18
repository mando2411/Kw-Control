<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $rolesWithPermissions = [
            'Administrator' => ['users', 'roles'],
        'مندوب' => [],
            'مرشح' => [],
            'مرشح رئيس قائمة' => [],
            'بحث في الكشوف' => [],
            'حذف المضامين' => [],
            'متعهد' => [],
            'مسئول كنترول' => [],
            'الدواوين' => [],
            'وكيل المدرسة' => [],
            'كل صلاحيات اللجان' => [],
            'التحكم بالموقع' => []
        ];

        foreach ($rolesWithPermissions as $role => $permissions) {
            $dbRole = Role::updateOrCreate([
                'name' => $role
            ]);

            array_map(fn($perm) => Permission::updateOrCreate(['name' => "$perm.list"]), $permissions);
            array_map(fn($perm) => Permission::updateOrCreate(['name' => "$perm.create"]), $permissions);
            array_map(fn($perm) => Permission::updateOrCreate(['name' =>  "$perm.edit"]), $permissions);
            array_map(fn($perm) => Permission::updateOrCreate(['name' =>  "$perm.delete"]), $permissions);
            array_map(fn($perm) => Permission::updateOrCreate(['name' => "$perm.restore"]), $permissions);

            if ($role == 'Administrator') {
                foreach ($permissions as $permission) {

                   $t= $dbRole->givePermissionTo([
                        "$permission.list",
                        "$permission.create",
                        "$permission.edit",
                        "$permission.delete",
                        "$permission.restore",
                    ]);
                }
            }
        }
    }
}
