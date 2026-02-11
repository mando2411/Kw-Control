<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Committee;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CommitteeRequest;
use App\DataTables\CommitteeDataTable;
use App\Models\Election;
use Illuminate\Http\Request;
use App\Models\School;
use App\Services\Query\CommitteeGenerator;
use App\Events\CommitteeUpdate;


class CommitteeController extends Controller
{
    protected $generator;

    public function __construct(CommitteeGenerator $generator)
    {
        $this->generator = $generator;
    }

    public function index(CommitteeDataTable $dataTable)
    {
        return $dataTable->render('dashboard.committees.index');
    }


    public function create()
    {
        $relations = [
            'elections' => Election::all(),
        ];
        return view('dashboard.committees.create',compact('relations'));
    }


    public function store(CommitteeRequest $request)
    {
        $committee = Committee::create($request->getSanitized());
        session()->flash('message', 'Committee Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.committees.edit', $committee);
    }


    public function show(Committee $committee)
    {
        //
    }


    public function edit(Committee $committee)
    {
        $relations = [
            'elections' => Election::all(),
        ];
        return view('dashboard.committees.edit', compact('committee','relations'));
    }


    public function update(CommitteeRequest $request, Committee $committee)
    {
        $committee->update($request->getSanitized());
        session()->flash('message', 'Committee Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }

    public function generate(){

        $relations=[
            'elections'=>Election::select('id','name')->get()
        ];

        return view('dashboard.committees.multi',compact('relations'));
    }

    public function multi(Request $request)
    {
        $validateData = $request->validate([
            'men' => ['required','integer','min:1'],
            'women' => ['required','integer','min:1'],
            'election_id' => ['required']
        ]);
        // Generate Multi Committees Using Enums
        $message = $this->generator->createRecords($validateData);

        session()->flash('message', $message);
        session()->flash('type', 'success');
        return redirect()->back();
    }

    public function destroy(Committee $committee)
    {
        $committee->delete();
        return response()->json([
            'message' => 'Committee Deleted Successfully!'
        ]);
    }
    public function home(Request $request){
        if (collect($request->all())->isEmpty() || $request->id=="all") {
            $relations=[
                'schools' => School::with('committees')->get(),
            ];
            return view('dashboard.committees.committee' ,compact('relations'));
        }else{
                $school=School::where('id',$request->id)->get();
                $relations=[
                'schools' => School::with('committees')->get(),
            ];
            return view('dashboard.committees.committee' ,compact('relations','school','request'));
        }
    }

    public function status(Request $request,$id){
        $committee=Committee::find($id);
        if($committee){
            $committee->update([
                'status'=>$request->status
            ]);
            event(new CommitteeUpdate($committee));

        }
        return  response()->json(
            [
                'status'=>$committee->status
            ]
        );
    }
}
