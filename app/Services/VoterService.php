<?php
namespace App\Services;

use App\Models\Voter;
use App\Models\Contractor;
use App\Enums\Type;

class VoterService
{
    public function getVotersQuery($user, $request)
    {
        $query = Voter::query();

        if ($user->hasRole("Administrator")) {
            $query->whereHas('contractors');
        } else {
            $contractors = $user->contractors()->children()->with('voters:id')->get();
            $voterIds = $contractors->pluck('voters.*.id')->flatten()->unique();
            $query->whereIn('id', $voterIds);
        }

        if ($request->collect()->isNotEmpty()) {
            if ($request->madameeen) {
                $query->where('name', 'LIKE', '%' . $request->madameeen . '%');
            }
            if ($request->mota3hdeeen) {
                $contractors = Contractor::whereHas('user', function($subQuery) use ($request) {
                    $subQuery->where('name', 'LIKE', '%' . $request->mota3hdeeen . '%');
                })->pluck('id');

                if ($contractors) {
                    $query->whereHas('contractors', function($subQuery) use ($contractors) {
                        $subQuery->whereIn('contractor_id', $contractors);
                    });
                }
            }
        }

        return $query->withCount('contractors')->orderBy('contractors_count', 'desc');
    }

    public function getParents()
    {
        return Contractor::parents()
            ->with('user:id,name')
            ->get()
            ->map(fn($contractor) => [
                'id' => $contractor->id,
                'name' => $contractor->user?->name ?? $contractor->name
            ]);
    }

    public function getChildren($user)
    {
        if ($user->hasRole("Administrator")) {
            return Contractor::children()->get();
        }
        return $user->contractors()->children()->get();
    }

    public function VoterOverAll($votersQuery, ?array $scopedContractorIds = null){
        $withScopedContractorsCount = function ($query) use ($scopedContractorIds) {
            if (!is_array($scopedContractorIds) || empty($scopedContractorIds)) {
                return $query->withCount('contractors');
            }

            return $query->withCount([
                'contractors as contractors_count' => function ($contractorsQuery) use ($scopedContractorIds) {
                    $contractorsQuery->whereIn('contractors.id', $scopedContractorIds);
                }
            ]);
        };

        $duplicateOccurrences = function ($query) use ($withScopedContractorsCount) {
            return $withScopedContractorsCount($query)
                ->get()
                ->sum(function ($voter) {
                    return max(((int) $voter->contractors_count) - 1, 0);
                });
        };

        $data=[
            'MenCount' => (clone $votersQuery)->where('type', Type::MALE->value)
                ->count(),
                'WomenCount' => (clone $votersQuery)->where('type', Type::FEMALE->value)
                ->count(),
                'Count' => (clone $votersQuery)
                ->count(),
            'menHasContractors' => $duplicateOccurrences((clone $votersQuery)->where('type', Type::MALE->value)),
            
            'womenHasContractors' => $duplicateOccurrences((clone $votersQuery)->where('type', Type::FEMALE->value)),
            
            'AllHasContractors' => $duplicateOccurrences((clone $votersQuery)),
            'MenName'=>$withScopedContractorsCount((clone $votersQuery)
            ->where('type', Type::MALE->value))
            ->orderBy('contractors_count', 'desc')
            ->first()->name ?? '0',

            'WomenName'=>$withScopedContractorsCount((clone $votersQuery)
            ->where('type', Type::FEMALE->value))
            ->orderBy('contractors_count', 'desc')
            ->first()?->name ?? '0',
        ];
        return $data;
        
    }
}
