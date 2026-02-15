<?php

namespace App\Console\Commands;

use App\Traits\Commands\ControllerGenerator;
use App\Traits\Commands\DataTableGenerator;
use App\Traits\Commands\FormRequestGenerator;
use App\Traits\Commands\JsonApiResourceGenerator;
use App\Traits\Commands\ObserverGenerator;
use App\Traits\Commands\PermissionGenerator;
use App\Traits\Commands\RouteGenerator;
use App\Traits\Commands\ViewsGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class AdminGenerateCommand extends Command
{
    use FormRequestGenerator,
        DataTableGenerator,
        ObserverGenerator,
        JsonApiResourceGenerator,
        RouteGenerator,
        ViewsGenerator,
        PermissionGenerator,
        ControllerGenerator;

    protected $signature = 'admin:generate
                            {name : The name of the DataTable.}
                            {--f|forced}';

    protected $description = 'Generate Admin Module';
    private string $stubs_path;
    protected Stringable $model_name;
    private string|array|bool $forced;
    private mixed $model_namespace;

    public function handle(): void
    {
        $this->call('migrate');
        $this->model_name = Str::of($this->argument('name'))->studly()->singular();
        $this->model_namespace = new ('App\Models\\' . $this->model_name);
        $this->stubs_path = base_path('custom-stubs');
        $this->forced = $this->option('forced');
        $this->generateControllers()
            ->generateFormRequest()
            ->generateModelObserver()
            ->generateJsonResource()
            ->generateDateTable()
            ->generateViews()
            ->generateRoutes()
            ->seedPermissions()
            ->generateSideBarLink();
    }
}
