<?php

namespace App\Traits\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

trait PermissionGenerator
{

    public function seedPermissions(): static
    {
        $this->generateSeederFile();
        $module_permissions = array_map(
            fn($permission) => $this->model_name->plural()->kebab()->lower() . '.' . $permission,
            [
                'list',
                'create',
                'edit',
                'delete',
                'restore',
            ]);

        foreach ($module_permissions as $permission) {
            Permission::updateOrCreate([
                'name' => $permission
            ]);
        }

        if ($role = Role::whereName('Administrator')->first()) {
            $role->givePermissionTo($module_permissions);
        }
        $this->info($this->model_name . ' Permissions Seeded!');
        return $this;
    }


    public function generateSeederFile(): static
    {
        $permissionSeederFile = Str::of(File::get($this->stubs_path . '/resource.permissions.seeder.stub'))
            ->replace('{{ permissionPrefix }}', $this->model_name->plural()->lower()->kebab())
            ->replace('{{ resourceName }}', $this->model_name);
        File::ensureDirectoryExists(database_path('seeders/Permissions'));

        File::put(database_path('seeders/Permissions/'.$this->model_name.'PermissionSeeder.php'), $permissionSeederFile);
        return $this;
    }
}
