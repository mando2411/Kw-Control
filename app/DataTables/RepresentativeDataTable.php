<?php

namespace App\DataTables;

use App\Models\Candidate;
use App\Models\Representative;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RepresentativeDataTable extends DataTable
{
    private ?Collection $creatorCandidateOptions = null;

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('representative_name', fn(Representative $representative) => $representative->user?->name ?? 'غير محدد')
            ->addColumn('election', fn(Representative $representative) => $representative->election?->name ?? 'غير محددة')
            ->addColumn('created_by', function (Representative $representative) {
                $creatorName = (string) ($representative->user?->creator?->name ?? 'غير محدد');
                $creatorId = (int) ($representative->user?->creator_id ?? 0);

                if (!auth()->check() || !auth()->user()->can('representatives.edit')) {
                    return e($creatorName);
                }

                $optionsHtml = '<option value="">-- اختر المرشح --</option>';
                $representativeElectionId = (int) ($representative->election_id ?? 0);
                $currentCreatorRendered = false;

                foreach ($this->resolveCreatorCandidateOptions() as $option) {
                    $optionUserId = (int) ($option['user_id'] ?? 0);
                    $optionElectionId = (int) ($option['election_id'] ?? 0);

                    if ($representativeElectionId > 0 && $optionElectionId > 0 && $optionElectionId !== $representativeElectionId) {
                        continue;
                    }

                    $selected = $optionUserId === $creatorId ? ' selected' : '';
                    if ($selected !== '') {
                        $currentCreatorRendered = true;
                    }
                    $optionLabel = e((string) ($option['name'] ?? 'غير محدد'));
                    $optionsHtml .= '<option value="' . $optionUserId . '"' . $selected . '>' . $optionLabel . '</option>';
                }

                if ($creatorId > 0 && !$currentCreatorRendered) {
                    $optionsHtml .= '<option value="' . $creatorId . '" selected>' . e($creatorName) . ' (الحالي)</option>';
                }

                return '<div class="rep-created-by-editor d-flex flex-column gap-1">'
                    . '<select class="form-select form-select-sm js-rep-creator-select" data-rep-id="' . (int) $representative->id . '">' . $optionsHtml . '</select>'
                    . '<button type="button" class="btn btn-sm btn-outline-primary js-save-rep-creator" data-rep-id="' . (int) $representative->id . '">حفظ</button>'
                    . '</div>';
            })
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
                ->rawColumns(['created_by', 'action']);
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

    private function resolveCreatorCandidateOptions(): Collection
    {
        if ($this->creatorCandidateOptions !== null) {
            return $this->creatorCandidateOptions;
        }

        $currentUser = auth()->user();
        $query = Candidate::withoutGlobalScopes()
            ->join('users', 'users.id', '=', 'candidates.user_id')
            ->select('candidates.user_id', 'users.name', 'candidates.election_id')
            ->distinct();

        if ($currentUser && !$currentUser->hasRole('Administrator')) {
            $userElectionId = (int) ($currentUser->election_id ?? 0);
            if ($userElectionId > 0) {
                $query->where('candidates.election_id', $userElectionId);
            }
        }

        $this->creatorCandidateOptions = $query
            ->orderBy('users.name')
            ->get()
            ->map(function ($row) {
                return [
                    'user_id' => (int) ($row->user_id ?? 0),
                    'name' => (string) ($row->name ?? ''),
                    'election_id' => (int) ($row->election_id ?? 0),
                ];
            })
            ->filter(fn (array $row) => (int) ($row['user_id'] ?? 0) > 0)
            ->values();

        return $this->creatorCandidateOptions;
    }
}
