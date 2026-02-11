<?php

namespace App\Traits\Commands;

use Illuminate\Support\Facades\File;

trait ObserverGenerator
{
    public function generateModelObserver(): static
    {
        $observerFile = app_path('Observers/' . $this->model_name . 'Observer.php');
        if (File::exists($observerFile)) {
            $this->error('App\Observers\\' . $this->model_name . ' Observer Exists!');
            if (File::exists($observerFile) && !$this->forced) {
                return $this;
            } else {
                File::delete($observerFile);
            }
        }

        \Artisan::call('make:observer', [
            'name' => $this->model_name.'Observer',
            '--model' => 'App\Models\\' . $this->model_name
        ]);
        $this->info('App\Observers\\' . $this->model_name . 'Observer Created!');
        return $this;
    }
}
