<?php

namespace App\Traits\Commands;

use DB;
use Illuminate\Support\Facades\File;
use Str;

trait DataTableGenerator
{
    public function generateDateTable(): static
    {
        $dataTableFile = app_path('DataTables/' . $this->model_name . 'DataTable.php');
        if (File::exists($dataTableFile)) {
            $this->error('App\DataTables\\' . $this->model_name . ' DataTable Exists!');
            if (File::exists($dataTableFile) && !$this->forced) {
                return $this;
            } else {
                File::delete($dataTableFile);
            }
        }

        \Artisan::call('datatables:make', [
            'name' => $this->model_name . 'DataTable',
            '--model' => $this->model_name,
            '--columns' => $this->getColumns(),
            '--action' => 'dashboard.' . $this->model_name->lower()->kebab()->plural() . '.action'
        ]);

        $this->handleTranslatedColumns();
        $this->info('App\DataTables\\' . $this->model_name . ' DataTable Created!');
        return $this;
    }

    public function getColumns(): string
    {
        $q = "SHOW COLUMNS FROM " . $this->model_namespace->getTable();
        $columns = [];
        foreach (DB::select($q) as $column) {
            $columns[] = $column->Field;
        }
        $columns = $this->isTranslatedModel() ?
            array_merge($columns, $this->model_namespace->translatedAttributes) :
            $columns;
        $rejected_columns = $this->getRejectedColumns();
        $columns = array_filter($columns, fn($col) => !in_array($col, $rejected_columns));
        $modelHidden = $this->model_namespace->getHidden();
        $columns = array_filter($columns, fn($column) => !in_array($column, $modelHidden));
        return implode(',', $columns);
    }

    public function handleTranslatedColumns(): void
    {
        $replacement = '';
        if ($this->isTranslatedModel()) {
            $dataTableTranslatedColumnTemp = File::get($this->stubs_path . '/datatable-translation-column.stub');
            foreach ($this->model_namespace->translatedAttributes as $translatedAttribute) {
                $replacement .= Str::of($dataTableTranslatedColumnTemp)
                        ->replace('{{ modelVariable }}', $this->model_name->camel())
                        ->replace('{{ model }}', $this->model_name)
                        ->replace('{{ column }}', $translatedAttribute) . PHP_EOL;
            }
        }

        $path = app_path('DataTables/' . $this->model_name . 'DataTable.php');
        $content = Str::of(File::get($path))
            ->replace('//TranslationColumns', $replacement)
            ->replace('{{ modelVariable }}', $this->model_name->camel())
            ->replace('{{ model }}', $this->model_name);
        File::put($path, $content);
    }

    private function getRejectedColumns(): array
    {
        return [
            'body',
            'content',
            'description',
            'keywords',
            'includes',
            'excludes',
            'deleted_at',
            'updated_at',
        ];
    }
}
