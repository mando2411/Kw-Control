<?php

namespace App\DataTables;

use App\Models\Representative;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RepresentativeDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('representative_name', fn(Representative $representative) => $representative->user?->name ?? 'غير محدد')
            ->addColumn('election', fn(Representative $representative) => $representative->election?->name ?? 'غير محددة')
            ->addColumn('created_by', fn(Representative $representative) => $representative->user?->creator?->name ?? 'غير محدد')
            ->editColumn('created_at', fn(Representative $representative) => optional($representative->created_at)->format('Y/m/d'))
            ->addColumn('action', 'dashboard.representatives.action')
            ->filterColumn('representative_name', function ($query, $keyword) {
                $query->whereHas('user', function ($userQuery) use ($keyword) {
                    $userQuery->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('election', function ($query, $keyword) {
                $query->whereHas('election', function ($electionQuery) use ($keyword) {
                    $electionQuery->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('created_by', function ($query, $keyword) {
                $query->whereHas('user.creator', function ($creatorQuery) use ($keyword) {
                    $creatorQuery->where('name', 'like', "%{$keyword}%");
                });
            })
            
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    public function query(Representative $model): QueryBuilder
    {
        $query = $model->newQuery()->with([
            'user:id,name,creator_id',
            'user.creator:id,name',
            'election:id,name',
        ]);

        $hasRepresentativeElectionColumn = Schema::hasColumn('representatives', 'election_id');
        if ($hasRepresentativeElectionColumn && auth()->check() && !auth()->user()->hasRole('Administrator')) {
            $query->where('election_id', (int) auth()->user()->election_id);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('data-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Blrtip')
            ->orderBy(3, 'desc')
            ->selectStyleSingle()
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'pageLength' => 25,
                'lengthMenu' => [[10, 25, 50, 100], [10, 25, 50, 100]],
                'language' => [
                    'search' => 'بحث:',
                    'searchPlaceholder' => 'ابحث في المندوبين...',
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
                Button::make('excel')->text('تصدير إكسل')->className('btn btn-sm float-right ms-1 p-1 text-light btn-success'),
                Button::make('csv')->text('تصدير سي إس في')->className('btn btn-sm float-right ms-1 p-1 text-light btn-primary'),
                Button::make('print')->text('طباعة')->className('btn btn-sm float-right ms-1 p-1 text-light btn-secondary'),
                Button::make('reload')->text('تحديث')->className('btn btn-sm float-right ms-1 p-1 text-light btn-info')
            ]));
    }

    public function getColumns(): array
    {
        return [
            Column::computed('representative_name')->title('اسم المندوب')->searchable(true),
            Column::computed('election')->title('الحملة الانتخابية')->searchable(true),
            Column::computed('created_by')->title('المرشح الذي أضاف المندوب')->searchable(true),
            Column::make('created_at')->title('تاريخ الإنشاء'),
            Column::computed('action')
                  ->title('الإجراءات')
                  ->exportable(false)
                  ->printable(false)
                  ->width(90)
                  ->addClass('text-center text-nowrap'),
        ];
    }

    protected function filename(): string
    {
        return 'Representative_' . date('YmdHis');
    }
}
