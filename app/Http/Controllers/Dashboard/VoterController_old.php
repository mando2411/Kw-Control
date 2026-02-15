<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Voter;
use App\Models\Family;
use App\Models\Committee;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\VoterRequest;
use App\DataTables\VoterDataTable;
use Illuminate\Http\Request;
use App\Imports\VotersImport;
use App\Imports\VoterCheck;
use App\Models\Contractor;
use App\Models\Group;
use App\Models\Selection;
use App\Services\Attendance;
use Maatwebsite\Excel\Facades\Excel;


class VoterController_old extends Controller
{
    protected $attendance;

    public function __construct(Attendance $attendance)
    {
        $this->attendance=$attendance;
    }
    public function index(VoterDataTable $dataTable)
    {
        return $dataTable->render('dashboard.voters.index');
    }

//test
    public function create()
    {
        $relations=[
            'families' =>Family::select('name','id')->get(),
            'committees' => Committee::select('name','id')->get()
        ];
        return view('dashboard.voters.create',compact('relations'));
    }


    public function store(VoterRequest $request)
    {
        $voter = Voter::create($request->getSanitized());
        session()->flash('message', 'Voter Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.voters.edit', $voter);
    }


    public function show(Voter $voter)
    {
        //
    }


    public function edit(Voter $voter)
    {
        $relations=[
            'families' =>Family::select('name','id')->get(),
            'committees' => Committee::select('name','id')->get()
        ];

        return view('dashboard.voters.edit', compact('voter','relations'));
    }


    public function update(VoterRequest $request, Voter $voter)
    {
        $voter->update($request->getSanitized());
        session()->flash('message', 'Voter Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Voter $voter)
    {
        $voter->delete();
        return response()->json([
            'message' => 'Voter Deleted Successfully!'
        ]);
    }
    public function import(Request $request){
        $request->validate([
            'import' => 'required|mimes:xlsx,xls,csv',
            'check' => 'required',
            'election' => 'required'

        ]);
        if(request('check') == "replace"){
            Voter::with(['contractors', 'groups'])->each(function ($voter) {
                $voter->contractors()->detach();
                $voter->groups()->detach();
                $voter->delete();
            });
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            Voter::truncate();
            Selection::truncate();
            Family::truncate();
            Group::truncate();

            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        ini_set('memory_limit', '2048M');

        ini_set('max_execution_time', 300); // 5 minutes
        if(request('check')=="status"){
            Excel::import(new VoterCheck($request->election), $request->file('import'));
        }else{
            Excel::import(new VotersImport($request->election), $request->file('import'));
        }
        // dd($request->all());
        return redirect()->back();
    }

    public function updateStatus($id, Request $request)
    {

        $this->attendance->status($id,$request);

        return redirect()->back();
    }
    public function madameen(Request $request){
        $parents = Contractor::parents()->get()->map(fn($contractor) => [
            'id' => $contractor->id,
            'name' => $contractor->name
        ]);

        $committees = Committee::select('name', 'id')->get();

        $votersQuery = collect($request)->isNotEmpty()
            ? Voter::Madamen()
            : Voter::whereHas('contractors');


        if (!auth()->user()->hasRole("Administrator")) {
            $children = auth()->user()->contractors()->children();
            $votersIds = $children->get()->pluck('voters.*.id')->flatten()->unique();
            $votersQuery = $votersQuery->whereIn('id', $votersIds);
        }else{
            $children=Contractor::query();
        }
        $voters = $votersQuery->get();

        return view('dashboard.voters.madameen', compact('voters','parents','committees','children'));
    }


}
