<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Yajra\DataTables\QueryDataTable;

class DataTableServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        QueryDataTable::macro('filterTranslatedColumn', function ($column): QueryDataTable {
            return $this->filterColumn($column, function ($query, $keyword) use ($column) {
                return $query->whereTranslationLike($column, "%$keyword%");
            });
        });
    }
}
