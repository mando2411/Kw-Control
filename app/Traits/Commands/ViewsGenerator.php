<?php

namespace App\Traits\Commands;

use App\Traits\Models\HasSeo;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

trait ViewsGenerator
{
    private $viewPath = '';

    public function generateSideBarLink(): static
    {
        $sidebar_file_path = resource_path('views/layouts/dashboard/sidebar.blade.php');
        $commentSeparator = '            {{--Sidebar Auto Generation--}}';
        $link = Str::of(File::get($this->stubs_path . '/sidebar.link.stub'))
            ->replace('{{ modelNameSingular }}', $this->model_name->singular())
            ->replace('{{ routeName }}', $this->model_name->plural()->kebab())
            ->replace('{{ permissionPrefix }}', $this->model_name->plural()->kebab()->lower())
            ->replace('{{ modelNamePlural }}', $this->model_name->plural());
        $sidebar_file_content = Str::of(File::get($sidebar_file_path));

        if (!$sidebar_file_content->contains($link) || $this->forced) {
            $sidebar_file_content = $sidebar_file_content->replace($commentSeparator, $link . PHP_EOL . $commentSeparator);
            $this->info('Sidebar Link Created!');
        } else {
            $this->error('Link Exists!');
        }

        File::put($sidebar_file_path, $sidebar_file_content);
        return $this;
    }

    public function generateViews(): static
    {
        $this->viewPath = resource_path('views/dashboard/' . $this->model_name->plural()->kebab());
        File::ensureDirectoryExists($this->viewPath);
        return $this->generateIndexBlade()
            ->generateActionBlade()
            ->generateCreateBlade()
            ->generateEditBlade();
    }

    public function generateActionBlade(): static
    {
        $content = Str::of(File::get($this->stubs_path . '/action.blade.stub'))
            ->replace('{{ routeName }}', $this->model_name->lower()->plural()->kebab());
        File::put($this->viewPath . '/action.blade.php', $content);
        return $this;
    }

    public function generateIndexBlade(): static
    {
        $content = Str::of(File::get($this->stubs_path . '/index.blade.stub'))
            ->replace('{{ breadCrumbTitle }}', $this->model_name->plural())
            ->replace('{{ model }}', $this->model_name->lower()->singular());
        File::put($this->viewPath . '/index.blade.php', $content);
        return $this;
    }

    public function generateCreateBlade(): static
    {
        if (File::exists($this->viewPath . '/create.blade.php')) {
            $this->error('create.blade.php Exists!');
            if (!$this->forced)
                return $this;
            return $this;
        }
        $createBladeContent = Str::of(File::get($this->stubs_path . '/form.blade.stub'))
            ->replace('//translatableFields', $this->isTranslatedModel() ? $this->generateTranslatedFieldsForm() : '')
            ->replace('//basicFields', $this->generateBasicFieldsForm())
            ->replace('//SeoForm', $this->modelHasSeo() ? $this->getSeoForm() : '')
            ->replace('{{ routePrefix }}', $this->model_name->plural()->kebab())
            ->replace('{{ breadCrumbTitle }}', $this->model_name->plural()->studly())
            ->replace('{{ model }}', $this->model_name->plural()->kebab())
            ->replace('{{ breadCrumbMainTitle }}', 'Create '. $this->model_name)
            ->replace('{{ action }}', 'store')
            ->replace(':value=""', '')
            ->replace('{{ param }}', '');
        File::put($this->viewPath . '/create.blade.php', $createBladeContent);
        return $this;
    }

    public function generateEditBlade(): static
    {
        if (File::exists($this->viewPath . '/edit.blade.php')) {
            $this->error('edit.blade.php Exists!');
            if (!$this->forced)
                return $this;
        }
        $editBladeContent = Str::of(File::get($this->stubs_path . '/form.blade.stub'))
            ->replace('//translatableFields', $this->isTranslatedModel() ? $this->generateTranslatedFieldsForm('edit') : '')
            ->replace('//basicFields', $this->generateBasicFieldsForm('edit'))
            ->replace('//SeoForm', $this->modelHasSeo() ? $this->getSeoForm() : '')
            ->replace('{{ routePrefix }}', $this->model_name->plural()->kebab())
            ->replace('{{ breadCrumbTitle }}', $this->model_name->plural()->studly())
            ->replace('{{ model }}', $this->model_name->plural()->kebab())
            ->replace('{{ breadCrumbMainTitle }}', 'Edit '. $this->model_name)
            ->replace('{{ action }}', 'update')
            ->replace('@csrf', '@csrf'.PHP_EOL."        @method('PUT')")
            ->replace('{{ param }}', ', $' . $this->model_name->camel());
        File::put($this->viewPath . '/edit.blade.php', $editBladeContent);
        return $this;
    }

    public function getColumnsWithDataTypes(): array
    {
        $q = "SHOW COLUMNS FROM " . $this->model_namespace->getTable();
        $columns = [];
        foreach (DB::select($q) as $column) {
            $columns[$column->Field] = $column->Type;
        }
        if ($this->isTranslatedModel()) {
            $q = "SHOW COLUMNS FROM " . $this->model_name->singular()->camel() . '_translations';
            foreach (DB::select($q) as $column) {
                $columns[$column->Field] = $column->Type;
            }
        }
        return $columns;
    }

    private function generateTranslatedFieldsForm($mode = 'create'): Stringable
    {
        $translatedFieldsForm = Str::of(File::get($this->stubs_path . '/multi-tap-form.stub'));
        $inputTemplate = Str::of(File::get($this->stubs_path . '/input.stub'));
        $inputs = '';
        $columnsWithDataTypes = $this->getColumnsWithDataTypes();
        foreach ($this->model_namespace->translatedAttributes as $column) {
            $inputs .= $inputTemplate
                    ->replace('{{ model }}', $this->model_name)
                    ->replace('{{ inputType }}', $columnsWithDataTypes[$column] == 'longtext' ? 'editor' : 'text')
                    ->replace('{{ errorKey }}', '{{$localKey}}.' . $column)
                    ->replace('{{ inputName }}', '{{$localKey}}[' . $column . ']')
                    ->replace('{{ inputValue }}', $mode == 'create' ? '' : '$' . $this->model_name->camel() . '->translateOrNew($localKey)->' . $column)
                    ->replace('{{ inputId }}', '{{$localKey}}-' . $column)
                    ->replace('{{ inputLabel }}', Str::studly($column))
                . PHP_EOL;
        }
        return $translatedFieldsForm->replace('{{ inputs }}', $inputs);
    }

    private function generateBasicFieldsForm($mode = 'create'): Stringable
    {
        $translatedFieldsForm = Str::of(File::get($this->stubs_path . '/basic-fields-form.stub'));
        $inputTemplate = Str::of(File::get($this->stubs_path . '/input.stub'));
        $inputs = '';
        $columnsWithDataTypes = $this->getColumnsWithDataTypes();
        foreach ($this->model_namespace->getFillable() as $column) {
            $inputs .= $inputTemplate
                    ->replace('{{ inputType }}', $columnsWithDataTypes[$column] == 'longtext' ? 'editor' : 'text')
                    ->replace('{{ errorKey }}', $column)
                    ->replace('{{ inputName }}', $column)
                    ->replace('{{ inputValue }}', $mode == 'create' ? '' : '$' . $this->model_name->camel() . '->' . $column)
                    ->replace('{{ inputId }}', $column)
                    ->replace('{{ inputLabel }}', Str::studly($column))
                . PHP_EOL;
        }
        return $translatedFieldsForm->replace('{{ inputs }}', $inputs);
    }

    private function modelHasSeo(): bool
    {
        $this_class = get_class(new $this->model_namespace);

        $class_traits = class_uses_recursive($this_class);

        return in_array(HasSeo::class, $class_traits);
    }

    private function getSeoForm($mode = 'create'): Stringable
    {
        $translatedFieldsForm = Str::of(File::get($this->stubs_path . '/seo-form.stub'));
        return $translatedFieldsForm->replace("{{ seoObject }}", $mode == 'create' ? '' : '$' . $this->model_name->camel() . '->seo');
    }
}
