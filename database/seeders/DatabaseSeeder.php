<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(AdminSeeder::class);
        $this->loadPermissions();
    }

    private function loadPermissions()
    {
        $permissions_seeder_path = database_path('seeders/Permissions');
        if (File::exists($permissions_seeder_path)) {
            $dir_files = array_diff(scandir($permissions_seeder_path), ['..', '.']);
            foreach ($dir_files as $file) {
                if (!\Str::of($file)->endsWith('.php')) {
                    continue;
                }
                $class = str_replace('.php', '', $file);
                $this->call("Database\Seeders\Permissions\\" . $class);
            }
        }
    }
}
