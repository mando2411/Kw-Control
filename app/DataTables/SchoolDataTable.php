<?php

namespace App\DataTables;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SchoolDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('type', fn(School $school) => $this->formatSchoolType($school->type))
            ->addColumn('election', fn(School $school) => $school->election?->name ?? 'عام')
            ->editColumn('committees_count', fn(School $school) => (int) ($school->committees_count ?? 0))
            ->editColumn('created_at', fn(School $school) => optional($school->created_at)->format('Y/m/d'))
            ->addColumn('action', 'dashboard.schools.action')
            ->filterColumn('election', function ($query, $keyword) {
                $query->whereHas('election', function ($electionQuery) use ($keyword) {
                    $electionQuery->where('name', 'like', "%{$keyword}%");
                });
            })
            
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    public function query(School $model): QueryBuilder
    {
        $query = $model->newQuery()->with('election')->withCount('committees');
        $hasSchoolElectionColumn = Schema::hasColumn('schools', 'election_id');

        if ($hasSchoolElectionColumn && auth()->check() && !auth()->user()->hasRole('Administrator')) {
            $query->where(function ($nested) {
                $nested->where('election_id', auth()->user()->election_id)
                    ->orWhereNull('election_id');
            });
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
            ->orderBy(4, 'desc')
            ->selectStyleSingle()
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'pageLength' => 25,
                'lengthMenu' => [[10, 25, 50, 100], [10, 25, 50, 100]],
                'language' => [
                    'search' => 'بحث:',
                    'searchPlaceholder' => 'ابحث في المدارس...',
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
            Column::make('name')->title('اسم المدرسة'),
            Column::make('type')->title('النوع'),
            Column::computed('election')->title('الحملة الانتخابية')->searchable(true),
            Column::make('committees_count')->title('عدد اللجان'),
            Column::make('created_at')->title('تاريخ الإنشاء'),
            Column::computed('action')
                  ->title('الإجراءات')
                  ->exportable(false)
                  ->printable(false)
                  ->width(90)
                  ->addClass('text-center text-nowrap'),
        ];
    }

    private function formatSchoolType(?string $type): string
    {
        $normalized = trim((string) $type);
        if ($normalized === '') {
            return '-';
        }

        $latin = strtolower($normalized);
        if (in_array($latin, ['male', 'males', 'men'], true) || in_array($normalized, ['ذكر', 'ذكور'], true)) {
            return 'ذكور';
        }

        if (in_array($latin, ['female', 'females', 'women'], true) || in_array($normalized, ['اناث', 'إناث', 'أنثى', 'انثى'], true)) {
            return 'إناث';
        }

        return $normalized;
    }

    protected function filename(): string
    {
        return 'School_' . date('YmdHis');
    }
}
