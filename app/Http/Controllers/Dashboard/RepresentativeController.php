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
use App\Models\Candidate;
use App\Services\Attendance;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;



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
        DB::transaction(function () use ($request, $userRequest) {
            $currentUser = auth()->user();
            $user = User::create($userRequest->getSanitized());

            $representativeRole = Role::where('name', 'مندوب')->first();
            if ($representativeRole) {
                $user->assignRole($representativeRole);
            }

            $representativeData = $request->getSanitized();
            $representativeData['user_id'] = $user->id;

            $committeeElectionId = $this->resolveCommitteeElectionId($representativeData['committee_id'] ?? null);
            if ($committeeElectionId !== null) {
                $representativeData['election_id'] = $representativeData['election_id'] ?? $committeeElectionId;
            }

            $representativeData['election_id'] = $representativeData['election_id'] ?? $user->election_id;

            $representative = Representative::create($representativeData);

            $selectedCandidateIds = collect((array) $request->input('candidate_ids', []))
                ->map(fn ($value) => (int) $value)
                ->filter(fn (int $value) => $value > 0)
                ->unique()
                ->values()
                ->all();

            if (!empty($selectedCandidateIds) && $currentUser && Schema::hasTable('candidate_representative')) {
                $allowedCandidateIds = $this->resolveListLeaderCandidateIds($currentUser);
                if (!empty($allowedCandidateIds)) {
                    $syncCandidateIds = array_values(array_intersect($selectedCandidateIds, $allowedCandidateIds));
                    if (!empty($syncCandidateIds)) {
                        $representative->candidates()->sync($syncCandidateIds);
                    }
                }
            }
        });

        session()->flash('message', 'تم اضافه مندوب بكلمه سر افتراضيه > (1) ');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.rep-home');
    }


    public function show(Representative $representative)
    {
        //
    }


    public function edit(Representative $representative)
    {
        $committeesQuery = Committee::query()->select(['id', 'name', 'type']);
        if (Schema::hasColumn('committees', 'election_id')) {
            $committeesQuery->addSelect('election_id');
        } else {
            $committeesQuery->selectRaw('NULL as election_id');
        }

        if (auth()->check() && !auth()->user()->hasRole('Administrator') && Schema::hasColumn('committees', 'election_id')) {
            $committeesQuery->where('election_id', (int) auth()->user()->election_id);
        }

        $relations = [
            'elections' => Election::query()->select('id', 'name')->orderBy('name')->get(),
            'roles' => Role::all(),
            'committees' => $committeesQuery->orderBy('name')->get(),
        ];

        return view('dashboard.representatives.edit', compact('representative', 'relations'));
    }


    public function update(RepresentativeRequest $request, Representative $representative ,UserRequest $userRequest)
    {
        $user = $representative->user;
        abort_if(!$user, 422, 'لا يوجد مستخدم مرتبط بهذا المندوب.');

        $representativeData = $request->getSanitized();
        $committeeElectionId = $this->resolveCommitteeElectionId($representativeData['committee_id'] ?? null);
        if ($committeeElectionId !== null) {
            $representativeData['election_id'] = $committeeElectionId;
        }

        $user->update($userRequest->getSanitized());
        $user->syncRoles(['مندوب']);
        $representative->update($representativeData);

        session()->flash('message', 'تم تحديث بيانات المندوب بنجاح');
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
            $committee_id = auth()->user()->representatives()->value('committee_id');

            if ($committee_id) {
                $committees = Committee::select('name', 'id', 'type')->where('id', $committee_id)->get()->map(function ($committee) {
                    $committee->title = "{$committee->name} ({$committee->type}) - {$committee->id}";
                    return $committee;
                });
            } else {
                $committees = collect();
                session()->flash('message', 'لم يتم ربطك بأي لجنة بعد.');
                session()->flash('type', 'warning');
            }
        }else{
            $committees = $this->attendance->getCommittees();
        }
        return view('dashboard.attendance.index', compact('committees'));
    }
    //================================================================================================
    public function home(Request $request){
        $user = auth('web')->user();
        $selectedSchoolId = (string) $request->input('id', 'all');
        $hasSchoolElectionColumn = Schema::hasColumn('schools', 'election_id');
        $hasCommitteeElectionColumn = Schema::hasColumn('committees', 'election_id');
        $hasUserCreatorColumn = Schema::hasColumn('users', 'creator_id');
        $userElectionId = (int) ($user?->election_id ?? 0);

        $isListLeaderUser = false;
        $listLeaderCandidates = collect();
        if ($user) {
            $allowedCandidateIds = $this->resolveListLeaderCandidateIds($user);
            $isListLeaderUser = !empty($allowedCandidateIds);

            if ($isListLeaderUser) {
                $listLeaderCandidates = Candidate::withoutGlobalScopes()
                    ->with('user:id,name')
                    ->whereIn('id', $allowedCandidateIds)
                    ->orderByRaw('CASE WHEN candidates.user_id = ? THEN 0 ELSE 1 END', [(int) $user->id])
                    ->orderBy('id')
                    ->get();
            }
        }

        $representativeCreatorScope = $this->resolveRepresentativeCreatorScope($user);
        $shouldScopeRepresentatives = is_array($representativeCreatorScope) && $hasUserCreatorColumn;

        $schoolsFilter = static function ($query) use ($hasSchoolElectionColumn, $userElectionId) {
            if ($hasSchoolElectionColumn) {
                $query->where(function ($nested) use ($userElectionId) {
                    $nested->where('election_id', $userElectionId)
                        ->orWhereNull('election_id');
                });
            }
        };

        $committeeElectionFilter = static function ($query) use ($hasCommitteeElectionColumn, $userElectionId) {
            if ($hasCommitteeElectionColumn) {
                $query->where('election_id', $userElectionId);
            }
        };

        $schoolsDropdownQuery = School::query()->select(['id', 'name', 'type']);

        $schoolsDataQuery = School::query()
            ->select(['id', 'name', 'type'])
            ->with([
                'committees' => function ($committeeQuery) use ($user, $committeeElectionFilter, $hasCommitteeElectionColumn, $shouldScopeRepresentatives, $representativeCreatorScope) {
                    $committeeQuery->select(['id', 'name', 'type', 'school_id']);
                    if ($hasCommitteeElectionColumn) {
                        $committeeQuery->addSelect('election_id');
                    }

                    if ($user && !$user->hasRole('Administrator')) {
                        $committeeElectionFilter($committeeQuery);
                    }

                    $committeeQuery->with([
                        'representatives' => function ($representativeQuery) use ($shouldScopeRepresentatives, $representativeCreatorScope) {
                            if ($shouldScopeRepresentatives) {
                                if (empty($representativeCreatorScope)) {
                                    $representativeQuery->whereRaw('1 = 0');
                                } else {
                                    $representativeQuery->whereHas('user', function ($userQuery) use ($representativeCreatorScope) {
                                        $userQuery->whereIn('creator_id', $representativeCreatorScope);
                                    });
                                }
                            }

                            $representativeQuery->with([
                                'user:id,name,phone,creator_id',
                                'candidates:id',
                            ]);
                        },
                    ]);
                },
            ]);

        $committeesQuery = Committee::query()->select(['id', 'name', 'type', 'school_id']);
        if ($hasCommitteeElectionColumn) {
            $committeesQuery->addSelect('election_id');
        }

        if ($user && !$user->hasRole('Administrator')) {
            $schoolsFilter($schoolsDropdownQuery);
            $schoolsFilter($schoolsDataQuery);

            $schoolsDropdownQuery->whereHas('committees', function ($committeeQuery) use ($committeeElectionFilter) {
                $committeeElectionFilter($committeeQuery);
            });

            $schoolsDataQuery->whereHas('committees', function ($committeeQuery) use ($committeeElectionFilter) {
                $committeeElectionFilter($committeeQuery);
            });

            $committeeElectionFilter($committeesQuery);
        }

        if ($selectedSchoolId !== 'all') {
            $selectedSchoolNumericId = (int) $selectedSchoolId;
            $schoolsDataQuery->where('id', $selectedSchoolNumericId);
            $committeesQuery->where('school_id', $selectedSchoolNumericId);
        }

        $relations = [
            'schools' => $schoolsDropdownQuery->orderBy('name')->get(),
            'committees' => $committeesQuery->orderBy('name')->get(),
            'is_list_leader_user' => $isListLeaderUser,
            'list_candidates' => $listLeaderCandidates,
        ];

        $schools = $schoolsDataQuery->orderBy('name')->get();

        return view('dashboard.representatives.home', compact('relations', 'schools', 'selectedSchoolId'));
    }

    private function resolveRepresentativeCreatorScope(?User $user): ?array
    {
        if (!$user || $user->hasRole('Administrator')) {
            return null;
        }

        $viewerId = (int) $user->id;
        $viewerElectionId = (int) ($user->election_id ?? 0);

        $listLeaderCandidateQuery = \App\Models\Candidate::withoutGlobalScopes()
            ->select(['id', 'election_id'])
            ->where('user_id', $viewerId)
            ->where('candidate_type', 'list_leader');

        if ($viewerElectionId > 0) {
            $listLeaderCandidateQuery->where('election_id', $viewerElectionId);
        }

        $listLeaderCandidate = $listLeaderCandidateQuery->latest('id')->first();

        if ($listLeaderCandidate) {
            $listMembersQuery = \App\Models\Candidate::withoutGlobalScopes()
                ->where(function ($query) use ($listLeaderCandidate) {
                    $query->where('id', (int) $listLeaderCandidate->id)
                        ->orWhere('list_leader_candidate_id', (int) $listLeaderCandidate->id);
                })
                ->whereNotNull('user_id');

            if ($viewerElectionId > 0) {
                $listMembersQuery->where('election_id', $viewerElectionId);
            }

            return $listMembersQuery
                ->pluck('user_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
        }

        $isCandidateUser = $user->hasRole('مرشح') || \App\Models\Candidate::withoutGlobalScopes()
            ->where('user_id', $viewerId)
            ->exists();

        if ($isCandidateUser) {
            return [$viewerId];
        }

        return null;
    }
    public function changeRep($id, Request $request){
        $rep = Representative::with(['user', 'candidates:id'])->findOrFail($id);

        abort_if(!$rep->user, 422, 'لا يوجد مستخدم مرتبط بهذا المندوب.');

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:15',
                Rule::unique('users')->ignore($rep->user->id),
            ],
            'committee_id' => 'nullable|integer|exists:committees,id',
            'candidate_ids' => 'nullable|array',
            'candidate_ids.*' => 'integer|exists:candidates,id',
        ]);

        $committeeId = $validatedData['committee_id'] ?? null;
        if ($committeeId !== null && !admin()->hasRole('Administrator') && Schema::hasColumn('committees', 'election_id')) {
            $committee = Committee::query()->select('id', 'election_id')->find($committeeId);
            abort_if($committee && (int) $committee->election_id !== (int) admin()->election_id, 422, 'اللجنة المختارة لا تتبع نفس الحملة الانتخابية.');
        }

        $rep->user->update(Arr::only($validatedData, ['name', 'phone']));

        if($committeeId !== null ) {
            $rep->update(['committee_id' => $committeeId]);
        }

        if (Schema::hasTable('candidate_representative') && $request->has('candidate_ids')) {
            $selectedCandidateIds = collect((array) $request->input('candidate_ids', []))
                ->map(fn ($value) => (int) $value)
                ->filter(fn (int $value) => $value > 0)
                ->unique()
                ->values()
                ->all();

            $currentUser = admin();
            $allowedCandidateIds = $this->resolveListLeaderCandidateIds($currentUser);

            if (!empty($allowedCandidateIds)) {
                $selectedCandidateIds = array_values(array_intersect($selectedCandidateIds, $allowedCandidateIds));
            }

            $rep->candidates()->sync($selectedCandidateIds);
        }

        return response()->json(
            [
                'message'=> " تم تعديل بيانات المندوب بنجاح"

            ]
        );
    }

    private function resolveCommitteeElectionId($committeeId): ?int
    {
        $committeeId = (int) $committeeId;
        if ($committeeId <= 0 || !Schema::hasColumn('committees', 'election_id')) {
            return null;
        }

        $committee = Committee::query()->select('id', 'election_id')->find($committeeId);
        if (!$committee || $committee->election_id === null) {
            return null;
        }

        return (int) $committee->election_id;
    }

    private function resolveListLeaderCandidateIds(User $user): array
    {
        $viewerId = (int) $user->id;
        $viewerElectionId = (int) ($user->election_id ?? 0);
        $hasActualCandidateColumn = Schema::hasColumn('candidates', 'is_actual_list_candidate');

        $listLeaderCandidateQuery = Candidate::withoutGlobalScopes()
            ->select(['id', 'election_id'])
            ->where('user_id', $viewerId)
            ->where('candidate_type', 'list_leader');

        if ($viewerElectionId > 0) {
            $listLeaderCandidateQuery->where('election_id', $viewerElectionId);
        }

        $listLeaderCandidate = $listLeaderCandidateQuery->latest('id')->first();
        if (!$listLeaderCandidate) {
            return [];
        }

        $listMembersQuery = Candidate::withoutGlobalScopes()
            ->where(function (Builder $query) use ($listLeaderCandidate) {
                $query->where('id', (int) $listLeaderCandidate->id)
                    ->orWhere('list_leader_candidate_id', (int) $listLeaderCandidate->id);
            });

        // Hide administrative-only list candidates from representative candidate selector.
        if ($hasActualCandidateColumn) {
            $listMembersQuery->where(function (Builder $query) {
                $query->where('is_actual_list_candidate', true)
                    ->orWhereNull('is_actual_list_candidate');
            });
        }

        if ($viewerElectionId > 0) {
            $listMembersQuery->where('election_id', $viewerElectionId);
        }

        return $listMembersQuery
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

}
