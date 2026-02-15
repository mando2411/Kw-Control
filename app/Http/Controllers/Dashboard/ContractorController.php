<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Contractor;
use App\Models\User;
use App\Models\Election;
use App\Models\Role;
use App\Models\Family;
use App\Models\Voter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ContractorRequest;
use App\Http\Requests\Dashboard\UserRequest;
use App\DataTables\ContractorDataTable;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Helpers\ArabicHelper;


class ContractorController extends Controller
{

    public function index(ContractorDataTable $dataTable)
    {
            $parents = Contractor::parents()->where('creator_id',auth()->user()->id)->get()->map(fn($contractor)=>[
                'id'=>$contractor->id,
                'name'=>$contractor->name
            ]);

                if(auth()->user()->hasRole("Administrator")){
                    $children=Contractor::Children()->get();
                }elseif(auth()->user()->contractor){
					$parents = auth()->user()->contractor()->get();
                    $children=auth()->user()->contractor->childs;
                }
                else{
                    $children = auth()->user()->contractors()->Children()->get();
                }


            return view('dashboard.contractors.index', compact('parents','children'));
    }


    public function create()
    {
        $relations = [
            'elections' => Election::all(),
             'roles'=>Role::all(),
            'contractors' => Contractor::get()->map(fn($contractor)=>[
                'id'=>$contractor->id,
                'name'=>$contractor->name
            ])
            ];
        return view('dashboard.contractors.create',compact('relations'));
    }


    public function store(ContractorRequest $request)
    {
        $contractor = Contractor::create($request->getSanitized());
        $contractor->assignRole($request->get('roles'));
        session()->flash('message', 'Contractor Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->back();
    }
    public function contractor(ContractorRequest $request ,UserRequest $userRequest)
    {
        $user = User::create($userRequest->getSanitized());
        $user->assignRole($request->get('roles'));
        $user->assignRole(Role::whereName('متعهد')->first()->id);
        $request['user_id']=$user->id;
        $request['creator_id']=auth()->user()->id;
        $contractor = Contractor::create($request->all());
        session()->flash('message', 'Contractor Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->back();
    }


    public function show(Contractor $contractor)
    {
        //
    }


    public function edit(Contractor $contractor)
    {
        $relations = [
            'elections' => Election::all(),
             'roles'=>Role::all(),
            'contractors' => Contractor::get()->map(fn($contractor)=>[
                'id'=>$contractor->id,
                'name'=>$contractor->name
            ])
            ];

        return view('dashboard.contractors.edit', compact('contractor','relations'));
    }


    public function update(ContractorRequest $request ,UserRequest $userRequest, Contractor $contractor )
    {   $user=$contractor->user;
        $user->update($userRequest->getSanitized());
        $user->syncRoles($userRequest->get('roles'));
        $contractor->update($request->all());
        session()->flash('message', 'Contractor Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Contractor $contractor)
    {
        $user=$contractor;
        if($user){
            $user->forceDelete();
        }
        return response()->json([
            'message' => 'Contractor Deleted Successfully!'
        ]);
    }

    public function ass(Request $request, $id)
{
    $voterData = $request->input('voter');

    $contractor = Contractor::findOrFail($id);
    if($request->has('id')){
        $oldContractor=Contractor::where('id',$request->id)->first();
        
    }

    if (!empty($voterData)) {
       
        foreach ($voterData as $voterId) {
            $isInVoters = $contractor->voters()->where('voter_id', $voterId)->exists();
            $isInSoftDeletes = $contractor->softDelete()->where('voter_id', $voterId)->exists();

            if ($isInSoftDeletes) {
                $contractor->softDelete()->detach($voterId);
            }

            if (!$isInVoters) {
                $contractor->voters()->attach($voterId);
            }
			
			
            //if ($oldContractor) {
          //  $oldContractor->voters()->detach($voterId);
      //  }
            
        }

        session()->flash('message', 'تمت الاضافه بنجاح');
        session()->flash('type', 'success');
    } else {
        session()->flash('message', 'لم يتم اختيار اي ناخب');
        session()->flash('type', 'danger');
    }

    return redirect()->back();
}
    public function change($id, Request $request){
        $contractor=Contractor::findOrFail($id);
        if ($request->name == 'name' || $request->name == 'phone') {
            if($request->name == 'phone'){
                $request['phone']=$request->value;
                $validatedData = $request->validate([
                    'phone' => [
                        'nullable',
                        'string',
                        'max:15',
                        // Rule::unique('users')->ignore($contractor->id),
                                ],
                        'committee_id' =>'nullable'
                ]);
            }
            $contractor->update([
                $request->name => $request->value
            ]);
        }else{
            $contractor->update([
                $request->name => $request->value
            ]);
        }
        return response()->json([
            "message"=> "تم التعديل بنجاح"
        ]);
    }
    public function profile($token){
        $contractor=Contractor::where('token',$token)->first();
$families = Family::select('name', 'id')
    ->where('election_id', $contractor->creator->election_id)
    ->get();
		return view('dashboard.contractors.profile', compact('contractor','families'));
    }
    public function search(Request $request){
        $votersQuery = Voter::query();
        $log=[];
        if ($request->filled('name')) {
            $name = $request->input('name');
            $normalizedInput = ArabicHelper ::normalizeArabic($name);
            $votersQuery->where('normalized_name', 'LIKE', $normalizedInput . '%');
            $log[]="بحث بالاسم :".$name;
        }
        if ($request->filled('family')) {
            $family = $request->input('family');
            $votersQuery->where('family_id',$family);
            $family_name=Family::where('id',$family)->first()->name;
            $log[]="بحث العائله :".$family_name;
        }
        if ($request->filled('sibling')) {
            $sibling = $request->input('sibling');
            $votersQuery->where('father',$sibling);
            $log[]="بحث عن الاقارب  :". $sibling;
        }
        
        $contractor=Contractor::where('id',$request->id)->first();

		 $voters = $votersQuery->whereHas('election',function($q) use($contractor){
                $q->where('election_id',$contractor->creator->election_id);
            });
        $logString = implode(", ", $log);

        activity()
        ->causedBy($contractor)
        ->performedOn(new Voter())
        ->withProperties(['search_data' => $request->all()])
        ->event('Search')
        ->log($logString);

        if ($request->filled('id')) {
            $contractorId = $request->input('id');
            $votersQuery->whereDoesntHave('contractors', function ($query) use ($contractorId) {
                $query->where('contractor_id', $contractorId);
            });
     
        }
        $voters = $votersQuery->get();
        return response()->json([
            "voters"=>$voters,
        ]);
    }
    public function group(Request $request){
            $data=$request->validate([
                "name"=>["required","string"],
                "type"=>["required","string"],
                "contractor_id"=>["required"]
            ]);
            Group::create($data);
            return redirect()->back();
    }
    public function modify(Request $request){
        $contractor = Contractor::findOrFail($request->id);
        if($request->voters){
            if($request->select == 'delete'){
                $contractor->voters()->detach($request->voters);
                $contractor->softDelete()->attach($request->voters);
            }else{
                $group=Group::findOrFail($request->select);
                $group->voters()->attach($request->voters);
            }
        }else{
            session()->flash('message', 'لم يتم اختيار اي ناخب');
            session()->flash('type', 'danger');

        }
        return redirect()->back();
    }
    public function modify_g(Request $request){
        $contractor = Contractor::findOrFail($request->id);
        $group = Group::findOrFail($request->group_id);
        if($request->voters){
            $group->voters()->detach($request->voters);
            if($request->select == 'delete'){
                $contractor->voters()->detach($request->voters);
                $contractor->softDelete()->attach($request->voters);
            }elseif($request->select != 'delete-g'){
                $group=Group::findOrFail($request->select);
                $group->voters()->attach($request->voters);
            }
        }else{
            session()->flash('message', 'لم يتم اختيار اي ناخب');
            session()->flash('type', 'danger');

        }
        return redirect()->back();
    }
    public function delete_mad(Request $request){
        $contractor = Contractor::findOrFail($request->id);
            $contractor->voters()->detach($request->voter);
            $contractor->softDelete()->attach($request->voter);
            session()->flash('message', 'تمت الحذف بنجاح');
            session()->flash('type', 'success');
            return redirect()->back();
    }
}
