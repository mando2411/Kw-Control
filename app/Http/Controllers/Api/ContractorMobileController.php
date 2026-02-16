<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ArabicHelper;
use App\Http\Controllers\Controller;
use App\Models\Contractor;
use App\Models\Family;
use App\Models\Group;
use App\Models\Voter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractorMobileController extends Controller
{
    private function resolveContractor(string $token): Contractor
    {
        return Contractor::where('token', $token)->firstOrFail();
    }

    public function bootstrap(string $token): JsonResponse
    {
        $contractor = $this->resolveContractor($token);

        $electionId = $contractor->election_id ?? optional($contractor->creator)->election_id;
        $election = optional($contractor->creator)->election;

        $families = Family::query()
            ->select('id', 'name')
            ->when($electionId, function ($query) use ($electionId) {
                $query->where('election_id', $electionId);
            })
            ->orderBy('name', 'asc')
            ->get();

        $groups = $contractor->groups()
            ->withCount('voters')
            ->latest('updated_at')
            ->get(['id', 'name', 'type', 'contractor_id', 'updated_at']);

        $formatElectionValue = function ($value, string $format): ?string {
            if (blank($value)) {
                return null;
            }

            if ($value instanceof \DateTimeInterface) {
                return $value->format($format);
            }

            try {
                return Carbon::parse((string) $value)->format($format);
            } catch (\Throwable $e) {
                return null;
            }
        };

        return response()->json([
            'contractor' => [
                'id' => $contractor->id,
                'name' => $contractor->name,
                'token' => $contractor->token,
                'creator_name' => optional($contractor->creator)->name,
                'creator_image' => optional($contractor->creator)->image,
                'election_name' => optional(optional($contractor->creator)->election)->name,
            ],
            'election' => [
                'name' => optional($election)->name,
                'start_date' => $formatElectionValue($election?->start_date, 'Y-m-d'),
                'start_time' => $formatElectionValue($election?->start_time, 'H:i:s'),
                'end_date' => $formatElectionValue($election?->end_date, 'Y-m-d'),
                'end_time' => $formatElectionValue($election?->end_time, 'H:i:s'),
            ],
            'links' => [
                'about_url' => 'https://kw-control.com/about-control',
                'support_path' => '/contract/' . $contractor->token . '/support',
                'download_path' => '/download/contractor-app',
            ],
            'families' => $families,
            'groups' => $groups,
        ]);
    }

    public function voters(Request $request, string $token): JsonResponse
    {
        $contractor = $this->resolveContractor($token);

        $request->validate([
            'name' => ['nullable', 'string'],
            'family' => ['nullable', 'integer'],
            'sibling' => ['nullable', 'string'],
            'sibling_exclude_id' => ['nullable', 'integer'],
            'scope' => ['nullable', 'in:all,attached,available'],
            'exclude_grouped' => ['nullable', 'in:0,1'],
            'ids_only' => ['nullable', 'in:0,1'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $votersQuery = Voter::query();

        if ($request->filled('name')) {
            $normalizedInput = ArabicHelper::normalizeArabic((string) $request->input('name'));
            $votersQuery->where('normalized_name', 'LIKE', $normalizedInput . '%');
        }

        if ($request->filled('family')) {
            $votersQuery->where('family_id', (int) $request->input('family'));
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
            } else {
                $normalizedSibling = ArabicHelper::normalizeArabic($sibling);
                $votersQuery->where('normalized_name', 'LIKE', $normalizedSibling . '%');
            }

            if ($request->filled('sibling_exclude_id')) {
                $votersQuery->where('id', '!=', (int) $request->input('sibling_exclude_id'));
            }
        }

        $electionId = $contractor->election_id ?? optional($contractor->creator)->election_id;
        if ($electionId) {
            $votersQuery->whereHas('election', function ($query) use ($electionId) {
                $query->where('election_id', $electionId);
            });
        }

        $scope = (string) $request->input('scope', 'all');

        $attachedIdsSubQuery = DB::table('contractor_voter')
            ->select('voter_id')
            ->where('contractor_id', $contractor->id);

        if ($scope === 'attached') {
            $votersQuery->whereIn('id', $attachedIdsSubQuery);
        } elseif ($scope === 'available') {
            $votersQuery->whereNotIn('id', $attachedIdsSubQuery);
        }

        if ((string) $request->input('exclude_grouped', '0') === '1') {
            $votersQuery->whereDoesntHave('groups', function ($query) use ($contractor) {
                $query->where('contractor_id', $contractor->id);
            });
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

        $perPage = (int) $request->input('per_page', 20);
        $page = max((int) $request->input('page', 1), 1);

        $paginated = $votersQuery->paginate($perPage, ['*'], 'page', $page);
        $items = collect($paginated->items());
        $itemIds = $items->pluck('id')->filter()->values();

        $attachedIds = [];
        $pivotPercentages = [];
        if ($itemIds->isNotEmpty()) {
            $pivotRows = DB::table('contractor_voter')
                ->where('contractor_id', $contractor->id)
                ->whereIn('voter_id', $itemIds->all())
                ->get(['voter_id', 'percentage']);

            $attachedIds = $pivotRows
                ->pluck('voter_id')
                ->map(fn ($value) => (int) $value)
                ->all();

            foreach ($pivotRows as $pivotRow) {
                $pivotPercentages[(int) $pivotRow->voter_id] = (int) ($pivotRow->percentage ?? 0);
            }
        }

        $attachedLookup = array_flip($attachedIds);
        $mappedItems = $items->map(function ($voter) use ($attachedLookup, $pivotPercentages) {
            return [
                'id' => $voter->id,
                'name' => $voter->name,
                'status' => $voter->status,
                'restricted' => $voter->restricted,
                'phone1' => $voter->phone1,
                'committee_id' => $voter->committee_id,
                'family_id' => $voter->family_id,
                'updated_at' => $voter->updated_at,
                'percentage' => $pivotPercentages[(int) $voter->id] ?? 0,
                'is_added' => isset($attachedLookup[(int) $voter->id]),
            ];
        })->values();

        return response()->json([
            'voters' => $mappedItems,
            'pagination' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'has_more' => $paginated->hasMorePages(),
            ],
        ]);
    }

    public function voterDetails(string $token, int $voterId): JsonResponse
    {
        $contractor = $this->resolveContractor($token);
        $voter = Voter::findOrFail($voterId);

        $pivot = $voter->contractors()->where('contractor_id', $contractor->id)->first()?->pivot;

        return response()->json([
            'voter' => [
                'id' => $voter->id,
                'name' => $voter->name,
                'phone1' => $voter->phone1,
                'status' => $voter->status,
                'restricted' => $voter->restricted,
                'alsndok' => $voter->alsndok,
                'percentage' => $pivot?->percentage ?? 0,
            ],
            'committee_name' => optional($voter->committee)->name,
            'school_name' => optional(optional($voter->committee)->school)->name,
        ]);
    }

    public function updatePhone(Request $request, string $token, int $voterId): JsonResponse
    {
        $this->resolveContractor($token);

        $data = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $voter = Voter::findOrFail($voterId);
        $voter->update(['phone1' => $data['phone']]);

        return response()->json([
            'message' => 'تم تعديل رقم الهاتف بنجاح',
        ]);
    }

    public function updatePercentage(Request $request, string $token, int $voterId): JsonResponse
    {
        $contractor = $this->resolveContractor($token);

        $data = $request->validate([
            'percentage' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $voter = Voter::findOrFail($voterId);
        $pivot = $voter->contractors()->where('contractor_id', $contractor->id)->first()?->pivot;

        if (!$pivot) {
            return response()->json([
                'message' => 'الناخب غير مضاف لهذا المتعهد',
            ], 422);
        }

        $pivot->percentage = $data['percentage'];
        $pivot->save();

        return response()->json([
            'message' => 'تم تحديث نسبة الالتزام',
        ]);
    }

    public function setVoterAttachment(Request $request, string $token, int $voterId): JsonResponse
    {
        $contractor = $this->resolveContractor($token);

        $data = $request->validate([
            'action' => ['required', 'in:attach,detach'],
        ]);

        if ($data['action'] === 'attach') {
            $isInVoters = $contractor->voters()->where('voter_id', $voterId)->exists();
            $isInSoftDeletes = $contractor->softDelete()->where('voter_id', $voterId)->exists();

            if ($isInSoftDeletes) {
                $contractor->softDelete()->detach($voterId);
            }

            if (!$isInVoters) {
                $contractor->voters()->attach($voterId);
            }

            return response()->json([
                'message' => 'تمت الاضافة بنجاح',
            ]);
        }

        $contractor->voters()->detach([$voterId]);
        $contractor->softDelete()->syncWithoutDetaching([$voterId]);

        return response()->json([
            'message' => 'تم الحذف بنجاح',
        ]);
    }

    public function setVotersAttachmentBulk(Request $request, string $token): JsonResponse
    {
        $contractor = $this->resolveContractor($token);

        $data = $request->validate([
            'action' => ['required', 'in:attach,detach'],
            'voter_ids' => ['required', 'array', 'min:1'],
            'voter_ids.*' => ['integer'],
        ]);

        $voterIds = collect($data['voter_ids'])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($voterIds->isEmpty()) {
            return response()->json([
                'message' => 'لم يتم اختيار اي ناخب',
            ], 422);
        }

        if ($data['action'] === 'attach') {
            foreach ($voterIds as $voterId) {
                $isInVoters = $contractor->voters()->where('voter_id', $voterId)->exists();
                $isInSoftDeletes = $contractor->softDelete()->where('voter_id', $voterId)->exists();

                if ($isInSoftDeletes) {
                    $contractor->softDelete()->detach($voterId);
                }

                if (!$isInVoters) {
                    $contractor->voters()->attach($voterId);
                }
            }

            return response()->json([
                'message' => 'تمت الاضافة بنجاح',
                'count' => $voterIds->count(),
            ]);
        }

        $contractor->voters()->detach($voterIds->all());
        $contractor->softDelete()->syncWithoutDetaching($voterIds->all());

        return response()->json([
            'message' => 'تم الحذف بنجاح',
            'count' => $voterIds->count(),
        ]);
    }

    public function groups(string $token): JsonResponse
    {
        $contractor = $this->resolveContractor($token);

        $groups = $contractor->groups()
            ->withCount('voters')
            ->latest('updated_at')
            ->get(['id', 'name', 'type', 'updated_at']);

        return response()->json([
            'groups' => $groups,
        ]);
    }

    public function createGroup(Request $request, string $token): JsonResponse
    {
        $contractor = $this->resolveContractor($token);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:مضمون,تحت المراجعة'],
        ]);

        $group = Group::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'contractor_id' => $contractor->id,
        ]);

        return response()->json([
            'message' => 'تم إنشاء القائمة بنجاح',
            'group' => $group,
        ]);
    }

    public function groupDetails(Request $request, string $token, int $groupId): JsonResponse
    {
        $contractor = $this->resolveContractor($token);

        $request->validate([
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $group = $contractor->groups()->where('id', $groupId)->firstOrFail();
        $perPage = (int) $request->input('per_page', 20);
        $page = max((int) $request->input('page', 1), 1);

        $paginated = $group->voters()
            ->orderBy('name', 'asc')
            ->paginate($perPage, ['voters.*'], 'page', $page);

        $items = collect($paginated->items());
        $itemIds = $items->pluck('id')->filter()->values();
        $pivotPercentages = [];

        if ($itemIds->isNotEmpty()) {
            $pivotRows = DB::table('contractor_voter')
                ->where('contractor_id', $contractor->id)
                ->whereIn('voter_id', $itemIds->all())
                ->get(['voter_id', 'percentage']);

            foreach ($pivotRows as $pivotRow) {
                $pivotPercentages[(int) $pivotRow->voter_id] = (int) ($pivotRow->percentage ?? 0);
            }
        }

        $mappedItems = $items->map(function ($voter) use ($pivotPercentages) {
            return [
                'id' => $voter->id,
                'name' => $voter->name,
                'status' => $voter->status,
                'restricted' => $voter->restricted,
                'phone1' => $voter->phone1,
                'committee_id' => $voter->committee_id,
                'family_id' => $voter->family_id,
                'updated_at' => $voter->updated_at,
                'percentage' => $pivotPercentages[(int) $voter->id] ?? 0,
                'is_added' => true,
            ];
        })->values();

        return response()->json([
            'group' => [
                'id' => $group->id,
                'name' => $group->name,
                'type' => $group->type,
            ],
            'voters' => $mappedItems,
            'pagination' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'has_more' => $paginated->hasMorePages(),
            ],
        ]);
    }

    public function updateGroup(Request $request, string $token, int $groupId): JsonResponse
    {
        $contractor = $this->resolveContractor($token);
        $group = $contractor->groups()->where('id', $groupId)->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:مضمون,تحت المراجعة'],
        ]);

        $group->update($data);

        return response()->json([
            'message' => 'تم تعديل القائمة بنجاح',
            'group' => $group,
        ]);
    }

    public function deleteGroup(string $token, int $groupId): JsonResponse
    {
        $contractor = $this->resolveContractor($token);
        $group = $contractor->groups()->where('id', $groupId)->firstOrFail();

        $group->voters()->detach();
        $group->delete();

        return response()->json([
            'message' => 'تم حذف المجموعة بنجاح',
        ]);
    }

    public function groupVotersAction(Request $request, string $token, int $groupId): JsonResponse
    {
        $contractor = $this->resolveContractor($token);
        $group = $contractor->groups()->where('id', $groupId)->firstOrFail();

        $data = $request->validate([
            'action' => ['required', 'in:delete_from_group,delete_from_contractor,move_to_group'],
            'voter_ids' => ['required', 'array', 'min:1'],
            'voter_ids.*' => ['integer'],
            'target_group_id' => ['nullable', 'integer'],
        ]);

        $voterIds = collect($data['voter_ids'])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($voterIds->isEmpty()) {
            return response()->json([
                'message' => 'لم يتم اختيار اي ناخب',
            ], 422);
        }

        $group->voters()->detach($voterIds->all());

        if ($data['action'] === 'delete_from_contractor') {
            $contractor->voters()->detach($voterIds->all());
            $contractor->softDelete()->syncWithoutDetaching($voterIds->all());

            return response()->json([
                'message' => 'تم حذف الاسماء من المضامين',
                'count' => $voterIds->count(),
            ]);
        }

        if ($data['action'] === 'move_to_group') {
            $targetGroupId = (int) ($data['target_group_id'] ?? 0);
            $targetGroup = $contractor->groups()
                ->where('id', $targetGroupId)
                ->where('id', '!=', $group->id)
                ->firstOrFail();

            $targetGroup->voters()->syncWithoutDetaching($voterIds->all());

            return response()->json([
                'message' => 'تم النقل بنجاح',
                'count' => $voterIds->count(),
                'target_group_id' => $targetGroup->id,
            ]);
        }

        return response()->json([
            'message' => 'تم حذف الاسماء من المجموعة',
            'count' => $voterIds->count(),
        ]);
    }
}
