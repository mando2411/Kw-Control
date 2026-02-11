<?php

namespace App\Http\Controllers\Dashboard;

use PgSql\Lob;
use App\Models\Group;
use App\Models\Voter;
use App\Models\Family;
use App\Models\Candidate;
use App\Models\Committee;
use App\Models\Selection;
use App\Models\Contractor;
use App\Imports\VoterCheck;
use App\Services\Attendance;
use App\Services\VoterService;
use Illuminate\Http\Request;
use App\Imports\VotersImport;
use App\DataTables\VoterDataTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ContractorVotersImport;
use App\Http\Requests\Dashboard\VoterRequest;
use App\Http\Requests\Dashboard\ImportContractorVotersRequest;
use App\Enums\Type;
class VoterController extends Controller
{
    protected $attendance;
    //=====================================================================
    public function __construct(Attendance $attendance)
    {
        $this->attendance=$attendance;
    }
    //=====================================================================
    public function index(VoterDataTable $dataTable)
    {
        return $dataTable->render('dashboard.voters.index');
    }
    //=====================================================================
    public function create()
    {
        $relations=[
            'families' =>Family::select('name','id')->get(),
            'committees' => Committee::select('name','id')->get()
        ];
        return view('dashboard.voters.create',compact('relations'));
    }
    //=====================================================================
    public function store(VoterRequest $request)
    {
        $voter = Voter::create($request->getSanitized());
        session()->flash('message', 'Voter Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.voters.edit', $voter);
    }
    //=====================================================================
    public function show(Voter $voter)
    {
        //
    }
    //=====================================================================
    public function edit(Voter $voter)
    {
        $relations=[
            'families' =>Family::select('name','id')->get(),
            'committees' => Committee::select('name','id')->get()
        ];

        return view('dashboard.voters.edit', compact('voter','relations'));
    }
    //=====================================================================
    public function update(VoterRequest $request, Voter $voter)
    {
        $voter->update($request->getSanitized());
        session()->flash('message', 'Voter Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }
    //=====================================================================
    public function destroy(Voter $voter)
    {
        $voter->delete();
        return response()->json([
            'message' => 'Voter Deleted Successfully!'
        ]);
    }
    //=====================================================================
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
    //=====================================================================
    // public function updateStatus($id, Request $request)
    // {
    
    //     $this->attendance->status($id,$request);

    //     return redirect()->back();
    // }
    //=====================================================================
    public function updateStatus(Request $request, $id){
        try {  
            DB::beginTransaction();
            // Access the data sent from Ajax
            $request['status']    = $status    = $request->json('status');
            $request['committee'] = $committee = $request->json('committee');
            $voterId              = $request->json('voterId');
            Log::info(json_encode($request->all()));
            // $this->attendance->status($voterId,$request);
            $voter=Voter::find($voterId);

            $voter->update([
                'status'       => $status,
                'committee_id' => ($status==1)?$committee:null,
                'attend_id'    => ($status==1)?auth()->user()->id : null,
            ]);
        
            $this->attendance->counting($status);
            DB::commit();
            // Your status update logic here
            return response()->json([
                'success' => true,
                'message' => ($status==1)?'تم التحضير بنجاح ':'تم الغاء التحضير بنجاح',
                'data'    => $request->all(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطا اثناء التحديث',
                'data'    => $e->getMessage(),
                
            ], 500);
        }
    }
    //=====================================================================
    public function madameen(Request $request, VoterService $voterservice){
        $parents = Contractor::parents()->get()->map(fn($contractor) => [
            'id' => $contractor->id,
            'name' => $contractor->name
        ]);

        $committees = Committee::select('name', 'id')->get();

        $votersQuery = collect($request)->isNotEmpty()
            ? Voter::Madamen()
            : Voter::whereHas('contractors');

            // Log::info(json_encode($votersQuery->get()));
        if (!auth()->user()->hasRole("Administrator") && !auth()->user()->contractor) {
            $children = auth()->user()->contractors()->children();
            $votersIds = $children->get()->pluck('voters.*.id')->flatten()->unique();
            $votersQuery = $votersQuery->whereIn('id', $votersIds);
        }elseif(auth()->user()->contractor){
            $parents = auth()->user()->contractor()->get();
			$children = auth()->user()->contractor->childs;
            $votersIds = $children->pluck('voters.*.id')->flatten()->unique();;
            $votersQuery = $votersQuery->whereIn('id', $votersIds);
        }else{
            $children=Contractor::query();
        }
        $voters = $votersQuery->get();
        $overAll =$voterservice->VoterOverAll($votersQuery);
        return view('dashboard.voters.madameen', compact('voters','parents','committees','children','overAll',));
    }
    //=====================================================================
    public function importVotersFotContractorForm(){
        $candidates = Candidate::get();
        return view('dashboard.voters.import',compact('candidates'));
    }
    //=====================================================================
    public function importVotersFotContractor(ImportContractorVotersRequest $request){
        try {
            $import = new ContractorVotersImport($request->sub_contractor);
            Excel::import($import, $request->file('import'));
            
            return response()->json([
                'success'   => true,
                'message'   => 'File uploaded successfully',
                'data'      => [
                    'success_count' => $import->getSuccessCount(),
                    'failed_count'  => $import->getFailedCount(),
                    'repeat_count'  => $import->getRepeatedCount(),
                    'msg'           => ($import->getMsg()!='') ? $import->getMsg() : 'تاكد من ادراج ناخبين متواجدين بالفعل ضمن الانتخابات الخاصه بالمرشح',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'   => false,
                'message'   => 'Error uploading file: ' . $e->getMessage(),
                'data'      => [
                    'success_count' => 0,
                    'failed_count'  => 0,
                    'repeat_count'  => 0,
                    'msg'           => '',
                    
                ]
            ], 500);
        }
    }
    //=====================================================================
    public function updateVoterPhone(Request $request){
        try {  
            Log::info(json_encode($request->all()));
            
            DB::beginTransaction();
            // Access the data sent from Ajax
            $voter = Voter::find($request->json('voter_id'));
            $voter->update(['phone1' => $request->json('voter_phone')]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم تعديل رقم الهاتف بنجاح',
                'data'    => $request->all()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطا اثناء التعديل',
                'data'    => $e->getMessage(),
            ], 500);
        }
    }
    //=====================================================================
}