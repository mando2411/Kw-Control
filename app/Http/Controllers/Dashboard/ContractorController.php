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
use Illuminate\Support\Facades\DB;
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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'تمت الاضافه بنجاح',
                'status' => 'success',
            ]);
        }

        session()->flash('message', 'تمت الاضافه بنجاح');
        session()->flash('type', 'success');
    } else {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'لم يتم اختيار اي ناخب',
                'status' => 'error',
            ], 422);
        }

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
        $normalizedToken = trim((string) urldecode((string) $token));

        $contractor = Contractor::withoutGlobalScopes()
            ->with(['creator', 'election'])
            ->where('token', $normalizedToken)
            ->firstOrFail();
        $electionId = $contractor->election_id ?? optional($contractor->creator)->election_id;
        $families = Family::withoutGlobalScopes()
            ->select('name', 'id')
            ->when($electionId, function ($query) use ($electionId) {
                $query->where('election_id', $electionId);
            })
            ->get();
		return view('dashboard.contractors.profile', compact('contractor','families'));
    }
    public function search(Request $request){
        $contractorToken = trim((string) $request->input('contractor_token', ''));
        $isPortalContext = $contractorToken !== '';

        $votersQuery = $isPortalContext
            ? Voter::withoutGlobalScopes()
            : Voter::query();
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
            $familyModel = $isPortalContext
                ? Family::withoutGlobalScopes()->find($family)
                : Family::find($family);
            $family_name = $familyModel?->name;
            $log[]="بحث العائله :".$family_name;
        }
        if ($request->filled('sibling')) {
            $sibling = trim((string) $request->input('sibling'));
            $parts = preg_split('/\s+/u', $sibling, -1, PREG_SPLIT_NO_EMPTY);

            $tail = '';
            if (is_array($parts) && count($parts) > 1) {
                array_shift($parts);
                $tail = trim(implode(' ', $parts));
            }

            if ($tail !== '') {
                $normalizedTail = ArabicHelper::normalizeArabic($tail);
                $votersQuery->where(function ($query) use ($normalizedTail) {
                    $query->where('normalized_name', $normalizedTail)
                          ->orWhere('normalized_name', 'LIKE', $normalizedTail . ' %')
                          ->orWhere('normalized_name', 'LIKE', '% ' . $normalizedTail);
                });
                $log[] = "بحث عن الاخوة (بدون الاسم الاول): " . $tail;
            } else {
                $normalizedSibling = ArabicHelper::normalizeArabic($sibling);
                $votersQuery->where('normalized_name', 'LIKE', $normalizedSibling . '%');
                $log[] = "بحث عن الاخوة: " . $sibling;
            }

            if ($request->filled('sibling_exclude_id')) {
                $votersQuery->where('id', '!=', (int) $request->input('sibling_exclude_id'));
            }
        }
        
        $contractorQuery = $isPortalContext
            ? Contractor::withoutGlobalScopes()
            : Contractor::query();

        $contractor = $contractorQuery->find($request->id);
        if (!$contractor) {
            return response()->json(['voters' => []]);
        }

        if ($isPortalContext && !hash_equals((string) $contractor->token, $contractorToken)) {
            return response()->json(['voters' => []], 403);
        }

        $electionId = $contractor->election_id ?? optional($contractor->creator)->election_id;
        if ($electionId) {
            $votersQuery->whereHas('election', function ($q) use ($electionId) {
                $q->where('election_id', $electionId);
            });
        }
        $logString = implode(", ", $log);

        activity()
        ->causedBy($contractor)
        ->performedOn(new Voter())
        ->withProperties(['search_data' => $request->all()])
        ->event('Search')
        ->log($logString);

        if ($request->filled('id')) {
            $contractorId = (int) $request->input('id');
            $scope = (string) $request->input('scope', 'all');

            $attachedIdsSubQuery = DB::table('contractor_voter')
                ->select('voter_id')
                ->where('contractor_id', $contractorId);

            if ($scope === 'attached') {
                $votersQuery->whereIn('id', $attachedIdsSubQuery);
            } elseif ($scope === 'available') {
                $votersQuery->whereNotIn('id', $attachedIdsSubQuery);
            }

            if ((string) $request->input('exclude_grouped', '0') === '1') {
                $votersQuery->whereDoesntHave('groups', function ($query) use ($contractorId) {
                    $query->where('contractor_id', $contractorId);
                });
            }
        }
        $votersQuery->orderBy('name', 'asc');

        if ((string) $request->input('ids_only', '0') === '1') {
            $allIds = $votersQuery->pluck('id')
                ->map(fn ($value) => (int) $value)
                ->values();

            return response()->json([
                'ids' => $allIds,
                'total' => $allIds->count(),
            ]);
        }

        $perPage = (int) $request->input('per_page', 0);
        $page = max((int) $request->input('page', 1), 1);

        if ($perPage > 0) {
            $perPage = min(max($perPage, 1), 100);
            $paginated = $votersQuery->paginate($perPage, ['*'], 'page', $page);

            $items = collect($paginated->items());
            $itemIds = $items->pluck('id')->filter()->values();
            $attachedIds = [];

            if ($itemIds->isNotEmpty()) {
                $attachedIds = DB::table('contractor_voter')
                    ->where('contractor_id', $contractorId)
                    ->whereIn('voter_id', $itemIds->all())
                    ->pluck('voter_id')
                    ->map(fn ($value) => (int) $value)
                    ->all();
            }

            $attachedLookup = array_flip($attachedIds);
            $mappedItems = $items->map(function ($voter) use ($attachedLookup) {
                $voter->is_added = isset($attachedLookup[(int) $voter->id]);
                return $voter;
            })->values();

            return response()->json([
                "voters" => $mappedItems,
                "pagination" => [
                    "total" => $paginated->total(),
                    "per_page" => $paginated->perPage(),
                    "current_page" => $paginated->currentPage(),
                    "last_page" => $paginated->lastPage(),
                    "has_more" => $paginated->hasMorePages(),
                ],
            ]);
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
            $group = Group::create($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'تم إنشاء القائمة بنجاح',
                    'group' => $group,
                ]);
            }

            return redirect()->back();
    }
    public function modify(Request $request){
        $contractor = Contractor::findOrFail($request->id);
        if($request->voters){
            if($request->select == 'delete'){
                $contractor->voters()->detach($request->voters);
                $contractor->softDelete()->attach($request->voters);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'message' => 'تم الحذف بنجاح',
                        'status' => 'success',
                    ]);
                }
            }else{
                $group=Group::findOrFail($request->select);
                $group->voters()->attach($request->voters);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'message' => 'تم النقل بنجاح',
                        'status' => 'success',
                    ]);
                }
            }
        }else{
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'لم يتم اختيار اي ناخب',
                    'status' => 'error',
                ], 422);
            }

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
