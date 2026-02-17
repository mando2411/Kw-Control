<?php

namespace App\DataTables;

use App\Models\Candidate;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CandidateDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('name', fn(Candidate $candidate) => $candidate->user->name ?? '-') // Fetch user name
            ->editColumn('election', fn(Candidate $candidate) => $candidate->election->name ?? '-') // Fetch election name
            ->editColumn('created_at', fn(Candidate $candidate) => $candidate->created_at->format('M Y, d'))
            ->addColumn('action', 'dashboard.candidates.action')
            ->setRowId('id')
            ->rawColumns(['action']);
    }

    public function query(Candidate $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['user', 'election']);

        if (Auth::check()) {
            $listLeaderCandidate = Candidate::withoutGlobalScopes()
                ->select('id')
                ->where('user_id', (int) Auth::id())
                ->where('candidate_type', 'list_leader')
                ->first();

            if ($listLeaderCandidate) {
                $query->where(function ($nestedQuery) use ($listLeaderCandidate) {
                    $nestedQuery
                        ->where('candidates.id', (int) $listLeaderCandidate->id)
                        ->orWhere('candidates.list_leader_candidate_id', (int) $listLeaderCandidate->id);
                });
            }
        }

        return $query;
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
            Column::make('name')->title('Candidate Name'), // Display candidate's name
            Column::make('election')->title('Election Name'), // Display election name
            Column::make('max_contractor'),
            Column::make('max_represent'),
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
        return 'Candidate_' . date('YmdHis');
    }
}
