<?php

namespace App\DataTables;

use App\Models\Election;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ElectionDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('start_date', fn(Election $election) => optional($election->start_date)->format('Y/m/d'))
            ->editColumn('end_date', fn(Election $election) => optional($election->end_date)->format('Y/m/d'))
            ->editColumn('start_time', fn(Election $election) => $election->start_time ? \Carbon\Carbon::parse($election->start_time)->format('H:i') : '')
            ->editColumn('end_time', fn(Election $election) => $election->end_time ? \Carbon\Carbon::parse($election->end_time)->format('H:i') : '')
            ->editColumn('created_at', fn(Election $election) => $election->created_at->format('Y/m/d'))
            ->addColumn('action', 'dashboard.elections.action')
            
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    public function query(Election $model): QueryBuilder
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
            //->dom('Bfrtip')
            ->orderBy(0)
            ->selectStyleSingle()
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'pageLength' => 25,
                'lengthMenu' => [[10, 25, 50, 100], [10, 25, 50, 100]],
                'language' => [
                    'search' => 'بحث:',
                    'searchPlaceholder' => 'ابحث في النتائج...',
                    'lengthMenu' => 'عرض _MENU_ سجل',
                    'info' => 'عرض _START_ إلى _END_ من أصل _TOTAL_ سجل',
                    'infoEmpty' => 'لا توجد سجلات متاحة',
                    'infoFiltered' => '(مفلترة من أصل _MAX_ سجل)',
                    'zeroRecords' => 'لا توجد نتائج مطابقة',
                    'emptyTable' => 'لا توجد بيانات حالياً',
                    'paginate' => [
                        'first' => 'الأول',
                        'last' => 'الأخير',
                        'next' => 'التالي',
                        'previous' => 'السابق',
                    ],
                    'processing' => 'جاري التحميل...',
                ],
            ])
            ->buttons(array_reverse([
                Button::make('excel')->text('تصدير Excel')->className('btn btn-sm float-right ms-1 p-1 text-light btn-success'),
                Button::make('csv')->text('تصدير CSV')->className('btn btn-sm float-right ms-1 p-1 text-light btn-primary'),
                Button::make('print')->text('طباعة')->className('btn btn-sm float-right ms-1 p-1 text-light btn-secondary'),
                Button::make('reload')->text('تحديث')->className('btn btn-sm float-right ms-1 p-1 text-light btn-info')
            ]));
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('الرقم'),
            Column::make('name')->title('اسم الانتخابات'),
            Column::make('start_date')->title('تاريخ البداية'),
            Column::make('end_date')->title('تاريخ النهاية'),
            Column::make('start_time')->title('وقت البداية'),
            Column::make('end_time')->title('وقت النهاية'),
            Column::make('type')->title('النوع'),
            Column::make('created_at')->title('تاريخ الإنشاء'),
            Column::computed('action')->title('الإجراءات')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Election_' . date('YmdHis');
    }
}
