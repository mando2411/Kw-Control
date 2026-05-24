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
use App\Models\Voter;
use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Services\VoteService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
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

            $rows = $rows
                ->groupBy(fn ($row) => (int) ($row->voter_id ?? 0))
                ->map(function ($group) {
                    $firstRow = $group->first();
                    $candidateNames = $group->pluck('candidate_name')
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();
                    $contractorNames = $group->pluck('contractor_name')
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();

                    $firstRow->candidate_names = $candidateNames;
                    $firstRow->contractor_names = $contractorNames;
                    $firstRow->is_duplicate = $group->count() > 1;
                    $firstRow->duplicate_count = $group->count();

                    return $firstRow;
                })
                ->values();
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
    public function candidateOrdering(Request $request)
    {
        abort_if(!auth()->check() || !auth()->user()->can('candidates.list'), 403);

        $selectedElectionId = (int) $request->input('election_id', 0);
        $hasSortingOrderColumn = Schema::hasColumn('candidates', 'sorting_order');
        $elections = Election::query()->orderByDesc('id')->get(['id', 'name']);
        $candidates = collect();

        if ($selectedElectionId > 0) {
            $candidatesQuery = Candidate::withoutGlobalScopes()
                ->join('users', 'candidates.user_id', '=', 'users.id')
                ->where('candidates.election_id', $selectedElectionId);

            if ($hasSortingOrderColumn) {
                $candidatesQuery
                    ->orderByRaw('CASE WHEN candidates.sorting_order IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('candidates.sorting_order');
            }
            $candidatesQuery->orderBy('users.name');

            $selectColumns = [
                'candidates.id',
                'candidates.candidate_type',
                'candidates.list_leader_candidate_id',
                'candidates.list_name',
                'users.name as user_name',
            ];
            if ($hasSortingOrderColumn) {
                $selectColumns[] = 'candidates.sorting_order';
            }

            $candidates = $candidatesQuery
                ->get($selectColumns)
                ->map(function ($candidate) {
                    $candidateType = (string) ($candidate->candidate_type ?? 'candidate');
                    $typeLabel = 'مستقل';
                    if ($candidateType === 'list_leader') {
                        $typeLabel = 'رئيس قائمة';
                    } elseif ((int) ($candidate->list_leader_candidate_id ?? 0) > 0) {
                        $typeLabel = 'عضو قائمة';
                    }

                    $displayName = (string) ($candidate->user_name ?? '');
                    if ($candidateType === 'list_leader') {
                        $listName = trim((string) ($candidate->list_name ?? ''));
                        if ($listName !== '') {
                            $displayName = 'القائمة: ' . $listName;
                        }
                    }

                    return [
                        'id' => (int) $candidate->id,
                        'name' => $displayName,
                        'type_label' => $typeLabel,
                        'sorting_order' => $candidate->sorting_order !== null ? (int) $candidate->sorting_order : null,
                    ];
                })
                ->values();
        }

        return view('dashboard.candidates.ordering', compact('elections', 'selectedElectionId', 'candidates'));
    }
    //================================================================================================
    public function candidateOrderingUpdate(Request $request)
    {
        abort_if(!auth()->check() || !auth()->user()->can('candidates.edit'), 403);
        abort_unless(Schema::hasColumn('candidates', 'sorting_order'), 422, 'حقل ترتيب المرشحين غير متوفر. يرجى تنفيذ التحديثات.');

        $validated = $request->validate([
            'election_id' => ['required', 'integer', 'exists:elections,id'],
            'orders' => ['nullable', 'array'],
            'orders.*' => ['nullable', 'integer', 'min:1'],
        ]);

        $electionId = (int) $validated['election_id'];
        $orders = (array) ($validated['orders'] ?? []);

        $candidateIds = Candidate::withoutGlobalScopes()
            ->where('election_id', $electionId)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        DB::transaction(function () use ($candidateIds, $orders, $electionId) {
            foreach ($candidateIds as $candidateId) {
                $orderValue = $orders[$candidateId] ?? null;
                $orderValue = ($orderValue === '' || $orderValue === null) ? null : (int) $orderValue;

                Candidate::withoutGlobalScopes()
                    ->where('id', $candidateId)
                    ->where('election_id', $electionId)
                    ->update(['sorting_order' => $orderValue]);
            }
        });

        return redirect()
            ->route('dashboard.candidates.ordering', ['election_id' => $electionId])
            ->with('success', 'تم حفظ ترتيب المرشحين بنجاح.');
    }
    //================================================================================================
    public function sorting(Request $request)
    {
        $currentUser = auth()->user();
        $hasSortingOrderColumn = Schema::hasColumn('candidates', 'sorting_order');
        $sortingStatus = $this->resolveCurrentUserSortingStatus($currentUser);
        $hasRepresentativeCandidatePivot = Schema::hasTable('candidate_representative');
        $isScopedSortingUser = $this->shouldScopeSortingCandidates($currentUser);

        $representatives = $currentUser->representatives()
            ->with('committee')
            ->when($hasRepresentativeCandidatePivot, function ($query) {
                $query->with('candidates:id,user_id,election_id');
            })
            ->get();

        $requestedCommitteeId = (int) $request->input('committee', 0);

        // Representatives do not choose committee manually, so infer a usable one.
        if ($isScopedSortingUser && $requestedCommitteeId <= 0) {
            $requestedCommitteeId = (int) ($representatives->pluck('committee_id')->filter()->first() ?? 0);
            if ($requestedCommitteeId <= 0) {
                $requestedCommitteeId = $this->resolveSortingDefaultCommitteeId($currentUser);
            }
            if ($requestedCommitteeId > 0) {
                $request->merge(['committee' => $requestedCommitteeId]);
            }
        }

        $sortingScopeElectionId = $this->resolveSortingScopeElectionId(
            $currentUser,
            $requestedCommitteeId > 0 ? $requestedCommitteeId : null
        );

        $selectedCandidateIds = $this->resolveSortingRepresentativeSelectedCandidateIds(
            $currentUser,
            $requestedCommitteeId > 0 ? $requestedCommitteeId : null,
            $sortingScopeElectionId
        );

        $allowedCandidateUserIds = $this->resolveSortingAllowedCandidateUserIds($currentUser, $sortingScopeElectionId);
        $currentUserCandidate = Candidate::withoutGlobalScopes()
            ->select('id', 'candidate_type', 'list_leader_candidate_id')
            ->where('user_id', (int) $currentUser->id)
            ->first();
        if (!$currentUserCandidate && (int) ($currentUser->creator_id ?? 0) > 0) {
            $currentUserCandidate = Candidate::withoutGlobalScopes()
                ->select('id', 'candidate_type', 'list_leader_candidate_id')
                ->where('user_id', (int) $currentUser->creator_id)
                ->first();
        }
        $myListLeaderCandidateId = 0;
        if ($currentUserCandidate) {
            $myListLeaderCandidateId = (string) ($currentUserCandidate->candidate_type ?? '') === 'list_leader'
                ? (int) $currentUserCandidate->id
                : (int) ($currentUserCandidate->list_leader_candidate_id ?? 0);
        }
        $myListLeaderCandidateIds = $myListLeaderCandidateId > 0 ? [$myListLeaderCandidateId] : [];

        if ($isScopedSortingUser && $representatives->isNotEmpty()) {
            $committees = Committee::whereIn('id', $representatives->pluck('committee_id')->filter()->unique()->values()->all())->get();
        } elseif ($isScopedSortingUser) {
            $fallbackElectionId = (int) ($currentUser->election_id ?? 0);
            if ($fallbackElectionId <= 0) {
                $fallbackElectionId = (int) Candidate::withoutGlobalScopes()
                    ->where('user_id', (int) ($currentUser->creator_id ?? 0))
                    ->value('election_id');
            }

            $committees = Committee::query()
                ->when($fallbackElectionId > 0 && Schema::hasColumn('committees', 'election_id'), function ($query) use ($fallbackElectionId) {
                    $query->where('election_id', $fallbackElectionId);
                })
                ->get();
        } else {
            $committees = Committee::all();
        }

        $committee = null;
        if (collect($request->all())->isEmpty()) {
            $candidates = null;
        } else {
            $committee = Committee::where('id', $request->committee)->with('candidates.user')->first();

            if (!$committee) {
                $candidates = null;
            } else {
                $effectiveElectionId = $sortingScopeElectionId;
                if ($effectiveElectionId <= 0 && Schema::hasColumn('committees', 'election_id')) {
                    $effectiveElectionId = (int) ($committee->election_id ?? 0);
                }
                $includeElectionWideForRepresentative = $isScopedSortingUser
                    && ($currentUser->hasRole('مندوب') || $currentUser->representatives()->exists())
                    && $effectiveElectionId > 0
                    && $this->campaignUsesListSystem($effectiveElectionId)
                    && Schema::hasColumn('candidates', 'candidate_type')
                    && Schema::hasColumn('candidates', 'list_leader_candidate_id');

                // Heal missing committee-candidate mapping for representative-visible candidates.
                $this->ensureSortingCommitteeCandidateMapping(
                    $committee,
                    $selectedCandidateIds,
                    $allowedCandidateUserIds,
                    $effectiveElectionId
                );

                $candidatesQuery = $committee->candidates()
                    ->withoutGlobalScopes()
                    ->join('users', 'candidates.user_id', '=', 'users.id')
                    ->when($effectiveElectionId > 0, function ($query) use ($effectiveElectionId) {
                        $query->where('candidates.election_id', $effectiveElectionId);
                    })
                    ->when(is_array($allowedCandidateUserIds), function ($query) use ($selectedCandidateIds, $allowedCandidateUserIds, $includeElectionWideForRepresentative) {
                        $query->where(function (Builder $scopedQuery) use ($selectedCandidateIds, $allowedCandidateUserIds, $includeElectionWideForRepresentative) {
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

                            if ($includeElectionWideForRepresentative) {
                                $scopedQuery->orWhereRaw('1 = 1');
                            }
                        });
                    })
                    ->orderByRaw("CASE WHEN candidates.candidate_type = 'list_leader' THEN 1 ELSE 0 END")
                    ->orderBy('users.name')
                    ->select('candidates.*', 'users.name as user_name');

                if ($hasSortingOrderColumn) {
                    $candidatesQuery
                        ->orderByRaw("CASE WHEN candidates.sorting_order IS NULL THEN 1 ELSE 0 END")
                        ->orderBy('candidates.sorting_order');
                }

                $this->applySortingCandidateVisibilityScope($candidatesQuery, $effectiveElectionId);

                $candidates = $candidatesQuery
                    ->get()
                    ->map(function ($candidate) use ($myListLeaderCandidateIds) {
                        $displayName = (string) ($candidate->user_name ?? '');
                        $candidateType = (string) ($candidate->candidate_type ?? '');
                        if ($candidateType === 'list_leader') {
                            $listName = trim((string) ($candidate->list_name ?? ''));
                            $displayName = $listName !== ''
                                ? 'القائمة: ' . $listName
                                : 'القائمة';
                        }

                        $listLeaderCandidateId = (int) ($candidate->list_leader_candidate_id ?? 0);
                        $candidateId = (int) $candidate->id;
                        $isListLeader = $candidateType === 'list_leader';
                        $isListMember = $listLeaderCandidateId > 0;
                        $listGroupId = $isListLeader ? $candidateId : $listLeaderCandidateId;
                        $belongsToMyList = in_array($candidateId, $myListLeaderCandidateIds, true)
                            || ($listLeaderCandidateId > 0 && in_array($listLeaderCandidateId, $myListLeaderCandidateIds, true));
                        $candidateGroup = 'independent';
                        if ($belongsToMyList) {
                            $candidateGroup = 'my_list';
                        } elseif ($isListLeader || $isListMember) {
                            $candidateGroup = 'other_lists';
                        }

                        return [
                            'id' => $candidate->id,
                            'name' => $displayName,
                            'user_id' => $candidate->user_id,
                            'votes' => $candidate->pivot->votes,
                            'committee' => $candidate->pivot->committee_id,
                            'is_list' => $isListLeader,
                            'candidate_group' => $candidateGroup,
                            'list_group_id' => $listGroupId,
                            'sorting_order' => $candidate->sorting_order !== null ? (int) $candidate->sorting_order : null,
                        ];
                    });
            }
        }
        return view('dashboard.sorting.index', compact('committees', 'candidates', 'committee', 'sortingStatus'));
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
                $committeeIds = $committees->pluck('id')->map(fn ($id) => (int) $id)->values()->all();

                $listLeaderVoteTotals = Candidate::withoutGlobalScopes()
                    ->where('election_id', $election_id)
                    ->where('candidate_type', 'list_leader')
                    ->with(['committees' => function ($query) use ($committeeIds) {
                        if (!empty($committeeIds)) {
                            $query->whereIn('committees.id', $committeeIds);
                        }
                        $query->select('committees.id');
                    }])
                    ->get(['id'])
                    ->mapWithKeys(function ($candidate) {
                        return [(int) $candidate->id => (int) $candidate->committees->sum('pivot.votes')];
                    })
                    ->all();

                $listLeaderResults = Candidate::withoutGlobalScopes()
                    ->join('users', 'candidates.user_id', '=', 'users.id')
                    ->where('candidates.election_id', $election_id)
                    ->where('candidates.candidate_type', 'list_leader')
                    ->orderBy('users.name')
                    ->get(['candidates.id', 'users.name as user_name'])
                    ->map(function ($candidate) use ($listLeaderVoteTotals) {
                        $candidateId = (int) ($candidate->id ?? 0);
                        return [
                            'id' => $candidateId,
                            'name' => (string) ($candidate->user_name ?? ''),
                            'votes' => (int) ($listLeaderVoteTotals[$candidateId] ?? 0),
                        ];
                    })
                    ->sortByDesc('votes')
                    ->values();

                return view('dashboard.resualt.all_index', compact('candidates', 'committees', 'schools', 'election_id', 'listLeaderVoteTotals', 'listLeaderResults'));
            }
        }
        abort(404);
    }
    //================================================================================================
    public function allResultLiveStats(Request $request)
    {
        $validated = $request->validate([
            'election_id' => ['required', 'integer', 'exists:elections,id'],
        ]);

        $electionId = (int) $validated['election_id'];

        $committees = Committee::query()
            ->when(Schema::hasColumn('committees', 'election_id'), function ($query) use ($electionId) {
                $query->where('election_id', $electionId);
            })
            ->get(['id', 'type']);

        $committeeIds = $committees->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        $committeeTypeMap = $committees->mapWithKeys(fn ($committee) => [(int) $committee->id => (string) $committee->type]);

        $candidatesQuery = Candidate::withoutGlobalScopes()
            ->where('election_id', $electionId)
            ->with(['committees' => function ($query) use ($committeeIds) {
                if (!empty($committeeIds)) {
                    $query->whereIn('committees.id', $committeeIds);
                }
                $query->select('committees.id', 'committees.type');
            }])
            ->select(['id', 'votes', 'election_id', 'candidate_type', 'list_leader_candidate_id']);

        $this->applyActualCandidateOnlyScope($candidatesQuery);

        $candidates = $candidatesQuery
            ->get()
            ->sortByDesc('votes')
            ->values();

        $committeeTotals = [];
        foreach ($committeeIds as $committeeId) {
            $committeeTotals[$committeeId] = 0;
        }

        $candidateRows = [];
        foreach ($candidates as $candidate) {
            $candidateCommitteeVotes = [];
            $menTotal = 0;
            $womenTotal = 0;
            $candidateType = (string) ($candidate->candidate_type ?? '');
            $listGroupId = $candidateType === 'list_leader'
                ? (int) $candidate->id
                : (int) ($candidate->list_leader_candidate_id ?? 0);

            foreach ($candidate->committees as $committee) {
                $committeeId = (int) $committee->id;
                $votes = (int) ($committee->pivot->votes ?? 0);

                $candidateCommitteeVotes[$committeeId] = $votes;
                $committeeTotals[$committeeId] = ($committeeTotals[$committeeId] ?? 0) + $votes;

                if (($committeeTypeMap[$committeeId] ?? '') === \App\Enums\Type::MEN->value) {
                    $menTotal += $votes;
                } else {
                    $womenTotal += $votes;
                }
            }

            $candidateVotesTotal = (int) ($menTotal + $womenTotal);
            $candidateRows[] = [
                'id' => (int) $candidate->id,
                'votes' => $candidateVotesTotal,
                'men_total' => $menTotal,
                'women_total' => $womenTotal,
                'committee_votes' => $candidateCommitteeVotes,
                'list_group_id' => $listGroupId,
            ];
        }

        $listLeaderVoteTotals = Candidate::withoutGlobalScopes()
            ->where('election_id', $electionId)
            ->where('candidate_type', 'list_leader')
            ->with(['committees' => function ($query) use ($committeeIds) {
                if (!empty($committeeIds)) {
                    $query->whereIn('committees.id', $committeeIds);
                }
                $query->select('committees.id');
            }])
            ->get(['id'])
            ->mapWithKeys(function ($candidate) {
                return [(int) $candidate->id => (int) $candidate->committees->sum('pivot.votes')];
            })
            ->all();

        $candidateRows = collect($candidateRows)->map(function (array $candidateRow) use ($listLeaderVoteTotals) {
            $listGroupId = (int) ($candidateRow['list_group_id'] ?? 0);
            $candidateRow['list_total_votes'] = (int) ($listGroupId > 0 ? ($listLeaderVoteTotals[$listGroupId] ?? 0) : 0);
            $candidateRow['total_with_list_votes'] = (int) (($candidateRow['votes'] ?? 0) + ($candidateRow['list_total_votes'] ?? 0));
            unset($candidateRow['list_group_id']);

            return $candidateRow;
        })->values()->all();

        $menTotalAll = collect($committeeTotals)
            ->filter(fn ($votes, $committeeId) => ($committeeTypeMap[(int) $committeeId] ?? '') === \App\Enums\Type::MEN->value)
            ->sum();

        $womenTotalAll = collect($committeeTotals)
            ->filter(fn ($votes, $committeeId) => ($committeeTypeMap[(int) $committeeId] ?? '') === \App\Enums\Type::WOMEN->value)
            ->sum();

        return response()->json([
            'success' => true,
            'election_id' => $electionId,
            'candidates' => $candidateRows,
            'list_leader_votes' => $listLeaderVoteTotals,
            'committee_totals' => $committeeTotals,
            'men_total_all' => (int) $menTotalAll,
            'women_total_all' => (int) $womenTotalAll,
            'grand_total_all' => (int) collect($committeeTotals)->sum(),
        ]);
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
        $candidatesQuery = Candidate::withoutGlobalScopes()
            ->join('users', 'candidates.user_id', '=', 'users.id')
            ->where('candidates.election_id', $election_id) // Explicit qualification
            // ->where('users.name','!=', $candidate_name) // Explicit qualification
            ->orderBy('votes', 'desc') // Order by votes in descending order
            ->orderBy('users.name') // Order by users' names
            ->select('candidates.*', 'users.name as user_name'); // Select the necessary fields

        $this->applyActualCandidateOnlyScope($candidatesQuery);

        $candidates = $candidatesQuery->get();
        return $candidates;
    }
    //==============================================================
    public function changeVotes(Request $request, VoteService $voteService)
    {
        try {
            // Access the data sent from Ajax
            $vote_count    = $request->json('vote_count');
            $count_status  = $request->json('count_status');
            $candidate_id  = $request->json('candidate_id');
            $committee     = $request->json('committee');

            $currentUser = auth()->user();
            if ($this->resolveCurrentUserSortingStatus($currentUser) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'الفرز متوقف لهذا المستخدم حاليا.',
                ], 403);
            }

            $sortingScopeElectionId = $this->resolveSortingScopeElectionId(
                $currentUser,
                (int) $committee > 0 ? (int) $committee : null
            );

            $selectedCandidateIds = $this->resolveSortingRepresentativeSelectedCandidateIds(
                $currentUser,
                (int) $committee > 0 ? (int) $committee : null,
                $sortingScopeElectionId
            );
            $allowedCandidateUserIds = $this->resolveSortingAllowedCandidateUserIds($currentUser, $sortingScopeElectionId);
            $includeElectionWideForRepresentative = $this->shouldScopeSortingCandidates($currentUser)
                && ($currentUser->hasRole('مندوب') || $currentUser->representatives()->exists())
                && $sortingScopeElectionId > 0
                && $this->campaignUsesListSystem($sortingScopeElectionId)
                && Schema::hasColumn('candidates', 'candidate_type')
                && Schema::hasColumn('candidates', 'list_leader_candidate_id');

            if (is_array($allowedCandidateUserIds)) {
                $targetCandidate = Candidate::withoutGlobalScopes()
                    ->select('id', 'user_id', 'election_id', 'candidate_type', 'list_leader_candidate_id')
                    ->where('id', (int) $candidate_id)
                    ->first();
                $targetCandidateUserId = (int) ($targetCandidate->user_id ?? 0);

                $isAllowedByRepresentativeAssignments = !empty($selectedCandidateIds)
                    && in_array((int) $candidate_id, $selectedCandidateIds, true);

                $isAllowedByCreatorCandidate = !empty($allowedCandidateUserIds)
                    && $targetCandidateUserId > 0
                    && in_array($targetCandidateUserId, $allowedCandidateUserIds, true);

                $isAllowedByElectionScope = false;
                if ($includeElectionWideForRepresentative && $targetCandidate) {
                    $targetElectionId = (int) ($targetCandidate->election_id ?? 0);
                    if ($targetElectionId > 0 && $targetElectionId === (int) $sortingScopeElectionId) {
                        $targetCandidateScopeQuery = Candidate::withoutGlobalScopes()
                            ->where('id', (int) $candidate_id)
                            ->where('election_id', (int) $sortingScopeElectionId);
                        $this->applySortingCandidateVisibilityScope($targetCandidateScopeQuery, (int) $sortingScopeElectionId);
                        $isAllowedByElectionScope = $targetCandidateScopeQuery->exists();
                    }
                }

                if (!$isAllowedByRepresentativeAssignments && !$isAllowedByCreatorCandidate && !$isAllowedByElectionScope) {
                    return response()->json([
                        'success' => false,
                        'message' => 'لا يمكنك تعديل أصوات هذا المرشح لأنه غير مخصص لك.',
                    ], 403);
                }
            }

            $result = $voteService->updateVotes2($committee, $candidate_id, $count_status, $vote_count);
            //==========================================
            if (isset($result['error'])) {
                return response()->json([
                    'success'   => false,
                    'message'   => $result['error'],
                    'data'      => $request->all(),
                ], 500);
            } else {
                return response()->json([
                    'success'   => true,
                    'message'   => $result['success'],
                    'data'      => $request->all(),
                    'vote_count' => $result['vote_count'],
                ]);
            }
            //==========================================
        } catch (\Throwable $e) {
            Log::error('changeVotes failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->take(8)->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطا اثناء التصويت',
                'data'    => $e->getMessage(),
            ], 500);
        }
    }

    public function sortingStatus(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'error' => 'يجب تسجيل الدخول أولا.'
            ], 403);
        }

        $currentStatus = $this->resolveCurrentUserSortingStatus($user);
        $newStatus = $currentStatus === 1 ? 0 : 1;

        try {
            Cache::forever($this->sortingStatusCacheKey((int) $user->id), $newStatus);
        } catch (\Throwable $exception) {
            Log::warning('Failed to persist sorting status in cache', [
                'user_id' => (int) $user->id,
                'error' => $exception->getMessage(),
            ]);

            session()->put($this->sortingStatusSessionKey((int) $user->id), $newStatus);
        }

        return response()->json([
            'status' => $newStatus,
            'message' => $newStatus === 1
                ? 'تم فتح الفرز لهذا المستخدم بنجاح.'
                : 'تم إيقاف الفرز لهذا المستخدم بنجاح.',
        ]);
    }

    public function sortingLiveStats(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'error' => 'يجب تسجيل الدخول أولا.'
            ], 403);
        }

        $validated = $request->validate([
            'committee' => ['required', 'integer', 'exists:committees,id'],
            'candidate_ids' => ['nullable', 'array'],
            'candidate_ids.*' => ['integer'],
        ]);

        $committeeId = (int) ($validated['committee'] ?? 0);
        $committee = Committee::where('id', $committeeId)->first();

        if (!$committee) {
            return response()->json([
                'error' => 'اللجنة غير موجودة.'
            ], 404);
        }

        $requestedCandidateIds = collect((array) ($validated['candidate_ids'] ?? []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $sortingScopeElectionId = $this->resolveSortingScopeElectionId($user, $committeeId);

        $selectedCandidateIds = $this->resolveSortingRepresentativeSelectedCandidateIds($user, $committeeId, $sortingScopeElectionId);
        $allowedCandidateUserIds = $this->resolveSortingAllowedCandidateUserIds($user, $sortingScopeElectionId);
        $includeElectionWideForRepresentative = $this->shouldScopeSortingCandidates($user)
            && ($user->hasRole('مندوب') || $user->representatives()->exists())
            && $sortingScopeElectionId > 0
            && $this->campaignUsesListSystem($sortingScopeElectionId)
            && Schema::hasColumn('candidates', 'candidate_type')
            && Schema::hasColumn('candidates', 'list_leader_candidate_id');

        $effectiveElectionId = $sortingScopeElectionId;
        if ($effectiveElectionId <= 0 && Schema::hasColumn('committees', 'election_id')) {
            $effectiveElectionId = (int) ($committee->election_id ?? 0);
        }

        $visibleCandidatesQuery = $committee->candidates()
            ->withoutGlobalScopes()
            ->join('users', 'candidates.user_id', '=', 'users.id')
            ->when($effectiveElectionId > 0, function ($query) use ($effectiveElectionId) {
                $query->where('candidates.election_id', $effectiveElectionId);
            })
            ->when(is_array($allowedCandidateUserIds), function ($query) use ($selectedCandidateIds, $allowedCandidateUserIds, $includeElectionWideForRepresentative) {
                $query->where(function (Builder $scopedQuery) use ($selectedCandidateIds, $allowedCandidateUserIds, $includeElectionWideForRepresentative) {
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

                    if ($includeElectionWideForRepresentative) {
                        $scopedQuery->orWhereRaw('1 = 1');
                    }
                });
            })
            ->when(!empty($requestedCandidateIds), function ($query) use ($requestedCandidateIds) {
                $query->whereIn('candidates.id', $requestedCandidateIds);
            })
            ->select('candidates.id', 'candidate_committee.votes');

        $this->applySortingCandidateVisibilityScope($visibleCandidatesQuery, $effectiveElectionId);

        $visibleCandidates = $visibleCandidatesQuery->get();

        $candidateVotes = $visibleCandidates
            ->mapWithKeys(fn ($candidate) => [(int) $candidate->id => (int) $candidate->votes]);

        return response()->json([
            'success' => true,
            'committee_id' => $committeeId,
            'total_attending' => (int) Voter::where('committee_id', $committeeId)->count(),
            'total_sorting_votes' => (int) $candidateVotes->sum(),
            'candidate_votes' => $candidateVotes,
            'sorting_status' => $this->resolveCurrentUserSortingStatus($user),
        ]);
    }

    public function sortingNamedPresets(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'error' => 'يجب تسجيل الدخول أولا.'
            ], 403);
        }

        $validated = $request->validate([
            'committee' => ['nullable', 'integer', 'exists:committees,id'],
        ]);

        $committeeId = (int) ($validated['committee'] ?? 0);
        $electionId = $this->resolveSortingNamedPresetElectionId($user, $committeeId);
        if ($electionId <= 0) {
            return response()->json([
                'success' => true,
                'election_id' => 0,
                'presets' => [],
            ]);
        }

        return response()->json([
            'success' => true,
            'election_id' => $electionId,
            'presets' => $this->getSortingNamedPresets($electionId),
        ]);
    }

    public function sortingSaveNamedPreset(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'error' => 'يجب تسجيل الدخول أولا.'
            ], 403);
        }

        $validated = $request->validate([
            'committee' => ['nullable', 'integer', 'exists:committees,id'],
            'name' => ['required', 'string', 'max:120'],
            'orders' => ['required', 'array', 'min:1'],
            'orders.*' => ['nullable', 'integer', 'min:1'],
            'preset_id' => ['nullable', 'string', 'max:80'],
        ]);

        $committeeId = (int) ($validated['committee'] ?? 0);
        $electionId = $this->resolveSortingNamedPresetElectionId($user, $committeeId);
        if ($electionId <= 0) {
            return response()->json([
                'message' => 'تعذر تحديد الحملة الانتخابية للحفظ.'
            ], 422);
        }

        $normalizedOrders = collect((array) ($validated['orders'] ?? []))
            ->mapWithKeys(function ($value, $candidateId) {
                $candidateId = (int) $candidateId;
                $orderValue = (int) $value;

                if ($candidateId <= 0 || $orderValue <= 0) {
                    return [];
                }

                return [$candidateId => $orderValue];
            })
            ->all();

        if (empty($normalizedOrders)) {
            return response()->json([
                'message' => 'لا توجد قيم ترتيب صالحة للحفظ.'
            ], 422);
        }

        $orderValues = array_values($normalizedOrders);
        if (count(array_unique($orderValues)) !== count($orderValues)) {
            return response()->json([
                'message' => 'أرقام الترتيب يجب أن تكون فريدة بدون تكرار.'
            ], 422);
        }

        $requestedCandidateIds = array_map('intval', array_keys($normalizedOrders));
        $validCandidateIds = Candidate::withoutGlobalScopes()
            ->where('election_id', $electionId)
            ->whereIn('id', $requestedCandidateIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if (count($validCandidateIds) !== count($requestedCandidateIds)) {
            return response()->json([
                'message' => 'بعض المرشحين لا يتبعون نفس الحملة الانتخابية.'
            ], 422);
        }

        $existingPresets = $this->getSortingNamedPresets($electionId);
        $presetId = trim((string) ($validated['preset_id'] ?? ''));
        if ($presetId === '') {
            $presetId = 'preset_' . now()->format('YmdHisv') . '_' . (int) $user->id;
        }

        $savedPreset = [
            'id' => $presetId,
            'name' => trim((string) $validated['name']),
            'orders' => $normalizedOrders,
            'updated_at' => now()->toDateTimeString(),
            'updated_by' => (int) $user->id,
        ];

        $updatedPresets = collect($existingPresets)
            ->reject(fn ($preset) => (string) ($preset['id'] ?? '') === $presetId)
            ->prepend($savedPreset)
            ->values()
            ->all();

        Setting::query()->updateOrCreate(
            ['option_key' => $this->sortingNamedPresetsSettingKey($electionId)],
            ['option_value' => $updatedPresets]
        );

        return response()->json([
            'success' => true,
            'preset_id' => $presetId,
            'presets' => $updatedPresets,
            'message' => 'تم حفظ الترتيب وإتاحته لكل مستخدمي الحملة.'
        ]);
    }

    public function sortingCurrentPaper(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'يجب تسجيل الدخول أولا.'], 403);
        }

        if (!$this->sortingPaperTablesReady()) {
            return response()->json([
                'success' => false,
                'message' => 'ميزة تتبع الأوراق غير مفعلة بعد. يرجى تنفيذ التحديثات (migrate).',
            ], 422);
        }

        $validated = $request->validate([
            'committee' => ['required', 'integer', 'exists:committees,id'],
        ]);

        $committeeId = (int) $validated['committee'];
        $paper = DB::table('sorting_papers')
            ->where('committee_id', $committeeId)
            ->orderByDesc('paper_number')
            ->first(['id', 'paper_number', 'created_at']);

        return response()->json([
            'success' => true,
            'paper' => $paper ? [
                'id' => (int) $paper->id,
                'number' => (int) $paper->paper_number,
                'created_at' => (string) $paper->created_at,
            ] : null,
        ]);
    }

    public function sortingNextPaper(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'يجب تسجيل الدخول أولا.'], 403);
        }

        if (!$this->sortingPaperTablesReady()) {
            return response()->json([
                'success' => false,
                'message' => 'ميزة تتبع الأوراق غير مفعلة بعد. يرجى تنفيذ التحديثات (migrate).',
            ], 422);
        }

        $validated = $request->validate([
            'committee' => ['required', 'integer', 'exists:committees,id'],
        ]);

        $committeeId = (int) $validated['committee'];
        $electionId = $this->resolveSortingNamedPresetElectionId($user, $committeeId);

        try {
            $paper = DB::transaction(function () use ($committeeId, $electionId, $user) {
                $latestPaperNumber = (int) (DB::table('sorting_papers')
                    ->where('committee_id', $committeeId)
                    ->lockForUpdate()
                    ->max('paper_number') ?? 0);

                $nextPaperNumber = $latestPaperNumber + 1;

                $paperId = DB::table('sorting_papers')->insertGetId([
                    'election_id' => $electionId > 0 ? $electionId : null,
                    'committee_id' => $committeeId,
                    'paper_number' => $nextPaperNumber,
                    'started_by_user_id' => (int) $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return [
                    'id' => (int) $paperId,
                    'number' => (int) $nextPaperNumber,
                ];
            });
        } catch (\Throwable $exception) {
            Log::error('sortingNextPaper failed', [
                'committee_id' => $committeeId,
                'user_id' => (int) $user->id,
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'تعذر بدء الورقة الآن. تحقق من تحديثات قاعدة البيانات.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'paper' => $paper,
            'message' => 'تم الانتقال إلى الورقة رقم ' . (int) ($paper['number'] ?? 0),
        ]);
    }

    public function sortingLogPaperEvent(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'يجب تسجيل الدخول أولا.'], 403);
        }

        if (!$this->sortingPaperTablesReady()) {
            return response()->json([
                'success' => false,
                'message' => 'ميزة تتبع الأوراق غير مفعلة بعد. يرجى تنفيذ التحديثات (migrate).',
            ], 422);
        }

        $validated = $request->validate([
            'committee' => ['required', 'integer', 'exists:committees,id'],
            'candidate_id' => ['required', 'integer', 'exists:candidates,id'],
            'action_type' => ['required', 'string', 'max:20'],
            'action_value' => ['required', 'integer', 'min:0'],
            'delta_votes' => ['required', 'integer'],
        ]);

        $committeeId = (int) $validated['committee'];
        $candidateId = (int) $validated['candidate_id'];
        $deltaVotes = (int) $validated['delta_votes'];

        $candidate = Candidate::withoutGlobalScopes()
            ->select(['id', 'election_id'])
            ->where('id', $candidateId)
            ->first();

        if (!$candidate) {
            return response()->json(['message' => 'المرشح غير موجود.'], 422);
        }

        $paper = DB::table('sorting_papers')
            ->where('committee_id', $committeeId)
            ->orderByDesc('paper_number')
            ->first(['id', 'paper_number']);

        if (!$paper) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم بدء أي ورقة بعد.',
            ], 422);
        }

        DB::table('sorting_paper_events')->insert([
            'sorting_paper_id' => (int) $paper->id,
            'committee_id' => $committeeId,
            'candidate_id' => $candidateId,
            'delta_votes' => $deltaVotes,
            'action_type' => (string) $validated['action_type'],
            'action_value' => (int) $validated['action_value'],
            'created_by_user_id' => (int) $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'paper_number' => (int) $paper->paper_number,
        ]);
    }

    public function sortingPaperReport(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'يجب تسجيل الدخول أولا.'], 403);
        }

        if (!$this->sortingPaperTablesReady()) {
            return response()->json([
                'success' => false,
                'message' => 'ميزة تتبع الأوراق غير مفعلة بعد. يرجى تنفيذ التحديثات (migrate).',
                'papers' => [],
            ], 422);
        }

        $validated = $request->validate([
            'committee' => ['required', 'integer', 'exists:committees,id'],
        ]);

        $committeeId = (int) $validated['committee'];

        $papers = DB::table('sorting_papers')
            ->where('committee_id', $committeeId)
            ->orderBy('paper_number')
            ->get(['id', 'paper_number', 'created_at']);

        if ($papers->isEmpty()) {
            return response()->json([
                'success' => true,
                'papers' => [],
            ]);
        }

        $paperIds = $papers->pluck('id')->map(fn ($id) => (int) $id)->values()->all();

        $events = DB::table('sorting_paper_events as e')
            ->join('sorting_papers as p', 'p.id', '=', 'e.sorting_paper_id')
            ->join('candidates as c', 'c.id', '=', 'e.candidate_id')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->whereIn('e.sorting_paper_id', $paperIds)
            ->select([
                'e.sorting_paper_id',
                'p.paper_number',
                'c.id as candidate_id',
                'c.candidate_type',
                'c.list_name',
                'u.name as candidate_name',
                DB::raw('SUM(e.delta_votes) as net_votes'),
                DB::raw("SUM(CASE WHEN e.action_type = 'set' THEN 1 ELSE 0 END) as set_actions_count"),
            ])
            ->groupBy('e.sorting_paper_id', 'p.paper_number', 'c.id', 'c.candidate_type', 'c.list_name', 'u.name')
            ->orderBy('p.paper_number')
            ->get();

        $eventsByPaper = $events
            ->groupBy('sorting_paper_id')
            ->map(function ($paperEvents) {
                return collect($paperEvents)
                    ->map(function ($event) {
                        $candidateType = (string) ($event->candidate_type ?? '');
                        $displayName = (string) ($event->candidate_name ?? '');

                        if ($candidateType === 'list_leader') {
                            $listName = trim((string) ($event->list_name ?? ''));
                            if ($listName !== '') {
                                $displayName = 'القائمة: ' . $listName;
                            }
                        }

                        $setActionsCount = (int) ($event->set_actions_count ?? 0);
                        $setActionLabel = '';
                        if ($setActionsCount === 1) {
                            $setActionLabel = 'تحديد جديد';
                        } elseif ($setActionsCount > 1) {
                            $setActionLabel = 'إعادة مجموع الأصوات';
                        }

                        return [
                            'candidate_id' => (int) ($event->candidate_id ?? 0),
                            'candidate_name' => $displayName,
                            'votes' => (int) ($event->net_votes ?? 0),
                            'set_action_label' => $setActionLabel,
                        ];
                    })
                    ->filter(fn (array $item) => (int) ($item['votes'] ?? 0) !== 0)
                    ->sortByDesc('votes')
                    ->values()
                    ->all();
            });

        $result = $papers->map(function ($paper) use ($eventsByPaper) {
            $paperId = (int) ($paper->id ?? 0);
            $items = (array) ($eventsByPaper->get($paperId, []));

            return [
                'id' => $paperId,
                'number' => (int) ($paper->paper_number ?? 0),
                'created_at' => (string) ($paper->created_at ?? ''),
                'items' => $items,
                'total_votes' => (int) collect($items)->sum('votes'),
            ];
        })->values()->all();

        return response()->json([
            'success' => true,
            'papers' => $result,
        ]);
    }

    public function sortingResetPapers(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'يجب تسجيل الدخول أولا.'], 403);
        }

        if (!$this->sortingPaperTablesReady()) {
            return response()->json([
                'success' => false,
                'message' => 'ميزة تتبع الأوراق غير مفعلة بعد. يرجى تنفيذ التحديثات (migrate).',
            ], 422);
        }

        $validated = $request->validate([
            'committee' => ['required', 'integer', 'exists:committees,id'],
        ]);

        $committeeId = (int) $validated['committee'];

        try {
            $deletedPapers = DB::transaction(function () use ($committeeId) {
                $paperIds = DB::table('sorting_papers')
                    ->where('committee_id', $committeeId)
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->all();

                if (empty($paperIds)) {
                    return 0;
                }

                // Keep explicit event cleanup to be safe even if FK constraints differ between environments.
                DB::table('sorting_paper_events')
                    ->whereIn('sorting_paper_id', $paperIds)
                    ->delete();

                return (int) DB::table('sorting_papers')
                    ->whereIn('id', $paperIds)
                    ->delete();
            });
        } catch (\Throwable $exception) {
            Log::error('sortingResetPapers failed', [
                'committee_id' => $committeeId,
                'user_id' => (int) $user->id,
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'تعذر إعادة تعيين سجل الأوراق حاليا. حاول مرة أخرى.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'deleted_papers' => $deletedPapers,
            'message' => 'تمت إعادة تعيين سجل الأوراق لهذه اللجنة بنجاح.',
        ]);
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

    private function resolveSortingAllowedCandidateUserIds(?User $user, int $sortingScopeElectionId = 0): ?array
    {
        if (!$user) {
            return null;
        }

        if (!$this->shouldScopeSortingCandidates($user)) {
            return null;
        }

        $isRepresentativeLinked = $user->hasRole('مندوب') || $user->representatives()->exists();
        $userElectionId = $sortingScopeElectionId > 0
            ? $sortingScopeElectionId
            : (int) ($user->election_id ?? 0);

        if ($isRepresentativeLinked) {
            return $this->resolveSortingCandidateUserIdsByCreator(
                (int) ($user->creator_id ?? 0),
                $userElectionId
            );
        }

        if ($user->hasRole('مرشح')) {
            $ownedCandidateQuery = Candidate::withoutGlobalScopes()
                ->where('user_id', (int) $user->id);

            if ($userElectionId > 0) {
                $ownedCandidateQuery->where('election_id', $userElectionId);
            }

            $this->applySortingCandidateVisibilityScope($ownedCandidateQuery, $userElectionId);

            return $ownedCandidateQuery
                ->pluck('user_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
        }

        return $this->resolveSortingCandidateUserIdsByCreator(
            (int) ($user->creator_id ?? 0),
            $userElectionId
        );
    }

    private function resolveSortingCandidateUserIdsByCreator(int $creatorId, int $userElectionId = 0): array
    {
        if ($creatorId <= 0) {
            return [];
        }

        $creatorCandidateQuery = Candidate::withoutGlobalScopes()
            ->where('user_id', $creatorId);

        if ($userElectionId > 0) {
            $creatorCandidateQuery->where('election_id', $userElectionId);
        }

        $this->applySortingCandidateVisibilityScope($creatorCandidateQuery, $userElectionId);

        $resolvedUserIds = $creatorCandidateQuery
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($userElectionId > 0 && $this->campaignUsesListSystem($userElectionId)) {
            $ownerCandidate = Candidate::withoutGlobalScopes()
                ->select(['id', 'candidate_type', 'list_leader_candidate_id'])
                ->where('user_id', $creatorId)
                ->where('election_id', $userElectionId)
                ->first();

            if ($ownerCandidate && (int) ($ownerCandidate->list_leader_candidate_id ?? 0) > 0) {
                $listLeaderUserId = (int) Candidate::withoutGlobalScopes()
                    ->where('id', (int) $ownerCandidate->list_leader_candidate_id)
                    ->value('user_id');

                if ($listLeaderUserId > 0) {
                    $resolvedUserIds[] = $listLeaderUserId;
                }
            }
        }

        return collect($resolvedUserIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function applySortingCandidateVisibilityScope($candidateQuery, int $electionId = 0): void
    {
        if (!is_object($candidateQuery) || !method_exists($candidateQuery, 'where')) {
            return;
        }

        if (!Schema::hasColumn('candidates', 'is_actual_list_candidate')) {
            return;
        }

        $listCampaign = $electionId > 0 && $this->campaignUsesListSystem($electionId);

        if (!$listCampaign) {
            $candidateQuery->where(function (Builder $query) {
                $query->where('is_actual_list_candidate', true)
                    ->orWhereNull('is_actual_list_candidate');
            });

            return;
        }

        $candidateQuery->where(function (Builder $query) {
            $query->where('candidate_type', 'list_leader')
                ->orWhere(function (Builder $actualCandidatesQuery) {
                    $actualCandidatesQuery->where('is_actual_list_candidate', true)
                        ->orWhereNull('is_actual_list_candidate');
                });
        });
    }

    private function applyActualCandidateOnlyScope($candidateQuery): void
    {
        if (!is_object($candidateQuery) || !method_exists($candidateQuery, 'where')) {
            return;
        }

        // Do not expose administrative-only list leader candidates in sorting.
        if (!Schema::hasColumn('candidates', 'is_actual_list_candidate')) {
            return;
        }

        $candidateQuery->where(function (Builder $query) {
            $query->where('is_actual_list_candidate', true)
                ->orWhereNull('is_actual_list_candidate');
        });
    }

    private function shouldScopeSortingCandidates(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->hasRole('Administrator')) {
            return false;
        }

        // Representative linkage must always take precedence over other roles.
        if ($user->hasRole('مندوب') || $user->representatives()->exists()) {
            return true;
        }

        // Apply same restriction for normal candidates and list-member candidates.
        if ($user->hasRole('مرشح')) {
            return true;
        }

        return true;
    }

    private function resolveSortingRepresentativeSelectedCandidateIds(?User $user, ?int $committeeId = null, int $sortingScopeElectionId = 0): array
    {
        if (!$user) {
            return [];
        }

        if (!Schema::hasTable('candidate_representative')) {
            return [];
        }

        $representativesQuery = $user->representatives()->with('candidates:id');
        if (!empty($committeeId) && $committeeId > 0) {
            $representativesQuery->where('committee_id', (int) $committeeId);
        }

        $representatives = $representativesQuery->get();
        if ($representatives->isEmpty()) {
            return [];
        }

        $candidateIds = $representatives
            ->flatMap(fn ($representative) => $representative->candidates)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($candidateIds->isEmpty()) {
            return [];
        }

        $allowedSelectedCandidateQuery = Candidate::withoutGlobalScopes()
            ->whereIn('id', $candidateIds->all());

        $this->applySortingCandidateVisibilityScope($allowedSelectedCandidateQuery, $sortingScopeElectionId);

        return $allowedSelectedCandidateQuery
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function resolveSortingScopeElectionId(?User $user, ?int $committeeId = null): int
    {
        if ($committeeId && $committeeId > 0 && Schema::hasColumn('committees', 'election_id')) {
            $committeeElectionId = (int) Committee::query()
                ->where('id', (int) $committeeId)
                ->value('election_id');

            if ($committeeElectionId > 0) {
                return $committeeElectionId;
            }
        }

        $userElectionId = (int) ($user?->election_id ?? 0);
        if ($userElectionId > 0) {
            return $userElectionId;
        }

        $creatorElectionId = (int) Candidate::withoutGlobalScopes()
            ->where('user_id', (int) ($user?->creator_id ?? 0))
            ->value('election_id');

        if ($creatorElectionId > 0) {
            return $creatorElectionId;
        }

        return (int) Candidate::withoutGlobalScopes()
            ->where('user_id', (int) ($user?->id ?? 0))
            ->value('election_id');
    }

    private function campaignUsesListSystem(int $electionId): bool
    {
        static $listCampaignCache = [];

        if ($electionId <= 0) {
            return false;
        }

        if (array_key_exists($electionId, $listCampaignCache)) {
            return $listCampaignCache[$electionId];
        }

        $listCampaignCache[$electionId] = Candidate::withoutGlobalScopes()
            ->where('election_id', $electionId)
            ->where('candidate_type', 'list_leader')
            ->exists();

        return $listCampaignCache[$electionId];
    }

    private function resolveSortingDefaultCommitteeId(?User $user): int
    {
        if (!$user) {
            return 0;
        }

        $electionId = (int) ($user->election_id ?? 0);
        if ($electionId <= 0) {
            $electionId = (int) Candidate::withoutGlobalScopes()
                ->where('user_id', (int) ($user->creator_id ?? 0))
                ->value('election_id');
        }

        $committeeQuery = Committee::query();
        if ($electionId > 0 && Schema::hasColumn('committees', 'election_id')) {
            $committeeQuery->where('election_id', $electionId);
        }

        return (int) ($committeeQuery->orderBy('id')->value('id') ?? 0);
    }

    private function ensureSortingCommitteeCandidateMapping(
        Committee $committee,
        array $selectedCandidateIds,
        ?array $allowedCandidateUserIds,
        int $effectiveElectionId
    ): void {
        if (!Schema::hasTable('candidate_committee')) {
            return;
        }

        $candidateIdsToAttachQuery = Candidate::withoutGlobalScopes()
            ->when($effectiveElectionId > 0, function ($query) use ($effectiveElectionId) {
                $query->where('election_id', $effectiveElectionId);
            })
            ->where(function (Builder $query) use ($selectedCandidateIds, $allowedCandidateUserIds) {
                $hasSelectedCandidateIds = !empty($selectedCandidateIds);
                $hasAllowedCreatorCandidateUsers = is_array($allowedCandidateUserIds) && !empty($allowedCandidateUserIds);

                if ($hasSelectedCandidateIds) {
                    $query->whereIn('id', $selectedCandidateIds);
                }

                if ($hasAllowedCreatorCandidateUsers) {
                    if ($hasSelectedCandidateIds) {
                        $query->orWhereIn('user_id', $allowedCandidateUserIds);
                    } else {
                        $query->whereIn('user_id', $allowedCandidateUserIds);
                    }
                }

                if (!$hasSelectedCandidateIds && !$hasAllowedCreatorCandidateUsers) {
                    $query->whereRaw('1 = 0');
                }
            });

        $this->applySortingCandidateVisibilityScope($candidateIdsToAttachQuery, $effectiveElectionId);

        $candidateIdsToAttach = $candidateIdsToAttachQuery
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($candidateIdsToAttach)) {
            return;
        }

        $committee->candidates()->syncWithoutDetaching($candidateIdsToAttach);
    }

    private function resolveCurrentUserSortingStatus(?User $user): int
    {
        if (!$user) {
            return 1;
        }

        $cachedStatus = null;
        try {
            $cachedStatus = Cache::get($this->sortingStatusCacheKey((int) $user->id));
        } catch (\Throwable $exception) {
            $cachedStatus = session()->get($this->sortingStatusSessionKey((int) $user->id));
        }

        if ($cachedStatus === null) {
            return 1;
        }

        return (int) ((int) $cachedStatus === 1 ? 1 : 0);
    }

    private function sortingStatusCacheKey(int $userId): string
    {
        return 'sorting_status_user_' . $userId;
    }

    private function sortingStatusSessionKey(int $userId): string
    {
        return 'sorting_status_user_fallback_' . $userId;
    }

    private function resolveSortingNamedPresetElectionId(?User $user, int $committeeId = 0): int
    {
        $scopeElectionId = $this->resolveSortingScopeElectionId($user, $committeeId > 0 ? $committeeId : null);
        if ($scopeElectionId > 0) {
            return $scopeElectionId;
        }

        if ($committeeId > 0 && Schema::hasColumn('committees', 'election_id')) {
            return (int) (Committee::query()->where('id', $committeeId)->value('election_id') ?? 0);
        }

        return (int) ($user?->election_id ?? 0);
    }

    private function sortingNamedPresetsSettingKey(int $electionId): string
    {
        return 'sorting_named_presets_election_' . $electionId;
    }

    private function getSortingNamedPresets(int $electionId): array
    {
        if ($electionId <= 0) {
            return [];
        }

        $setting = Setting::query()
            ->where('option_key', $this->sortingNamedPresetsSettingKey($electionId))
            ->first();

        $presets = collect((array) ($setting?->option_value ?? []))
            ->map(function ($preset) {
                $id = trim((string) ($preset['id'] ?? ''));
                $name = trim((string) ($preset['name'] ?? ''));
                $orders = collect((array) ($preset['orders'] ?? []))
                    ->mapWithKeys(function ($orderValue, $candidateId) {
                        $candidateId = (int) $candidateId;
                        $orderValue = (int) $orderValue;

                        if ($candidateId <= 0 || $orderValue <= 0) {
                            return [];
                        }

                        return [$candidateId => $orderValue];
                    })
                    ->all();

                if ($id === '' || $name === '' || empty($orders)) {
                    return null;
                }

                return [
                    'id' => $id,
                    'name' => $name,
                    'orders' => $orders,
                    'updated_at' => (string) ($preset['updated_at'] ?? ''),
                    'updated_by' => (int) ($preset['updated_by'] ?? 0),
                ];
            })
            ->filter()
            ->values()
            ->all();

        return $presets;
    }

    private function sortingPaperTablesReady(): bool
    {
        return Schema::hasTable('sorting_papers')
            && Schema::hasTable('sorting_paper_events');
    }

}
