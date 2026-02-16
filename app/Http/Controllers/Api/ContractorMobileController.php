<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ArabicHelper;
use App\Http\Controllers\Controller;
use App\Models\Contractor;
use App\Models\Family;
use App\Models\Voter;
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

        $families = Family::query()
            ->select('id', 'name')
            ->when($electionId, function ($query) use ($electionId) {
                $query->where('election_id', $electionId);
            })
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'contractor' => [
                'id' => $contractor->id,
                'name' => $contractor->name,
                'token' => $contractor->token,
                'creator_name' => optional($contractor->creator)->name,
                'election_name' => optional(optional($contractor->creator)->election)->name,
            ],
            'families' => $families,
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

        $scope = (string) $request->input('scope', 'available');

        $attachedIdsSubQuery = DB::table('contractor_voter')
            ->select('voter_id')
            ->where('contractor_id', $contractor->id);

        $notAddedIdsSubQuery = DB::table('contractor_voter_delete')
            ->select('voter_id')
            ->where('contractor_id', $contractor->id);

        $allRegisteredIdsSubQuery = DB::table('contractor_voter')
            ->select('voter_id')
            ->where('contractor_id', $contractor->id)
            ->union(
                DB::table('contractor_voter_delete')
                    ->select('voter_id')
                    ->where('contractor_id', $contractor->id)
            );

        if ($scope === 'attached') {
            $votersQuery->whereIn('id', $attachedIdsSubQuery);
        } elseif ($scope === 'all') {
            $votersQuery->whereIn('id', $allRegisteredIdsSubQuery);
        } else {
            $votersQuery->whereIn('id', $notAddedIdsSubQuery);
        }

        if ((string) $request->input('exclude_grouped', '0') === '1') {
            $votersQuery->whereDoesntHave('groups', function ($query) use ($contractor) {
                $query->where('contractor_id', $contractor->id);
            });
        }

        $votersQuery->orderBy('name', 'asc');

        $perPage = (int) $request->input('per_page', 20);
        $page = max((int) $request->input('page', 1), 1);

        $paginated = $votersQuery->paginate($perPage, ['*'], 'page', $page);
        $items = collect($paginated->items());
        $itemIds = $items->pluck('id')->filter()->values();

        $attachedIds = [];
        if ($itemIds->isNotEmpty()) {
            $attachedIds = DB::table('contractor_voter')
                ->where('contractor_id', $contractor->id)
                ->whereIn('voter_id', $itemIds->all())
                ->pluck('voter_id')
                ->map(fn ($value) => (int) $value)
                ->all();
        }

        $attachedLookup = array_flip($attachedIds);
        $mappedItems = $items->map(function ($voter) use ($attachedLookup) {
            return [
                'id' => $voter->id,
                'name' => $voter->name,
                'status' => $voter->status,
                'restricted' => $voter->restricted,
                'phone1' => $voter->phone1,
                'committee_id' => $voter->committee_id,
                'family_id' => $voter->family_id,
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
}
