<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Role;
use App\Models\User;
use App\Models\School;
use App\Models\Setting;
use App\Models\Election;
use App\Models\Candidate;
use App\Models\Contractor;
use App\Models\Committee;
use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Services\VoteService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\DataTables\CandidateDataTable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use App\Http\Requests\Dashboard\UserRequest;
use App\Http\Requests\Dashboard\CandidateRequest;

class CandidateController extends Controller
{
    //================================================================================
    use ImageTrait;
    //================================================================================
    public function index(CandidateDataTable $dataTable)
    {
        $currentListLeaderCandidate = $this->currentListLeaderCandidate();

        $elections = Election::all();
        $candidatesQuery = Candidate::with([
            'election',
            'user' => function ($query) {
                $query->withCount(['contractors', 'representatives']);
            },
        ])->latest();

        $this->applyListLeaderVisibilityScope($candidatesQuery, $currentListLeaderCandidate);

        $candidates = $candidatesQuery->get();
        $isListLeaderCandidate = $currentListLeaderCandidate !== null;

        return $dataTable->render('dashboard.candidates.index', compact('elections', 'candidates', 'isListLeaderCandidate', 'currentListLeaderCandidate'));
    }

    public function listManagement(Request $request)
    {
        $currentListLeaderCandidate = $this->currentListLeaderCandidate();

        $canAccess = admin()->can('candidates.list') || $currentListLeaderCandidate;
        abort_if(!$canAccess, 403);

        $isListManagementContext = true;
        $listManagementCandidates = collect();
        $selectedCandidateUserIds = [];

        if ($currentListLeaderCandidate) {
            $listManagementCandidates = Candidate::withoutGlobalScopes()
                ->with('user')
                ->where(function (Builder $query) use ($currentListLeaderCandidate) {
                    $query
                        ->where('id', (int) $currentListLeaderCandidate->id)
                        ->orWhere('list_leader_candidate_id', (int) $currentListLeaderCandidate->id);
                })
                ->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [(int) $currentListLeaderCandidate->id])
                ->latest('id')
                ->get();

            $allowedCandidateUserIds = $listManagementCandidates
                ->pluck('user_id')
                ->filter()
                ->map(fn ($value) => (int) $value)
                ->unique()
                ->values()
                ->all();

            $requestedCandidateUserIds = collect((array) $request->input('candidate_users', []))
                ->filter(fn ($value) => $value !== null && $value !== '' && (string) $value !== 'all')
                ->map(fn ($value) => (int) $value)
                ->unique()
                ->values();

            $selectedCandidateUserIds = $requestedCandidateUserIds
                ->filter(fn (int $value) => in_array($value, $allowedCandidateUserIds, true))
                ->values()
                ->all();

            if (empty($selectedCandidateUserIds)) {
                $selectedCandidateUserIds = $allowedCandidateUserIds;
            }

            $contractorsBaseQuery = Contractor::withoutGlobalScopes()
                ->where('election_id', (int) $currentListLeaderCandidate->election_id)
                ->whereIn('creator_id', $selectedCandidateUserIds);

            $parents = (clone $contractorsBaseQuery)
                ->whereNull('parent_id')
                ->latest('id')
                ->get()
                ->map(fn ($contractor) => [
                    'id' => $contractor->id,
                    'name' => $contractor->name,
                ]);

            $children = (clone $contractorsBaseQuery)
                ->whereNotNull('parent_id')
                ->latest('id')
                ->get();
        } else {
            $parents = Contractor::parents()->where('creator_id', auth()->id())->get()->map(fn ($contractor) => [
                'id' => $contractor->id,
                'name' => $contractor->name,
            ]);

            if (auth()->user()->hasRole('Administrator')) {
                $children = Contractor::Children()->get();
            } elseif (auth()->user()->contractor) {
                $parents = auth()->user()->contractor()->get();
                $children = auth()->user()->contractor->childs;
            } else {
                $children = auth()->user()->contractors()->Children()->get();
            }
        }

        return view('dashboard.contractors.index', compact(
            'parents',
            'children',
            'isListManagementContext',
            'listManagementCandidates',
            'selectedCandidateUserIds',
            'currentListLeaderCandidate'
        ));
    }

    public function listManagementVoters(Request $request)
    {
        $currentListLeaderCandidate = $this->currentListLeaderCandidate();

        $canAccess = admin()->can('candidates.list') || $currentListLeaderCandidate;
        abort_if(!$canAccess, 403);

        if (!$currentListLeaderCandidate) {
            return response()->json([
                'success' => true,
                'total' => 0,
                'html' => view('dashboard.contractors.partials.list-management-voters-table', ['rows' => collect()])->render(),
            ]);
        }

        $listManagementCandidates = Candidate::withoutGlobalScopes()
            ->with('user')
            ->where(function (Builder $query) use ($currentListLeaderCandidate) {
                $query
                    ->where('id', (int) $currentListLeaderCandidate->id)
                    ->orWhere('list_leader_candidate_id', (int) $currentListLeaderCandidate->id);
            })
            ->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [(int) $currentListLeaderCandidate->id])
            ->latest('id')
            ->get();

        $allowedCandidateUserIds = $listManagementCandidates
            ->pluck('user_id')
            ->filter()
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values()
            ->all();

        $requestedCandidateUserIds = collect((array) $request->input('candidate_users', []))
            ->filter(fn ($value) => $value !== null && $value !== '' && (string) $value !== 'all')
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values();

        $selectedCandidateUserIds = $requestedCandidateUserIds
            ->filter(fn (int $value) => in_array($value, $allowedCandidateUserIds, true))
            ->values()
            ->all();

        if (empty($selectedCandidateUserIds)) {
            $selectedCandidateUserIds = $allowedCandidateUserIds;
        }

        $contractorIds = Contractor::withoutGlobalScopes()
            ->where('election_id', (int) $currentListLeaderCandidate->election_id)
            ->whereIn('creator_id', $selectedCandidateUserIds)
            ->pluck('id')
            ->map(fn ($value) => (int) $value)
            ->filter()
            ->values();

        $rows = collect();
        if ($contractorIds->isNotEmpty()) {
            $voterCivilIdColumn = Schema::hasColumn('voters', 'civil_id')
                ? 'v.civil_id'
                : 'v.alrkm_almd_yn';

            $rows = DB::table('contractor_voter as cv')
                ->join('voters as v', 'v.id', '=', 'cv.voter_id')
                ->leftJoin('families as f', 'f.id', '=', 'v.family_id')
                ->leftJoin('committees as cm', 'cm.id', '=', 'v.committee_id')
                ->leftJoin('contractors as c', 'c.id', '=', 'cv.contractor_id')
                ->leftJoin('users as cu', 'cu.id', '=', 'c.creator_id')
                ->whereIn('cv.contractor_id', $contractorIds->all())
                ->select([
                    'v.id as voter_id',
                    'v.name as voter_name',
                    DB::raw($voterCivilIdColumn . ' as civil_id'),
                    'f.name as family_name',
                    'cm.name as committee_name',
                    'cu.name as candidate_name',
                    'c.name as contractor_name',
                    'cv.created_at as attached_at',
                ])
                ->orderByDesc('cv.created_at')
                ->get();

            $duplicateCounts = $rows
                ->groupBy(fn ($row) => (int) ($row->voter_id ?? 0))
                ->map(fn ($group) => $group->count());

            $rows = $rows->map(function ($row) use ($duplicateCounts) {
                $key = (int) ($row->voter_id ?? 0);
                $repeatCount = (int) ($duplicateCounts[$key] ?? 0);
                $row->is_duplicate = $repeatCount > 1;
                $row->duplicate_count = $repeatCount;

                return $row;
            });
        }

        return response()->json([
            'success' => true,
            'total' => $rows->count(),
            'html' => view('dashboard.contractors.partials.list-management-voters-table', ['rows' => $rows])->render(),
        ]);
    }

    public function listManagementVoterDetails(Request $request, int $voter)
    {
        try {
            $scope = $this->resolveListManagementScope($request);
            abort_if(!$scope['can_access'], 403);

            $contractorIds = $scope['contractor_ids'];

            $civilIdExpr = 'NULL';
            if (Schema::hasColumn('voters', 'civil_id')) {
                $civilIdExpr = 'v.civil_id';
            } elseif (Schema::hasColumn('voters', 'alrkm_almd_yn')) {
                $civilIdExpr = 'v.alrkm_almd_yn';
            }

            $voterInfo = DB::table('voters as v')
                ->where('v.id', (int) $voter)
                ->select([
                    'v.id',
                    'v.name',
                    DB::raw($civilIdExpr . ' as civil_id'),
                ])
                ->first();

            abort_if(!$voterInfo, 404);

            $assignments = collect();
            if (!empty($contractorIds)) {
                $assignments = DB::table('contractor_voter as cv')
                    ->join('contractors as c', 'c.id', '=', 'cv.contractor_id')
                    ->leftJoin('users as cu', 'cu.id', '=', 'c.creator_id')
                    ->where('cv.voter_id', (int) $voter)
                    ->whereIn('cv.contractor_id', $contractorIds)
                    ->select([
                        'cv.contractor_id',
                        'c.name as contractor_name',
                        'c.creator_id as candidate_user_id',
                        'cu.name as candidate_name',
                        'cv.created_at as attached_at',
                    ])
                    ->orderByDesc('cv.created_at')
                    ->get()
                    ->map(function ($row) {
                        $row->attached_at = $row->attached_at
                            ? \Carbon\Carbon::parse($row->attached_at)->format('Y/m/d H:i')
                            : null;
                        return $row;
                    })
                    ->values();
            }

            return response()->json([
                'success' => true,
                'voter' => $voterInfo,
                'assignments' => $assignments,
            ]);
        } catch (\Throwable $exception) {
            Log::error('listManagementVoterDetails failed', [
                'voter_id' => (int) $voter,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'تعذر تحميل تفاصيل المضمون. يرجى المحاولة مرة أخرى.',
            ], 500);
        }
    }

    public function listManagementContractorsByCandidate(Request $request)
    {
        $scope = $this->resolveListManagementScope($request);
        abort_if(!$scope['can_access'], 403);

        $candidateUserId = (int) $request->input('candidate_user_id', 0);
        if (!$candidateUserId || !in_array($candidateUserId, $scope['allowed_candidate_user_ids'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'المرشح غير متاح ضمن القائمة.',
            ], 422);
        }

        $contractors = Contractor::withoutGlobalScopes()
            ->where('election_id', (int) $scope['leader_election_id'])
            ->where('creator_id', $candidateUserId)
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'contractors' => $contractors,
        ]);
    }

    public function listManagementAddVotersContractors(Request $request)
    {
        $scope = $this->resolveListManagementScope($request);
        abort_if(!$scope['can_access'], 403);

        $contractors = Contractor::withoutGlobalScopes()
            ->from('contractors as c')
            ->leftJoin('users as u', 'u.id', '=', 'c.creator_id')
            ->where('c.election_id', (int) $scope['leader_election_id'])
            ->whereIn('c.creator_id', $scope['selected_candidate_user_ids'])
            ->whereNotNull('c.parent_id')
            ->orderBy('u.name')
            ->orderBy('c.name')
            ->get([
                'c.id',
                'c.name',
                'c.creator_id as candidate_user_id',
                DB::raw('COALESCE(u.name, "مرشح") as candidate_name'),
            ]);

        return response()->json([
            'success' => true,
            'contractors' => $contractors,
        ]);
    }

    public function listManagementAddVotersSourceVoters(Request $request)
    {
        $scope = $this->resolveListManagementScope($request);
        abort_if(!$scope['can_access'], 403);

        $contractorId = (int) $request->input('contractor_id', 0);
        $search = trim((string) $request->input('search', ''));

        if (!$contractorId || !in_array($contractorId, $scope['contractor_ids'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'المتعهد غير متاح ضمن النطاق.',
            ], 422);
        }

        $civilIdExpr = 'NULL';
        if (Schema::hasColumn('voters', 'civil_id')) {
            $civilIdExpr = 'v.civil_id';
        } elseif (Schema::hasColumn('voters', 'alrkm_almd_yn')) {
            $civilIdExpr = 'v.alrkm_almd_yn';
        }

        if ($search === '') {
            $rows = DB::table('contractor_voter as cv')
                ->join('voters as v', 'v.id', '=', 'cv.voter_id')
                ->where('cv.contractor_id', $contractorId)
                ->select([
                    'v.id as voter_id',
                    'v.name as voter_name',
                    DB::raw($civilIdExpr . ' as civil_id'),
                    'v.phone1 as phone',
                ])
                ->orderByDesc('cv.created_at')
                ->limit(300)
                ->get();
        } else {
            $query = DB::table('voters as v')
                ->select([
                    'v.id as voter_id',
                    'v.name as voter_name',
                    DB::raw($civilIdExpr . ' as civil_id'),
                    'v.phone1 as phone',
                ])
                ->where(function ($subQuery) use ($search, $civilIdExpr) {
                    $subQuery
                        ->where('v.name', 'like', '%' . $search . '%')
                        ->orWhere('v.phone1', 'like', '%' . $search . '%');

                    if ($civilIdExpr !== 'NULL') {
                        $subQuery->orWhereRaw($civilIdExpr . ' like ?', ['%' . $search . '%']);
                    }
                })
                ->orderBy('v.name');

            $rows = $query->limit(300)->get();
        }

        $attachedMap = [];
        if ($rows->isNotEmpty()) {
            $attachedMap = DB::table('contractor_voter')
                ->where('contractor_id', $contractorId)
                ->whereIn('voter_id', $rows->pluck('voter_id')->map(fn ($id) => (int) $id)->all())
                ->pluck('voter_id')
                ->map(fn ($id) => (int) $id)
                ->flip()
                ->toArray();
        }

        $rows = $rows
            ->map(function ($row) use ($attachedMap) {
                $voterId = (int) ($row->voter_id ?? 0);
                $isAttached = $voterId > 0 && array_key_exists($voterId, $attachedMap);

                return [
                    'voter_id' => $voterId,
                    'voter_name' => (string) ($row->voter_name ?? ''),
                    'civil_id' => (string) ($row->civil_id ?? ''),
                    'phone' => (string) ($row->phone ?? ''),
                    'is_attached' => $isAttached,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'rows' => $rows,
            'total' => $rows->count(),
        ]);
    }

    public function listManagementAddVoterToContractor(Request $request, int $voter)
    {
        $scope = $this->resolveListManagementScope($request);
        abort_if(!$scope['can_access'], 403);

        $contractorId = (int) $request->input('contractor_id', 0);
        if (!$contractorId || !in_array($contractorId, $scope['contractor_ids'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'المتعهد المحدد غير متاح.',
            ], 422);
        }

        DB::transaction(function () use ($contractorId, $voter) {
            DB::table('contractor_voter_delete')
                ->where('contractor_id', $contractorId)
                ->where('voter_id', (int) $voter)
                ->delete();

            $alreadyAttached = DB::table('contractor_voter')
                ->where('contractor_id', $contractorId)
                ->where('voter_id', (int) $voter)
                ->exists();

            if (!$alreadyAttached) {
                DB::table('contractor_voter')->insert([
                    'contractor_id' => $contractorId,
                    'voter_id' => (int) $voter,
                    'percentage' => '0',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة المضمون للمتعهد المحدد بنجاح.',
        ]);
    }

    public function listManagementDeleteVoterAssignment(Request $request, int $voter)
    {
        $scope = $this->resolveListManagementScope($request);
        abort_if(!$scope['can_access'], 403);

        $sourceContractorId = (int) $request->input('source_contractor_id', 0);
        if (!$sourceContractorId || !in_array($sourceContractorId, $scope['contractor_ids'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'المتعهد غير متاح ضمن نطاق القائمة.',
            ], 422);
        }

        $deleted = 0;
        DB::transaction(function () use (&$deleted, $sourceContractorId, $voter) {
            $deleted = DB::table('contractor_voter')
                ->where('contractor_id', $sourceContractorId)
                ->where('voter_id', (int) $voter)
                ->delete();

            if ($deleted > 0) {
                $alreadyDeleted = DB::table('contractor_voter_delete')
                    ->where('contractor_id', $sourceContractorId)
                    ->where('voter_id', (int) $voter)
                    ->exists();

                if (!$alreadyDeleted) {
                    DB::table('contractor_voter_delete')->insert([
                        'contractor_id' => $sourceContractorId,
                        'voter_id' => (int) $voter,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        if ($deleted < 1) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على هذا الربط للحذف.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المضمون بنجاح.',
        ]);
    }

    public function listManagementTransferVoterAssignment(Request $request, int $voter)
    {
        $scope = $this->resolveListManagementScope($request);
        abort_if(!$scope['can_access'], 403);

        $sourceContractorId = (int) $request->input('source_contractor_id', 0);
        $targetCandidateUserId = (int) $request->input('target_candidate_user_id', 0);
        $targetContractorId = (int) $request->input('target_contractor_id', 0);

        if (!$sourceContractorId || !in_array($sourceContractorId, $scope['contractor_ids'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'المتعهد المصدر غير متاح.',
            ], 422);
        }

        if (!$targetCandidateUserId || !in_array($targetCandidateUserId, $scope['allowed_candidate_user_ids'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'المرشح الهدف غير متاح.',
            ], 422);
        }

        $targetContractor = Contractor::withoutGlobalScopes()
            ->where('id', $targetContractorId)
            ->where('election_id', (int) $scope['leader_election_id'])
            ->where('creator_id', $targetCandidateUserId)
            ->first();

        if (!$targetContractor) {
            return response()->json([
                'success' => false,
                'message' => 'المتعهد الهدف غير متاح للمرشح المختار.',
            ], 422);
        }

        if ((int) $targetContractor->id === $sourceContractorId) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى اختيار متعهد مختلف عن المصدر.',
            ], 422);
        }

        $pivotRow = DB::table('contractor_voter')
            ->where('contractor_id', $sourceContractorId)
            ->where('voter_id', (int) $voter)
            ->first();

        if (!$pivotRow) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على هذا الربط للنقل.',
            ], 404);
        }

        DB::transaction(function () use ($pivotRow, $sourceContractorId, $targetContractor, $voter) {
            DB::table('contractor_voter')
                ->where('contractor_id', $sourceContractorId)
                ->where('voter_id', (int) $voter)
                ->delete();

            DB::table('contractor_voter_delete')
                ->where('contractor_id', (int) $targetContractor->id)
                ->where('voter_id', (int) $voter)
                ->delete();

            $existingTarget = DB::table('contractor_voter')
                ->where('contractor_id', (int) $targetContractor->id)
                ->where('voter_id', (int) $voter)
                ->exists();

            if ($existingTarget) {
                DB::table('contractor_voter')
                    ->where('contractor_id', (int) $targetContractor->id)
                    ->where('voter_id', (int) $voter)
                    ->update([
                        'percentage' => (string) ($pivotRow->percentage ?? 0),
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('contractor_voter')->insert([
                    'contractor_id' => (int) $targetContractor->id,
                    'voter_id' => (int) $voter,
                    'percentage' => (string) ($pivotRow->percentage ?? 0),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'تم نقل المضمون بنجاح.',
        ]);
    }

    public function result()
    {
        $candidate_name = 'مرشح الفرز العام';
        $election_id    = auth()->user()->election_id;
        $candidates     = $this->fetchCondidatesBasedOnElection($election_id, $candidate_name);
        $committees = Committee::all();
        $schools = School::orderBy('id', 'desc')->get();
        return view('dashboard.resualt.index', compact('candidates', 'committees', 'schools'));
    }

    public function create()
    {
        $currentListLeaderCandidate = $this->currentListLeaderCandidate();
        $listMembersCount = 0;
        $listRemainingSlots = null;

        if ($currentListLeaderCandidate) {
            $listMembersCount = $this->effectiveListCandidatesCount($currentListLeaderCandidate);

            $allowedMembers = max(0, (int) ($currentListLeaderCandidate->list_candidates_count ?? 0));
            $listRemainingSlots = max(0, $allowedMembers - $listMembersCount);
        }

        $relations = [
            'elections' => $currentListLeaderCandidate
                ? Election::where('id', $currentListLeaderCandidate->election_id)->get()
                : Election::all(),
            'roles' => Role::all(),
        ];
        return view('dashboard.candidates.create', compact('relations', 'currentListLeaderCandidate', 'listMembersCount', 'listRemainingSlots'));
    }


    public function store(CandidateRequest $request, UserRequest $userRequest)
    {
        $currentListLeaderCandidate = $this->currentListLeaderCandidate();

        if ($currentListLeaderCandidate) {
            $allowedMembers = max(0, (int) ($currentListLeaderCandidate->list_candidates_count ?? 0));
            $currentMembersCount = $this->effectiveListCandidatesCount($currentListLeaderCandidate);

            if ($currentMembersCount >= $allowedMembers) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'list_candidates_count' => 'لا يمكن إضافة مرشحين جدد. تم الوصول للحد الأقصى المسموح به لهذه القائمة.',
                    ]);
            }
        }

        try {
            $candidate = DB::transaction(function () use ($request, $userRequest, $currentListLeaderCandidate) {
                $userData = $userRequest->getSanitized();
                if ($currentListLeaderCandidate) {
                    $userData['election_id'] = $currentListLeaderCandidate->election_id;
                }

                $user = User::create($userData);

                $candidateData = $request->getSanitized();
                $candidateData['user_id'] = $user->id;
                $candidateData['candidate_type'] = (string) ($candidateData['candidate_type'] ?? 'candidate');
                $candidateData['is_actual_list_candidate'] = (bool) ($candidateData['is_actual_list_candidate'] ?? true);

                if ($currentListLeaderCandidate) {
                    $candidateData['candidate_type'] = 'candidate';
                    $candidateData['list_leader_candidate_id'] = $currentListLeaderCandidate->id;
                    $candidateData['list_name'] = (string) ($currentListLeaderCandidate->list_name ?? '');
                    $candidateData['list_logo'] = (string) ($currentListLeaderCandidate->list_logo ?? '');
                    $candidateData['election_id'] = $currentListLeaderCandidate->election_id;
                    $candidateData['list_candidates_count'] = null;
                } elseif ((string) $candidateData['candidate_type'] !== 'list_leader') {
                    $candidateData['list_candidates_count'] = null;
                    $candidateData['list_name'] = null;
                    $candidateData['list_logo'] = null;
                    $candidateData['list_leader_candidate_id'] = null;
                }

                $candidate = Candidate::create($candidateData);

                $assignedRoles = ['مرشح'];
                if ($candidate->isListLeader()) {
                    $assignedRoles[] = 'مرشح رئيس قائمة';
                }

                foreach ($assignedRoles as $roleName) {
                    Role::findOrCreate($roleName, 'web');
                }

                $user->syncRoles($assignedRoles);

                if ($candidate->election) {
                    $committees = $candidate->election->committees->pluck('id')->toArray();
                    $candidate->committees()->sync($committees);
                }

                return $candidate;
            });
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'candidate' => 'تعذر حفظ المرشح. يرجى مراجعة البيانات والمحاولة مرة أخرى.',
                ]);
        }

        session()->flash('message', 'Candidate Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.candidates.edit', $candidate);
    }


    public function show(Candidate $candidate)
    {
        //
    }

    public function publicProfile(string $slug)
    {
        $rawSlug = trim(urldecode($slug));
        $candidateId = null;

        if (preg_match('/-(\d+)$/', $rawSlug, $matches)) {
            $candidateId = (int) $matches[1];
        }

        $namePart = $candidateId
            ? preg_replace('/-\d+$/', '', $rawSlug)
            : $rawSlug;

        $nameFromSlug = trim(str_replace('-', ' ', $namePart));

        $candidateQuery = Candidate::withoutGlobalScopes()
            ->with([
                'election',
                'user' => function ($query) {
                    $query->withCount(['contractors', 'representatives']);
                },
            ]);

        if ($candidateId) {
            $candidate = (clone $candidateQuery)->findOrFail($candidateId);
        } else {
            $candidate = (clone $candidateQuery)
                ->whereHas('user', function ($query) use ($rawSlug, $nameFromSlug) {
                    $query->where('name', $nameFromSlug)
                        ->orWhere('name', $rawSlug)
                        ->orWhereRaw("REPLACE(name, ' ', '-') = ?", [$rawSlug])
                        ->orWhereRaw("REPLACE(name, ' ', '-') = ?", [Str::replace(' ', '-', $nameFromSlug)]);
                })
                ->firstOrFail();
        }

        return view('public.candidates.profile', compact('candidate'));
    }


    public function edit(Candidate $candidate)
    {
        $this->ensureListLeaderCanManageCandidate($candidate);

        $currentListLeaderCandidate = $this->currentListLeaderCandidate();
        $relations = [
            'elections' => $currentListLeaderCandidate
                ? Election::where('id', $currentListLeaderCandidate->election_id)->get()
                : Election::all(),
            'roles' => Role::all(),
        ];
        return view('dashboard.candidates.edit', compact('candidate', 'relations', 'currentListLeaderCandidate'));
    }


    public function update(CandidateRequest $request, Candidate $candidate, UserRequest $userRequest)
    {   
        $this->ensureListLeaderCanManageCandidate($candidate);

        $currentListLeaderCandidate = $this->currentListLeaderCandidate();

        $user = $candidate->user;
        $userData = $userRequest->getSanitized();

        if ($currentListLeaderCandidate) {
            $userData['election_id'] = $currentListLeaderCandidate->election_id;
        }

        $user->update($userData);

        $candidateData = $request->getSanitized();

        $nextCandidateType = (string) ($candidateData['candidate_type'] ?? $candidate->candidate_type);
        if ($nextCandidateType === 'list_leader') {
            $listMembersActualCount = Candidate::withoutGlobalScopes()
                ->where('list_leader_candidate_id', (int) $candidate->id)
                ->where(function (Builder $query) {
                    $query->where('is_actual_list_candidate', true)
                        ->orWhereNull('is_actual_list_candidate');
                })
                ->count();

            $leaderWillBeActual = array_key_exists('is_actual_list_candidate', $candidateData)
                ? (bool) $candidateData['is_actual_list_candidate']
                : $this->isConsideredActual($candidate->is_actual_list_candidate);

            $requiredActualCount = $listMembersActualCount + ($leaderWillBeActual ? 1 : 0);
            $requestedListCount = (int) ($candidateData['list_candidates_count'] ?? $candidate->list_candidates_count ?? 0);

            if ($requestedListCount > 0 && $requestedListCount < $requiredActualCount) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'list_candidates_count' => 'لا يمكن تقليل عدد مرشحي القائمة عن العدد الحالي للمرشحين الفعليين.',
                    ]);
            }
        }

        if ($currentListLeaderCandidate) {
            $isLeaderSelf = (int) $candidate->id === (int) $currentListLeaderCandidate->id;

            if ($isLeaderSelf) {
                $candidateData['candidate_type'] = 'list_leader';
                $candidateData['list_leader_candidate_id'] = null;
                $user->syncRoles(['مرشح', 'مرشح رئيس قائمة']);
            } else {
                $candidateData['candidate_type'] = 'candidate';
                $candidateData['list_leader_candidate_id'] = $currentListLeaderCandidate->id;
                $candidateData['list_name'] = (string) ($currentListLeaderCandidate->list_name ?? '');
                $candidateData['list_logo'] = (string) ($currentListLeaderCandidate->list_logo ?? '');
                $candidateData['list_candidates_count'] = null;
                $user->syncRoles(['مرشح']);
            }

            $candidateData['election_id'] = $currentListLeaderCandidate->election_id;
        } else {
            $candidateType = (string) ($candidateData['candidate_type'] ?? 'candidate');
            if ($candidateType !== 'list_leader') {
                $candidateData['list_candidates_count'] = null;
                $candidateData['list_name'] = null;
                $candidateData['list_logo'] = null;
                $candidateData['list_leader_candidate_id'] = null;
            }

            $assignedRoles = ['مرشح'];
            if ($candidateType === 'list_leader') {
                Role::findOrCreate('مرشح رئيس قائمة', 'web');
                $assignedRoles[] = 'مرشح رئيس قائمة';
            }

            $user->syncRoles($assignedRoles);
        }

        $candidate->update($candidateData);

        if ($candidate->isListLeader()) {
            Candidate::withoutGlobalScopes()
                ->where('list_leader_candidate_id', (int) $candidate->id)
                ->update([
                    'list_name' => (string) ($candidate->list_name ?? ''),
                    'list_logo' => (string) ($candidate->list_logo ?? ''),
                ]);
        }

        session()->flash('message', 'Candidate Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Candidate $candidate)
    {
        $this->ensureListLeaderCanManageCandidate($candidate);

        $currentListLeaderCandidate = $this->currentListLeaderCandidate();
        if ($currentListLeaderCandidate && (int) $candidate->id === (int) $currentListLeaderCandidate->id) {
            abort(403);
        }

        $candidate->user()->delete();
        $candidate->delete();
        return response()->json([
            'message' => 'Candidate Deleted Successfully!'
        ]);
    }

    public function toggleStatus(Candidate $candidate)
    {
        if (!$this->candidateStopColumnsAvailable()) {
            $message = 'ميزة الإيقاف غير مفعلة بعد. يرجى تشغيل تحديثات قاعدة البيانات.';

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            session()->flash('message', $message);
            session()->flash('type', 'warning');

            return back();
        }

        $currentListLeaderCandidate = $this->currentListLeaderCandidate();
        abort_if(!$currentListLeaderCandidate, 403);

        $canToggleMember = (int) ($candidate->list_leader_candidate_id ?? 0) === (int) $currentListLeaderCandidate->id
            && (string) $candidate->candidate_type !== 'list_leader'
            && (int) $candidate->id !== (int) $currentListLeaderCandidate->id;

        abort_if(!$canToggleMember, 403);

        $isStopped = (bool) ($candidate->is_stopped ?? false);

        if ($isStopped) {
            $candidate->update([
                'is_stopped' => false,
                'stopped_by_candidate_id' => null,
                'stopped_at' => null,
            ]);

            $message = 'تم تفعيل المرشح بنجاح.';

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_stopped' => false,
                    'candidate_id' => (int) $candidate->id,
                ]);
            }

            session()->flash('message', $message);
            session()->flash('type', 'success');

            return back();
        }

        $candidate->update([
            'is_stopped' => true,
            'stopped_by_candidate_id' => (int) $currentListLeaderCandidate->id,
            'stopped_at' => now(),
        ]);

        $message = 'تم إيقاف المرشح بنجاح.';

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_stopped' => true,
                'candidate_id' => (int) $candidate->id,
            ]);
        }

        session()->flash('message', $message);
        session()->flash('type', 'warning');

        return back();
    }
    //================================================================================================
    //old
    // public function changeVotes($id, Request $request, VoteService $voteService)
    // {
    //     $increment = $request->input('increment');
    //     $result = $voteService->updateVotes($request->committee, $id, $increment, true);

    //     return redirect()->back()->with($result['status'] === 200 ? 'success' : 'error', $result['success'] ?? $result['error']);
    // }
    //================================================================================================
    public function setVotes($id, Request $request, VoteService $voteService)
    {
        $newVotes = $request->input('votes');
        $result = $voteService->updateVotes($request->committee, $id, $newVotes);

        if ($result['status'] !== 200) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        return response()->json(['success' => $result['success']]);
    }
    //================================================================================================
    public function sorting(Request $request)
    {
        $currentUser = auth()->user();
        $hasRepresentativeCandidatePivot = Schema::hasTable('candidate_representative');

        $representativeQuery = $currentUser->representatives()->with('committee');
        if ($hasRepresentativeCandidatePivot) {
            $representativeQuery->with('candidates:id,user_id,election_id');
        }

        $representative = $representativeQuery->first();
        if ($representative && $representative->committee_id) {
            $request->merge(['committee' => $representative->committee_id]);
        }

        $selectedCandidateIds = $hasRepresentativeCandidatePivot && $representative
            ? $representative->candidates->pluck('id')->map(fn ($id) => (int) $id)->unique()->values()->all()
            : [];

        $allowedCandidateUserIds = $this->resolveSortingAllowedCandidateUserIds($currentUser);

        $committees = $representative && $representative->committee_id
            ? Committee::where('id', $representative->committee_id)->get()
            : Committee::all();

        $committee = null;
        if (collect($request->all())->isEmpty()) {
            $candidates = null;
        } else {
            $committee = Committee::where('id', $request->committee)->with('candidates.user')->first();

            if (!$committee) {
                $candidates = null;
            } else {
                $candidates = $committee->candidates()
                    ->withoutGlobalScopes()
                    ->join('users', 'candidates.user_id', '=', 'users.id')
                    ->where('candidates.election_id', $currentUser->election_id)
                    ->when(is_array($allowedCandidateUserIds), function ($query) use ($selectedCandidateIds, $allowedCandidateUserIds) {
                        $query->where(function (Builder $scopedQuery) use ($selectedCandidateIds, $allowedCandidateUserIds) {
                            $hasSelectedCandidateIds = !empty($selectedCandidateIds);
                            $hasAllowedCreatorCandidateUsers = !empty($allowedCandidateUserIds);

                            if ($hasSelectedCandidateIds) {
                                $scopedQuery->whereIn('candidates.id', $selectedCandidateIds);
                            }

                            if ($hasAllowedCreatorCandidateUsers) {
                                if ($hasSelectedCandidateIds) {
                                    $scopedQuery->orWhereIn('candidates.user_id', $allowedCandidateUserIds);
                                } else {
                                    $scopedQuery->whereIn('candidates.user_id', $allowedCandidateUserIds);
                                }
                            }

                            if (!$hasSelectedCandidateIds && !$hasAllowedCreatorCandidateUsers) {
                                $scopedQuery->whereRaw('1 = 0');
                            }
                        });
                    })
                    ->orderBy('users.name')
                    ->select('candidates.*', 'users.name as user_name')
                    ->get()
                    ->map(function ($candidate) {
                        return [
                            'id' => $candidate->id,
                            'name' => $candidate->user_name,
                            'user_id' => $candidate->user_id,
                            'votes' => $candidate->pivot->votes,
                            'committee' => $candidate->pivot->committee_id,
                        ];
                    });
            }
        }
        return view('dashboard.sorting.index', compact('committees', 'candidates', 'committee'));
    }
    //================================================================================================
    public function allResult()
    {
        $show_all_result = false;
        $check_Setting = Setting::where('option_key', 'result_control')->first();
        if ($check_Setting && $check_Setting->option_value != NULL) {
            if ($check_Setting->option_value[0] == 'on') {
                $show_all_result    = true;
                $candidate_name     = 'مرشح الفرز العام';

                // Prefer the authenticated user's election for campaign-isolated results.
                $currentUser = auth('web')->user();
                $election_id = (int) ($currentUser?->election_id ?? 0);

                if ($election_id <= 0) {
                    $candidate_for_result = Setting::where('option_key', 'result_control_candidate')->first();
                    if ($candidate_for_result && $candidate_for_result->option_value != NULL) {
                        $election_id = (int) $this->fetchElectionFromCandidateId($candidate_for_result->option_value[0]);
                    } else {
                        $election_id = (int) $this->fetchElectionFromCandidate($candidate_name);
                    }
                }

                if ($election_id <= 0) {
                    abort(404);
                }

                $candidates     = $this->fetchCondidatesBasedOnElection($election_id, $candidate_name);

                $committeesQuery = Committee::query();
                if (Schema::hasColumn('committees', 'election_id')) {
                    $committeesQuery->where('election_id', $election_id);
                }

                $schoolsQuery = School::query()->orderBy('id', 'desc');
                if (Schema::hasColumn('schools', 'election_id')) {
                    $schoolsQuery->where('election_id', $election_id);
                }

                $committees = $committeesQuery->get();
                $schools = $schoolsQuery->get();

                return view('dashboard.resualt.all_index', compact('candidates', 'committees', 'schools'));
            }
        }
        abort(404);
    }
    //================================================================================================
    public function fetchElectionFromCandidate($candidate_name)
    {
        $election_id = null;
        $candidate = DB::table('candidates')
            ->join('users', 'candidates.user_id', '=', 'users.id')
            ->where('users.name', $candidate_name)
            ->select('candidates.*')
            ->first();

        if (isset($candidate)) {
            $election_id = $candidate->election_id;
        }
        return $election_id;
    }
    //================================================================================================
    public function fetchElectionFromCandidateId($id)
    {
        $election_id = null;
        $candidate =  DB::table('candidates')->where('user_id', $id)->first();
        if (isset($candidate)) {
            $election_id = $candidate->election_id;
        }
        return $election_id;
    }
    //==============================================================
    public function fetchCondidatesBasedOnElection($election_id, $candidate_name = '')
    {
        $candidates = Candidate::withoutGlobalScopes()
            ->join('users', 'candidates.user_id', '=', 'users.id')
            ->where('candidates.election_id', $election_id) // Explicit qualification
            // ->where('users.name','!=', $candidate_name) // Explicit qualification
            ->orderBy('votes', 'desc') // Order by votes in descending order
            ->orderBy('users.name') // Order by users' names
            ->select('candidates.*', 'users.name as user_name') // Select the necessary fields
            ->get();
        return $candidates;
    }
    //==============================================================
    public function changeVotes(Request $request, VoteService $voteService)
    {
        try {
            Log::info(json_encode($request->all()));

            DB::beginTransaction();
            // Access the data sent from Ajax
            $vote_count    = $request->json('vote_count');
            $count_status  = $request->json('count_status');
            $candidate_id  = $request->json('candidate_id');
            $committee     = $request->json('committee');

            $currentUser = auth()->user();
            $selectedCandidateIds = $this->resolveSortingRepresentativeSelectedCandidateIds($currentUser);
            $allowedCandidateUserIds = $this->resolveSortingAllowedCandidateUserIds($currentUser);

            if (is_array($allowedCandidateUserIds)) {
                $targetCandidateUserId = (int) Candidate::withoutGlobalScopes()
                    ->where('id', (int) $candidate_id)
                    ->value('user_id');

                $isAllowedByRepresentativeAssignments = !empty($selectedCandidateIds)
                    && in_array((int) $candidate_id, $selectedCandidateIds, true);

                $isAllowedByCreatorCandidate = !empty($allowedCandidateUserIds)
                    && $targetCandidateUserId > 0
                    && in_array($targetCandidateUserId, $allowedCandidateUserIds, true);

                if (!$isAllowedByRepresentativeAssignments && !$isAllowedByCreatorCandidate) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'لا يمكنك تعديل أصوات هذا المرشح لأنه غير مخصص لك.',
                    ], 403);
                }
            }

            $result = $voteService->updateVotes2($committee, $candidate_id, $count_status, $vote_count);
            //==========================================
            if (isset($result['error'])) {
                DB::rollBack();
                return response()->json([
                    'success'   => false,
                    'message'   => $result['error'],
                    'data'      => $request->all(),
                ], 500);
            } else {
                DB::commit();
                return response()->json([
                    'success'   => true,
                    'message'   => $result['success'],
                    'data'      => $request->all(),
                    'vote_count' => $result['vote_count'],
                ]);
            }
            //==========================================
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطا اثناء التصويت',
                'data'    => $e->getMessage(),
            ], 500);
        }
    }
    //==============================================================
    public function storeFakeCandidate(Request $request, UserRequest $userRequest)
    {
        if ($this->currentListLeaderCandidate()) {
            abort(403);
        }

        try {
            DB::beginTransaction();
            $image = $request->file('image');
            // $path = $image->store('', 'media');
            // $user = User::create($userRequest->getSanitized()+['image'=>public_path($path)]);
            $user = User::create([
                'name'          => $request->name,
                'image'         => $this->uploadImage($image,'media'),
                'creator_id'    => auth()->user()->id,
                'election_id'   => $request->election_id,
                'password'      => \Hash::make("1"),
            ]);
            $user->assignRole('مرشح');
            $request['user_id'] = $user->id;

            Candidate::create($request->all() + ['max_contractor' => 0, 'max_represent' => 0]);

            DB::commit();
            // Your status update logic here
            return response()->json([
                'success' => true,
                'message' => 'تم اضافة المرشح بنجاح',
                'data'    => $request->all(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطا اثناء الاضافه',
                'data'    => $e->getMessage(),

            ], 500);
        }
    }
    //==============================================================

    private function currentListLeaderCandidate(): ?Candidate
    {
        if (!auth()->check()) {
            return null;
        }

        $candidate = Candidate::withoutGlobalScopes()
            ->where('user_id', auth()->id())
            ->where('candidate_type', 'list_leader')
            ->first();

        if ($candidate && auth()->user()) {
            $currentElectionId = (int) (auth()->user()->election_id ?? 0);
            $leaderElectionId = (int) ($candidate->election_id ?? 0);

            if ($leaderElectionId > 0 && $currentElectionId !== $leaderElectionId) {
                auth()->user()->forceFill(['election_id' => $leaderElectionId])->save();
                auth()->user()->setAttribute('election_id', $leaderElectionId);
            }
        }

        return $candidate;
    }

    private function applyListLeaderVisibilityScope(Builder $query, ?Candidate $listLeaderCandidate): Builder
    {
        if (!$listLeaderCandidate) {
            return $query;
        }

        return $query->where(function (Builder $nestedQuery) use ($listLeaderCandidate) {
            $nestedQuery
                ->where('candidates.id', $listLeaderCandidate->id)
                ->orWhere('candidates.list_leader_candidate_id', $listLeaderCandidate->id);
        });
    }

    private function ensureListLeaderCanManageCandidate(Candidate $candidate): void
    {
        $listLeaderCandidate = $this->currentListLeaderCandidate();

        if (!$listLeaderCandidate) {
            return;
        }

        $canAccess = (int) $candidate->id === (int) $listLeaderCandidate->id
            || (int) ($candidate->list_leader_candidate_id ?? 0) === (int) $listLeaderCandidate->id;

        abort_if(!$canAccess, 403);
    }

    private function effectiveListCandidatesCount(Candidate $listLeaderCandidate): int
    {
        $membersActualCount = Candidate::withoutGlobalScopes()
            ->where('list_leader_candidate_id', (int) $listLeaderCandidate->id)
            ->where(function (Builder $query) {
                $query->where('is_actual_list_candidate', true)
                    ->orWhereNull('is_actual_list_candidate');
            })
            ->count();

        $leaderActualCount = $this->isConsideredActual($listLeaderCandidate->is_actual_list_candidate) ? 1 : 0;

        return $membersActualCount + $leaderActualCount;
    }

    private function isConsideredActual($isActualFlag): bool
    {
        if ($isActualFlag === null) {
            return true;
        }

        return (bool) $isActualFlag;
    }

    private function candidateStopColumnsAvailable(): bool
    {
        static $checked = false;
        static $available = false;

        if ($checked) {
            return $available;
        }

        $checked = true;

        try {
            $available = Schema::hasColumns('candidates', [
                'is_stopped',
                'stopped_by_candidate_id',
                'stopped_at',
            ]);
        } catch (\Throwable $exception) {
            $available = false;
        }

        return $available;
    }

    private function resolveListManagementScope(Request $request): array
    {
        $currentListLeaderCandidate = $this->currentListLeaderCandidate();
        $canAccess = admin()->can('candidates.list') || $currentListLeaderCandidate;

        if (!$canAccess || !$currentListLeaderCandidate) {
            return [
                'can_access' => false,
                'leader_election_id' => 0,
                'allowed_candidate_user_ids' => [],
                'selected_candidate_user_ids' => [],
                'contractor_ids' => [],
            ];
        }

        $listManagementCandidates = Candidate::withoutGlobalScopes()
            ->where(function (Builder $query) use ($currentListLeaderCandidate) {
                $query
                    ->where('id', (int) $currentListLeaderCandidate->id)
                    ->orWhere('list_leader_candidate_id', (int) $currentListLeaderCandidate->id);
            })
            ->pluck('user_id')
            ->filter()
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values()
            ->all();

        $requestedCandidateUserIds = collect((array) $request->input('candidate_users', []))
            ->filter(fn ($value) => $value !== null && $value !== '' && (string) $value !== 'all')
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values();

        $selectedCandidateUserIds = $requestedCandidateUserIds
            ->filter(fn (int $value) => in_array($value, $listManagementCandidates, true))
            ->values()
            ->all();

        if (empty($selectedCandidateUserIds)) {
            $selectedCandidateUserIds = $listManagementCandidates;
        }

        $contractorIds = Contractor::withoutGlobalScopes()
            ->where('election_id', (int) $currentListLeaderCandidate->election_id)
            ->whereIn('creator_id', $selectedCandidateUserIds)
            ->pluck('id')
            ->map(fn ($value) => (int) $value)
            ->filter()
            ->values()
            ->all();

        return [
            'can_access' => true,
            'leader_election_id' => (int) $currentListLeaderCandidate->election_id,
            'allowed_candidate_user_ids' => $listManagementCandidates,
            'selected_candidate_user_ids' => $selectedCandidateUserIds,
            'contractor_ids' => $contractorIds,
        ];
    }

    private function resolveSortingAllowedCandidateUserIds(?User $user): ?array
    {
        if (!$user) {
            return null;
        }

        // Apply this restriction for representative accounts only.
        $isRepresentativeUser = $user->hasRole('مندوب') || $user->representatives()->exists();
        if (!$isRepresentativeUser) {
            return null;
        }

        $creatorId = (int) ($user->creator_id ?? 0);
        if ($creatorId <= 0) {
            return [];
        }

        $creatorCandidateQuery = Candidate::withoutGlobalScopes()
            ->where('user_id', $creatorId);

        $userElectionId = (int) ($user->election_id ?? 0);
        if ($userElectionId > 0) {
            $creatorCandidateQuery->where('election_id', $userElectionId);
        }

        return $creatorCandidateQuery
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function resolveSortingRepresentativeSelectedCandidateIds(?User $user): array
    {
        if (!$user) {
            return [];
        }

        if (!Schema::hasTable('candidate_representative')) {
            return [];
        }

        $representative = $user->representatives()->with('candidates:id')->first();
        if (!$representative) {
            return [];
        }

        return $representative->candidates
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

}
