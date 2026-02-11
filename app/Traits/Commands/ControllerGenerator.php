<?php

namespace App\Traits\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait ControllerGenerator
{
    public function generateControllers(): static
    {
        return $this->generateResourceController()
            ->generateApiDocumentationController()
            ->generateApiResourceController();
    }

    public function generateResourceController(): static
    {
        $controllers_dir = app_path('Http/Controllers/Dashboard');
        File::ensureDirectoryExists($controllers_dir);
        $controller_file_name = $controllers_dir . '/' . $this->model_name . 'Controller.php';
        if (File::exists($controller_file_name)) {
            $this->error('App\Http\Controllers\Dashboard\\' . $this->model_name . 'Controller Exists!');
            if (File::exists($controller_file_name) && !$this->forced) {
                return $this;
            } else {
                File::delete($controller_file_name);
            }
        }

        $controller = Str::of(File::get($this->stubs_path . '/controller.model.stub'))
            ->replace('{{ namespacedModel }}', 'App\Models\\' . $this->model_name)
            ->replace('{{ modelVariable }}', $this->model_name->camel())
            ->replace('{{ viewFolder }}', $this->model_name->lower()->plural()->kebab())
            ->replace(['{{ model }}', '{{ class }}'], $this->model_name);
        File::put($controllers_dir . '/' . $this->model_name . 'Controller.php', $controller);
        $this->info('App\Http\Controllers\Dashboard\\' . $this->model_name . 'Controller Created!');
        return $this;
    }


    public function generateApiResourceController(): static
    {
        $controllers_dir = app_path('Http/Controllers/Api');
        File::ensureDirectoryExists($controllers_dir);
        $controller_file_name = $controllers_dir . '/' . $this->model_name . 'Controller.php';
        if (File::exists($controller_file_name)) {
            $this->error('App\Http\Controllers\Api\\' . $this->model_name . 'Controller Exists!');
            if (File::exists($controller_file_name) && !$this->forced) {
                return $this;
            } else {
                File::delete($controller_file_name);
            }
        }

        $controller = Str::of(File::get($this->stubs_path . '/api.resource.controller.stub'))
            ->replace(['{{ model }}', '{{ class }}'], $this->model_name)
            ->replace('{{ modelVariable }}', $this->model_name->camel())
            ->replace('{{ modelVariableCollection }}', $this->model_name->plural()->camel());
        File::put($controllers_dir . '/' . $this->model_name . 'Controller.php', $controller);
        $this->info('App\Http\Controllers\Dashboard\\' . $this->model_name . 'Controller Created!');
        return $this;
    }


    public function generateApiDocumentationController(): static
    {
        $controllers_dir = base_path('documentation');
        File::ensureDirectoryExists($controllers_dir);
        $controller_file_name = $controllers_dir . '/' . $this->model_name . 'Controller.php';
        if (File::exists($controller_file_name)) {
            $this->error('Documentation\\' . $this->model_name . 'Controller Exists!');
            if (File::exists($controller_file_name) && !$this->forced) {
                return $this;
            } else {
                File::delete($controller_file_name);
            }
        }

        $controller = Str::of(File::get($this->stubs_path . '/api.resource.controller.doc.stub'))
            ->replace(['{{ model }}', '{{ class }}'], $this->model_name)
            ->replace('{{ modelVariable }}', $this->model_name->camel())
            ->replace('{{ routePath }}', $this->model_name->plural()->lower()->kebab())
            ->replace('{{ modelNamePlural }}', $this->model_name->plural()->studly())
            ->replace('{{ modelVariableCollection }}', $this->model_name->plural()->camel());
        File::put($controllers_dir . '/' . $this->model_name . 'Controller.php', $controller);
        $this->info('Documentation\\' . $this->model_name . 'Controller Created!');
        return $this;
    }
}
