<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Committee;
use App\Models\Contractor;
use App\Models\Election;
use App\Models\Family;
use App\Models\Selection;
use App\Models\Voter;
use Illuminate\Http\Request;

class SelectionController extends Controller
{
    public function filter(Request $request)
    {
        // Step 1: Initialize the query on the Voter model
        $query = Voter::query();

        $filters = collect(['alfkhd', 'alfraa', 'albtn', 'cod1', 'cod2', 'cod3','family_id']); // Convert to a collection
        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->get($filter));
            }
        }

        $voters = $query->get();

        if ($voters->isEmpty()) {
            $voters = Voter::query()->get();
        }

        $selectionData = $filters->mapWithKeys(function ($filter) use ($voters) {
            return [
                $filter => $voters->pluck($filter)->filter()->unique()->values()->toArray(),
            ];
        });
        $selectionIds = [];
        foreach ($selectionData as $key => $values) {
            if($key != "family_id"){
                $selectionIds[$key] = Selection::whereIn($key, $values)
                    ->pluck($key, 'id')
                    ->unique()
                    ->toArray();
            }else{
                $selectionIds[$key] = Family::whereIn('id', $values)
                    ->pluck('name', 'id')
                    ->unique()
                    ->toArray();
            }
        }
        return response()->json([
            'selectionIds' => $selectionIds,
        ]);
    }

    public function reportFilter(Request $request)
    {
        $filters = ['contractor', 'candidate','committee'];
        $electionId = $request->election;

        $selectionData = collect($filters)->mapWithKeys(function ($filter) use ($electionId, $request) {

            if ($filter === 'contractor') {
                // If a candidate is selected, filter contractors by the candidate's creator_id
                if ($request->filled('candidate')) {
                    $candidateId = $request->candidate;
                    $candidate = Candidate::find($candidateId);

                    if ($candidate) {
                        return [
                            $filter => Contractor::where('creator_id', $candidate->user_id)
                                ->pluck('name', 'id')
                                ->unique()
                                ->toArray()
                        ];
                    }
                }

                // Otherwise, filter contractors by election_id
                return [
                    $filter => Contractor::where('election_id', $electionId)
                        ->pluck('name', 'id')
                        ->unique()
                        ->toArray()
                ];
            }
            if ($filter === 'committee') {

                // Otherwise, filter contractors by election_id
                return [
                    $filter => Committee::where('election_id', $electionId)
                        ->pluck('name', 'id')
                        ->unique()
                        ->toArray()
                ];
            }

            if ($filter === 'candidate') {
                // If a contractor is selected, filter candidates by the contractor's creator_id
                if ($request->filled('contractor')) {
                    $contractorId = $request->contractor;
                    $contractor = Contractor::find($contractorId);

                    if ($contractor) {
                        return [
                            $filter => Candidate::where('user_id', $contractor->creator_id)
                                ->get()
                                ->pluck('user.name', 'id') // Pluck name from related user table
                                ->unique()
                                ->toArray()
                        ];
                    }
                }

                // Otherwise, filter candidates by election_id
                return [
                    $filter => Candidate::where('election_id', $electionId)
                        ->get()
                        ->pluck('user.name', 'id') // Pluck name from related user table
                        ->unique()
                        ->toArray()
                ];
            }

            return [];
        });
        return response()->json($selectionData);
    }


}
