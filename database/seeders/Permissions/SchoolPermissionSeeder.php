<?php

namespace Database\Seeders\Permissions;

use App\Enums\Type;
use App\Models\Role;
use App\Models\School;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SchoolPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = ['schools.list', 'schools.create', 'schools.edit', 'schools.delete', 'schools.restore'];
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

        $schools = [
            ['name' => 'مدرسه نساء', 'type' => Type::WOMEN->value, 'election_id' => null],
            ['name' => 'مدرسه رجال', 'type' => Type::MEN->value, 'election_id' => null],
        ];

        foreach ($schools as $school) {
            School::updateOrCreate(
                [
                    'name' => $school['name'],
                    'type' => $school['type'],
                    'election_id' => $school['election_id'],
                ],
                $school
            );
        }
    }
}
