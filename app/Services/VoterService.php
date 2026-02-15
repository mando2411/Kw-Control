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

    public function VoterOverAll($votersQuery){
        $data=[
            'MenCount' => (clone $votersQuery)->where('type', Type::MALE->value)
                ->count(),
                'WomenCount' => (clone $votersQuery)->where('type', Type::FEMALE->value)
                ->count(),
                'Count' => (clone $votersQuery)
                ->count(),
            'menHasContractors' => (clone $votersQuery)->where('type', Type::MALE->value)
                ->withCount('contractors')
                ->having('contractors_count', '>', 1)
                ->count(),
            
            'womenHasContractors' => (clone $votersQuery)->where('type', Type::FEMALE->value)
                ->withCount('contractors')
                ->having('contractors_count', '>', 1)
                ->count(),
            
            'AllHasContractors' => (clone $votersQuery)
                ->withCount('contractors')
                ->having('contractors_count', '>', 1)
                ->count(),
            'MenName'=>(clone $votersQuery)
            ->where('type', Type::MALE->value)
            ->withCount('contractors')
            ->orderBy('contractors_count', 'desc')
            ->first()->name ?? '0',

            'WomenName'=>(clone $votersQuery)
            ->where('type', Type::FEMALE->value)
            ->withCount('contractors')
            ->orderBy('contractors_count', 'desc')
            ->first()?->name ?? '0',
        ];
        return $data;
        
    }
}
