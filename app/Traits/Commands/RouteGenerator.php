<?php

namespace App\Traits\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait RouteGenerator
{
    public function generateRoutes():static
    {
        return $this->generateDashboardResourceRoute()->generateApiResourceRoute();
    }
    public function generateDashboardResourceRoute(): static
    {
        $admin_routes_file_path = base_path('routes/admin.php');
        $routes_file = Str::of(File::get($admin_routes_file_path));
        $controller_comment = "//controllers";
        $route_line_comment = "//RoutePlace";

        $controllerName = $this->model_name . 'Controller';
        $resourceControllerNameSpace = "use App\Http\Controllers\Dashboard\\$controllerName;";

        $route_line = "Route::resource('".$this->model_name->plural()->kebab()."', ".$controllerName.'::class'.")->except('show');";

        if (!$this->forced && $routes_file->contains($route_line)) {
            $this->error('Dashboard Route Already Exists Created!');
            return $this;
        }

        $routes_file_content = $routes_file
            ->replace([$resourceControllerNameSpace,$route_line], '')
            ->replace($route_line_comment, $route_line. PHP_EOL .'    '. $route_line_comment)
            ->replace($controller_comment, $resourceControllerNameSpace . PHP_EOL . $controller_comment);

        //remove empty lines
        $routes_file_content = preg_replace("/(\R){2,}/", "$1", $routes_file_content->toString());
        File::put($admin_routes_file_path, $routes_file_content);
        $this->info('Dashboard Route Created!');
        return $this;
    }
    public function generateApiResourceRoute(): static
    {
        $api_routes_file_path = base_path('routes/api.php');
        $routes_file = Str::of(File::get($api_routes_file_path));
        $controller_comment = "//controllers";
        $route_line_comment = "//RoutePlace";

        $controllerName = $this->model_name . 'Controller';
        $resourceControllerNameSpace = "use App\Http\Controllers\Api\\$controllerName;";

        $route_line = Str::of(File::get($this->stubs_path.'/api.routes.stub'))
            ->replace('{{ controller }}', $this->model_name)
            ->replace('{{ prefix }}', $this->model_name->plural()->kebab()->lower());

        if (!$this->forced && $routes_file->contains($route_line)) {
            $this->error('API Route Already Exists Created!');
            return $this;
        }

        $routes_file_content = $routes_file
            ->replace([$resourceControllerNameSpace,$route_line], '')
            ->replace($route_line_comment, $route_line. PHP_EOL .PHP_EOL .'    '. $route_line_comment)
            ->replace($controller_comment, $resourceControllerNameSpace . PHP_EOL . $controller_comment);

        //remove empty lines
        $routes_file_content = preg_replace("/(\R){2,}/", "$1", $routes_file_content->toString());
        File::put($api_routes_file_path, $routes_file_content);
        $this->info('API Route Created!');
        return $this;
    }

}
