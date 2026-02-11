<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Representative;
use App\Models\User;
use App\Models\Election;
use App\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\UserRequest;
use App\Http\Requests\Dashboard\RepresentativeRequest;
use App\DataTables\RepresentativeDataTable;
use Illuminate\Http\Request;
use App\Models\Committee;
use App\Models\Voter;
use App\Models\School;
use App\Services\Attendance;
use Illuminate\Validation\Rule;



class RepresentativeController extends Controller
{

    protected $attendance;

    public function __construct(Attendance $attendance)
    {
        $this->attendance=$attendance;
    }
    public function index(RepresentativeDataTable $dataTable)
    {
        return $dataTable->render('dashboard.representatives.index');
    }


    public function create()
    {
        $relations = [
            'elections' => Election::all(),
             'roles'=>Role::all(),
        ];
        return view('dashboard.representatives.create' ,compact('relations'));
    }


    public function store(RepresentativeRequest $request ,UserRequest $userRequest)
    {
        $user = User::create($userRequest->getSanitized());
        $user->assignRole(Role::whereName('مندوب')->first()->id);
        $request['user_id']=$user->id;
        $representative = Representative::create($request->all());
        session()->flash('message', 'تم اضافه مندوب بكلمه سر افتراضيه > (1) ');
        session()->flash('type', 'success');
        return redirect()->back();
    }


    public function show(Representative $representative)
    {
        //
    }


    public function edit(Representative $representative)
    {
        $relations = [
            'elections' => Election::all(),
             'roles'=>Role::all(),
        ];
        return view('dashboard.representatives.edit', compact('representative','relations'));
    }


    public function update(RepresentativeRequest $request, Representative $representative ,UserRequest $userRequest)
    {
        $user=$representative->user;
        $user->update($userRequest->getSanitized());
        $user->syncRoles($userRequest->get('roles'));
        $representative->update($request->getSanitized());
        session()->flash('message', 'Representative Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Representative $representative)
    {
        $representative->delete();
        return response()->json([
            'message' => 'Representative Deleted Successfully!'
        ]);
    }
    //================================================================================================
    // public function attending(Request $request)//return blade,return voters when click show
    // {
    //     // Check if the user has a representative and assign the committee ID to the request
    //     if (auth()->user()->representatives()->exists()) {
    //         $request->merge(['committee' => auth()->user()->representatives()->first()->committee->id]);
    //     }

    //     // Retrieve formatted committees
    //     $committees = $this->attendance->getCommittees();

    //     // Retrieve filtered voters using the pipeline method
    //     $voters = $this->attendance->getVoters($request);

    //     if ($request->ajax()) {
    //         $view = view('dashboard.attendance.component.voters_list', compact('voters'))->render();  // Load only rows
    //         return response()->json([
    //             'html' => $view,
    //             'hasMorePages' => $voters->hasMorePages(),
    //             'nextPageUrl' => $voters->nextPageUrl(),  // Ensure this is properly returned
    //         ]);
    //     }
    //     return view('dashboard.attendance.index', compact('committees', 'voters'));
    // }
    //================================================================================================
    public function attending(Request $request){        
        // Check if the user has a representative and assign the committee ID to the request
        if (auth()->user()->representatives()->exists()) {
            $committee_id= auth()->user()->representatives()->first()->committee->id;
            // $committees= Committee::find($committee_id);
            
            $committees=Committee::select('name', 'id', 'type')->where('id',$committee_id)->get()->map(function ($committee) {
                $committee->title = "{$committee->name} ({$committee->type}) - {$committee->id}";
                return $committee;
            });
            
        }else{
            $committees = $this->attendance->getCommittees();
        }
        return view('dashboard.attendance.index', compact('committees'));
    }
    //================================================================================================
    public function home(Request $request){
        if (collect($request->all())->isEmpty() || $request->id=="all") {
            $relations=[
                'committees' => Committee::all(),
                'schools' => School::all(),
            ];
            return view('dashboard.representatives.home' ,compact('relations'));
        }else{
                $school=School::where('id',$request->id)->get();
                $relations=[
                'committees' => Committee::all(),
                'schools' => School::all(),
            ];
            return view('dashboard.representatives.home' ,compact('relations','school','request'));
        }
    }
    public function changeRep($id, Request $request){
        $rep = Representative::findOrFail($id);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:15',
                Rule::unique('users')->ignore($rep->user->id),             ],
                'committee_id' =>'nullable'
        ]);
        if($validatedData['committee_id'] == null ){
            unset($validatedData['committee_id']);
        }
        $rep->user->update($validatedData);
        $rep->update($validatedData);
        return response()->json(
            [
                'message'=> " تم تعديل بيانات المندوب بنجاح"

            ]
        );
    }

}
