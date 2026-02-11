<?php

namespace App\Traits\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait FormRequestGenerator
{
    public function generateFormRequest(): static
    {
        $request_dir = app_path('Http/Requests/Dashboard');
        $request_file_name = $request_dir . '/' . $this->model_name . 'Request.php';
        File::ensureDirectoryExists($request_dir);
        if (File::exists($request_file_name)) {
            $this->error('App\Http\Requests\Dashboard\\' . $this->model_name . ' Request Exists!');
            if (File::exists($request_file_name) && !$this->forced) {
                return $this;
            } else {
                File::delete($request_file_name);
            }
        }

        $request = Str::of(File::get($this->stubs_path . '/request.stub'))
            ->replace('{{ originalAttributes }}', $this->generateOriginalRequestAttributes())
            ->replace('{{ translatedAttributes }}', $this->generateTranslatedAttributes())
            ->replace('{{ rules }}', $this->getRules())
            ->replace('{{ translated_fields_rules }}', $this->getTranslatedFieldsRules())
            ->replace('{{ class }}', $this->model_name);
        File::put($request_file_name, $request);
        $this->info('App\Http\Requests\Dashboard\\' . $this->model_name . ' Request Created!');
        return $this;
    }

    public function getRules(): string
    {
        $model = new $this->model_namespace;
        $fillables = $model->getFillable();
        $rules = "";
        foreach ($fillables as $fillable) {
            $rules .= "'" . $fillable . "' => ['required']," . PHP_EOL;
        }
        return $rules;
    }

    public function isTranslatedModel(): bool
    {
        $model = new $this->model_namespace;
        return property_exists($model, 'translatedAttributes') && !empty($model->translatedAttributes);
    }

    private function getTranslatedFieldsRules(): array|string
    {
        if (!$this->isTranslatedModel()) {
            return '';
        }
        $model = new $this->model_namespace;
        $field_rule = '$rules["$local.{{ attribute }}"]  = [$local == config("app.locale") ? "required": "nullable"];' . PHP_EOL;
        $fields = "";
        $whiteSpace = '            ';
        foreach ($model->translatedAttributes as $attribute) {
            $fields .= $whiteSpace . str_replace('{{ attribute }}', $attribute, $field_rule);
        }
        $fields_stub = File::get(base_path('custom-stubs/translated-field-rule.stub'));
        return str_replace('{{ fields }}', $fields, $fields_stub);
    }

    private function generateOriginalRequestAttributes(): string
    {
        $originalAttributes = '';
        foreach ($this->model_namespace->getFillable() as $k => $fillable) {
            if ($k == 0) {
                $originalAttributes .= PHP_EOL;
            }
            $originalAttributes .= '"' . $fillable . '" => "' . Str::studly($fillable) . '",' . PHP_EOL;
        }
        return $originalAttributes;
    }

    private function generateTranslatedAttributes(): string
    {
        if (!$this->isTranslatedModel()) {
            return '';
        }
        $attributes = '';
        foreach ($this->model_namespace->translatedAttributes as $column) {
            $columnName = Str::studly($column);
            $attributes .= '$attributes[$localKey.".' . $column . '"] =  $local["native"] ." ' . $columnName . '";' . PHP_EOL;
        }
        return Str::of(File::get($this->stubs_path . '/request-translate-attributes.stub'))
            ->replace("{{ columns }}", $attributes);
    }
}
