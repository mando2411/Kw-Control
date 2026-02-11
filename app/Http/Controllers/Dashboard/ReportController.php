<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\VotersExport;
use App\Models\Candidate;
use App\Models\Committee;
use App\Models\Contractor;
use App\Models\Election;
use App\Models\Report;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ReportRequest;
use App\DataTables\ReportDataTable;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ReportController extends Controller
{

    public function index(ReportDataTable $dataTable)
    {
        return $dataTable->render('dashboard.reports.index');
    }


    public function create()
    {
        $relations=[
            'elections'=>Election::select('name','id')->get(),
            'candidates'=>Candidate::get()->map(function($candidate){
                return [
                    'id'=>$candidate->id,
                    'name'=>$candidate->user->name,
                ];
            }),
            'contractors'=>Contractor::Children()->select('name','id','creator_id')->get(),
            'committees'=>Committee::select('name','id')->get(),
        ];
        return view('dashboard.reports.create',compact('relations'));
    }


    public function store(ReportRequest $request, ReportService $reportService)
    {
        $data=$reportService->getReports($request);
        if($request->type == "PDF"){
            Report::create($data);
            session()->flash('message', 'Report Created Successfully!');
            session()->flash('type', 'success');
            return response()->download($data['pdf_url'], 'Voters.pdf', [
                'Content-Type' => 'application/pdf',
            ]);
        }else{
            return Excel::download(new ReportExport($data, request('columns')), 'Voters.xlsx');
        }
        return redirect()->back();
    }


    public function show(Report $report)
    {
        //
    }


    public function edit(Report $report)
    {
        return view('dashboard.reports.edit', compact('report'));
    }


    public function update(ReportRequest $request, Report $report)
    {
        $report->update($request->getSanitized());
        session()->flash('message', 'Report Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Report $report)
    {
        $report->delete();
        return response()->json([
            'message' => 'Report Deleted Successfully!'
        ]);
    }
}
