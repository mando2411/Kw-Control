<?php

namespace App\DataTables;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TourDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('enabled', fn(Tour $tour) => $tour->enabled ? 'Y' : 'N')
            ->editColumn('featured', fn(Tour $tour) => $tour->featured ? 'Y' : 'N')
            ->editColumn('created_at', fn(Tour $tour) => $tour->created_at->format('M Y, d'))
            ->addColumn('action', 'dashboard.tours.action')
            ->editColumn('title', fn(Tour $tour) => $tour->title)
            ->filterTranslatedColumn('title')
            ->orderColumn('title', fn($query, $dir) => $query->orderByTranslation('title', $dir))
            ->editColumn('slug', fn(Tour $tour) => $tour->slug)
            ->filterTranslatedColumn('slug')
            ->orderColumn('slug', fn($query, $dir) => $query->orderByTranslation('slug', $dir))
            ->setRowId('id')
            ->editColumn('featured_image', function (Tour $tour) {
                return '<img class="data-table-img-box" src="' . ($tour->featured_image ?? asset('assets/admin/images/placeholders/50x50.png')) . '" >';
            })
            ->rawColumns(['action','featured_image']);
    }

    public function query(Tour $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('data-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Blfrtip')
            ->orderBy(0)
            ->selectStyleSingle()
            ->buttons(array_reverse([
                Button::make('excel')->className('btn btn-sm float-right ms-1 p-1 text-light btn-success'),
                Button::make('csv')->className('btn btn-sm float-right ms-1 p-1 text-light btn-primary'),
                Button::make('print')->className('btn btn-sm float-right ms-1 p-1 text-light btn-secondary'),
                Button::make('reload')->className('btn btn-sm float-right ms-1 p-1 text-light btn-info')
            ]));
    }

    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('featured_image')->orderable(false)->searchable(false),
            Column::make('title'),
            Column::make('slug'),
            Column::make('adult_price'),
            Column::make('child_price'),
            Column::make('enabled'),
            Column::make('featured'),
            Column::make('created_at'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Tour_' . date('YmdHis');
    }
}
