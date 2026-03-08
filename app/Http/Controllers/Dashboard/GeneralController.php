<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Voter;
use App\Models\Committee;
use App\Models\Candidate;
use App\Models\Contractor;
use App\Models\User;
use App\Enums\Type;
use App\Scopes\ElectionScope;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;

class GeneralController extends Controller
{
    //==============================================================
    public function fetchContractorsForUser($user_id){
    // return $user_id;
        $contractors = Contractor::where(['creator_id'=>$user_id,'parent_id'=>Null])->pluck('name', 'id');
        return response()->json($contractors);
    }
    //==============================================================
    public function fetchSubContractorsForMain($main_id){
        $contractors = Contractor::where('parent_id', $main_id)->pluck('name', 'id');
        return response()->json($contractors);
    }
    //==============================================================
    public function fetchVotersForCommittee($committee_id,Request $request){
        $committee = Committee::withoutGlobalScopes()->find($committee_id);
        if (!$committee) {
            return response()->json([
                'success' => false,
                'message' => 'اللجنة غير موجودة.',
                'voters' => [],
            ], 404);
        }

        $scope = $this->resolveAttendingScope(auth()->user());
        if (!$scope['is_admin'] && !in_array((int) $committee->id, $scope['committee_ids'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح بعرض هذه اللجنة.',
                'voters' => [],
            ], 403);
        }

        $searchValue = trim((string) $request->input('searchValue', ''));
        $voterTypes = $this->resolveCommitteeVoterTypeVariants((string) ($committee->type ?? ''));
        $electionId = (int) ($committee->election_id ?? 0);

        $votersQuery = Voter::withoutGlobalScope(ElectionScope::class)
            ->whereIn('type', $voterTypes);

        $this->applyElectionVoterPivotScopeWhenAvailable($votersQuery, $electionId);

        if ($searchValue !== '') {
            $votersQuery = $this->search2($votersQuery, $searchValue);
        }

        $voters = $votersQuery
            ->orderBy('name', 'asc')
            ->limit(100)
            ->get(['id', 'name', 'alsndok', 'status']);

        return response()->json([
            'success' => true,
            'voters' => $voters,
        ]);
    }
    //==============================================================
    public function fetchAttendingCountForCommittee($committee_id){
        $committee = Committee::withoutGlobalScopes()->find($committee_id);
        if (!$committee) {
            return response()->json([
                'success' => false,
                'message' => 'اللجنة غير موجودة.',
                'attend_count' => 0,
                'voter_count' => 0,
            ], 404);
        }

        $scope = $this->resolveAttendingScope(auth()->user());
        if (!$scope['is_admin'] && !in_array((int) $committee->id, $scope['committee_ids'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح بعرض هذه اللجنة.',
                'attend_count' => 0,
                'voter_count' => 0,
            ], 403);
        }

        $voterTypes = $this->resolveCommitteeVoterTypeVariants((string) ($committee->type ?? ''));
        $electionId = (int) ($committee->election_id ?? 0);

        $baseQuery = Voter::withoutGlobalScope(ElectionScope::class)
            ->whereIn('type', $voterTypes);

        $this->applyElectionVoterPivotScopeWhenAvailable($baseQuery, $electionId);

        $voterCount = (clone $baseQuery)->count();

        $attendCount = (clone $baseQuery)
            ->where('status', 1)
            ->where('committee_id', (int) $committee->id)
            ->count();

        return response()->json([
            'success' => true,
            'attend_count' => (int) $attendCount,
            'voter_count' => (int) $voterCount,
        ]);
    }
    //==============================================================
    public function search2($voters, $search_value) {
        if ($search_value != '') {
            $searchValue = preg_replace('/\s+/u', ' ', trim((string) $search_value));
            $searchValue = $searchValue === null ? '' : $searchValue;
            if ($searchValue === '') {
                return $voters;
            }

            // Normalized characters mapping
            $normalizedChars = [
                'أ' => ['أ', 'ا', 'إ', 'آ'],
                'ا' => ['أ', 'ا', 'إ', 'آ'],
                'إ' => ['أ', 'ا', 'إ', 'آ'],
                'آ' => ['أ', 'ا', 'إ', 'آ'],
                'ي' => ['ي', 'ى'],
                'ى' => ['ي', 'ى'],
                'ة' => ['ة', 'ه'],
                'ه' => ['ة', 'ه']
            ];

            // Word substitutions
            $substitutions = [
                'نجلاء' => ['نجله'],
                'بداح' => ['ابداح'],
                'ظافر' => ['ضافر'],
                'ندا' => ['نداء'],
                'سارا' => ['سارة'],
                'نورا' => ['نوره']
            ];

            // Extract first name and remaining words
            $keywords = array_values(array_filter(explode(' ', $searchValue), fn ($word) => $word !== ''));
            $firstWord = (string) ($keywords[0] ?? ''); // First name must match exactly when available
            $remainingWords = array_slice($keywords, 1); // Other words

            $hasNormalizedName = Schema::hasColumn('voters', 'normalized_name');
            $normalizedSearch = str_replace(['أ', 'إ', 'آ', 'ى', 'ة'], ['ا', 'ا', 'ا', 'ي', 'ه'], $searchValue);

            $voters->where(function ($query) use ($searchValue, $firstWord, $remainingWords, $normalizedChars, $substitutions, $hasNormalizedName, $normalizedSearch) {
                if ($firstWord !== '') {
                    $query->orWhere(function ($nameQuery) use ($firstWord, $remainingWords, $normalizedChars, $substitutions) {
                        // Generate variations for the first word
                        $firstWordVariations = $this->generateSearchTerms($firstWord, $normalizedChars);

                        // Add word substitutions
                        if (array_key_exists($firstWord, $substitutions)) {
                            $firstWordVariations = array_merge($firstWordVariations, $substitutions[$firstWord]);
                        }

                        // Ensure first name is match at the beginning
                        $nameQuery->where(function ($subQuery) use ($firstWordVariations) {
                            foreach ($firstWordVariations as $variation) {
                                $subQuery->orWhere('name', 'LIKE', "{$variation}%");
                            }
                        });

                        // If there are additional words, ensure they appear somewhere in the name
                        if (!empty($remainingWords)) {
                            foreach ($remainingWords as $word) {
                                $nameQuery->where('name', 'LIKE', "%{$word}%");
                            }
                        }
                    });
                }

                // Broad fallback search to avoid false zero-results from strict first-name matching.
                $query->orWhere('name', 'LIKE', "%{$searchValue}%")
                    ->orWhere('alsndok', 'like', "%{$searchValue}%");

                if ($hasNormalizedName && $normalizedSearch !== '') {
                    $query->orWhere('normalized_name', 'LIKE', '%' . $normalizedSearch . '%');
                }
            });
        }
        return $voters;
    }
    
    /**
     * Generate all possible combinations of a string based on normalized characters
     */
    private function generateSearchTerms($input, $normalizedChars) {
        $combinations = [''];
    
        for ($i = 0; $i < mb_strlen($input, 'UTF-8'); $i++) {
            $char = mb_substr($input, $i, 1, 'UTF-8');
            if (isset($normalizedChars[$char])) {
                $newCombinations = [];
                foreach ($combinations as $combination) {
                    foreach ($normalizedChars[$char] as $replacement) {
                        $newCombinations[] = $combination . $replacement;
                    }
                }
                $combinations = $newCombinations;
            } else {
                foreach ($combinations as &$combination) {
                    $combination .= $char;
                }
            }
        }
    
        return $combinations;
    }
    //==============================================================
    private function resolveCommitteeVoterTypeVariants(string $committeeType): array
    {
        $type = trim($committeeType);
        $womenTypes = ['اناث', 'انثى', 'انثي'];
        $menTypes = ['ذكور', 'ذكر'];

        if (in_array($type, $womenTypes, true)) {
            return $womenTypes;
        }

        if (in_array($type, $menTypes, true)) {
            return $menTypes;
        }

        return collect([$type, Type::normalize($type)])
            ->filter(fn ($value) => is_string($value) && trim($value) !== '')
            ->map(fn ($value) => trim($value))
            ->unique()
            ->values()
            ->all();
    }
    //==============================================================
    private function applyElectionVoterPivotScopeWhenAvailable(Builder $query, int $electionId): void
    {
        if ($electionId <= 0 || !Schema::hasTable('election_voter')) {
            return;
        }

        $hasElectionVoterRows = DB::table('election_voter')
            ->where('election_id', $electionId)
            ->exists();

        if (!$hasElectionVoterRows) {
            // Fallback: avoid zero-results when pivot mapping is not initialized for this election.
            return;
        }

        $query->whereHas('election', function ($electionQuery) use ($electionId) {
            $electionQuery->where('elections.id', $electionId);
        });
    }
    //==============================================================
    private function resolveAttendingScope(?User $user): array
    {
        if (!$user) {
            return [
                'is_admin' => false,
                'committee_ids' => [],
            ];
        }

        if ($user->hasRole('Administrator')) {
            return [
                'is_admin' => true,
                'committee_ids' => [],
            ];
        }

        $representativeCommitteeIds = $user->representatives()
            ->whereNotNull('committee_id')
            ->pluck('committee_id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();

        if ($representativeCommitteeIds->isNotEmpty()) {
            return [
                'is_admin' => false,
                'committee_ids' => $representativeCommitteeIds->all(),
            ];
        }

        $allowedElectionIds = collect();

        $userElectionId = (int) ($user->election_id ?? 0);
        if ($userElectionId > 0) {
            $allowedElectionIds->push($userElectionId);
        }

        $candidateElectionIds = Candidate::withoutGlobalScopes()
            ->where('user_id', (int) $user->id)
            ->whereNotNull('election_id')
            ->pluck('election_id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->values();

        $allowedElectionIds = $allowedElectionIds
            ->merge($candidateElectionIds)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();

        if ($allowedElectionIds->isEmpty()) {
            return [
                'is_admin' => false,
                'committee_ids' => [],
            ];
        }

        $committeeIds = Committee::withoutGlobalScopes()
            ->when(
                Schema::hasColumn('committees', 'election_id'),
                fn ($query) => $query->whereIn('election_id', $allowedElectionIds->all())
            )
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        return [
            'is_admin' => false,
            'committee_ids' => $committeeIds,
        ];
    }
    //==============================================================
}