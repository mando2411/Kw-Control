<?php

namespace App\Traits\Commands;

use Illuminate\Support\Facades\File;

trait JsonApiResourceGenerator
{
    public function generateJsonResource(): static
    {
        $jsonResourceFile = app_path('Http/Resources/Api/' . $this->model_name . 'Resource.php');
        if (File::exists($jsonResourceFile)) {
            $this->error('App\Http\Resources\Api\\' . $this->model_name . ' Resource Exists!');
            if (File::exists($jsonResourceFile) && !$this->forced) {
                return $this;
            } else {
                File::delete($jsonResourceFile);
            }
        }

        \Artisan::call('make:resource', [
            'name' => 'Api\\' . $this->model_name . 'Resource',
        ]);
        $this->info('App\Resources\Api\\' . $this->model_name . 'Resource Created!');
        return $this;
    }
}
