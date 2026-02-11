<?php

namespace App\DataTables;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BookingDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('date', fn(Booking $booking) => $booking->date->format('M Y, d'))
            ->addColumn('action', 'dashboard.booking.action')
            ->editColumn('name', fn(Booking $booking) => $booking->name)
            ->editColumn('email', fn(Booking $booking) => $booking->email)
            ->editColumn('phone', fn(Booking $booking) => $booking->phone)
            ->editColumn('message', fn(Booking $booking) => $booking->message)
            ->editColumn('adult_number', fn(Booking $booking) => $booking->adult_number)
            ->editColumn('child_number', fn(Booking $booking) => $booking->child_number)
            ->editColumn('price', fn(Booking $booking) => $booking->price)
            ->editColumn('tour_id', fn(Booking $booking) => $booking->tour?->title)
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    public function query(Booking $model): QueryBuilder
    {
        return $model->newQuery()->with('tour');
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
            Column::make('tour_id')->title('Tour'),
            Column::make('name'),
            Column::make('nationality'),
            Column::make('email'),
            Column::make('phone'),
            Column::make('date'),
            Column::make('message'),
            Column::make('adult_number')->title('Adults'),
            Column::make('child_number')->title('Children'),
            Column::make('infants_number')->title('Infants'),
             Column::make('price'),
//            Column::computed('action')
//                ->exportable(false)
//                ->printable(false)
//                ->width(60)
//                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Booking_' . date('YmdHis');
    }
}
