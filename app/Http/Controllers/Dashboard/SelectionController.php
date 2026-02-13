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

        $filters = collect(['alfkhd', 'alfraa', 'albtn', 'cod1', 'cod2', 'cod3', 'alktaa', 'family_id']);
        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->get($filter));
            }
        }

        if ($request->filled('street')) {
            $street = $request->get('street');
            $query->whereExists(function ($exists) use ($street) {
                $exists->selectRaw('1')
                    ->from('selections')
                    ->where('selections.street', $street)
                    ->whereColumn('selections.alfkhd', 'voters.alfkhd')
                    ->whereColumn('selections.alfraa', 'voters.alfraa')
                    ->whereColumn('selections.albtn', 'voters.albtn')
                    ->whereColumn('selections.alktaa', 'voters.alktaa');
            });
        }

        if ($request->filled('alharaa')) {
            $alharaa = $request->get('alharaa');
            $query->whereExists(function ($exists) use ($alharaa) {
                $exists->selectRaw('1')
                    ->from('selections')
                    ->where('selections.alharaa', $alharaa)
                    ->whereColumn('selections.alfkhd', 'voters.alfkhd')
                    ->whereColumn('selections.alfraa', 'voters.alfraa')
                    ->whereColumn('selections.albtn', 'voters.albtn')
                    ->whereColumn('selections.alktaa', 'voters.alktaa');
            });
        }

        if ($request->filled('home')) {
            $home = $request->get('home');
            $query->whereExists(function ($exists) use ($home) {
                $exists->selectRaw('1')
                    ->from('selections')
                    ->where('selections.home', $home)
                    ->whereColumn('selections.alfkhd', 'voters.alfkhd')
                    ->whereColumn('selections.alfraa', 'voters.alfraa')
                    ->whereColumn('selections.albtn', 'voters.albtn')
                    ->whereColumn('selections.alktaa', 'voters.alktaa');
            });
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
            if ($key !== 'family_id') {
                $selectionIds[$key] = collect($values)
                    ->filter(fn($value) => !is_null($value) && $value !== '')
                    ->unique()
                    ->sort()
                    ->values()
                    ->mapWithKeys(fn($value) => [(string) $value => (string) $value])
                    ->toArray();
            } else {
                $selectionIds[$key] = Family::whereIn('id', $values)
                    ->pluck('name', 'id')
                    ->unique()
                    ->toArray();
            }
        }
        $selectionScope = Selection::query();
        foreach (['alfkhd', 'alfraa', 'albtn', 'cod1', 'cod2', 'cod3', 'alktaa'] as $column) {
            if ($request->filled($column)) {
                $selectionScope->where($column, $request->get($column));
            }
        }

        $locationOptions = [
            'street' => (clone $selectionScope)->whereNotNull('street')->pluck('street', 'street')->toArray(),
            'alharaa' => (clone $selectionScope)->whereNotNull('alharaa')->pluck('alharaa', 'alharaa')->toArray(),
            'home' => (clone $selectionScope)->whereNotNull('home')->pluck('home', 'home')->toArray(),
        ];

        return response()->json([
            'selectionIds' => $selectionIds,
            'locationOptions' => $locationOptions,
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
