<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Role;
use App\Models\User;
use App\Models\School;
use App\Models\Setting;
use App\Models\Election;
use App\Models\Candidate;
use App\Models\Committee;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use App\Services\VoteService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\DataTables\CandidateDataTable;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Dashboard\UserRequest;
use App\Http\Requests\Dashboard\CandidateRequest;

class CandidateController extends Controller
{
    //================================================================================
    use ImageTrait;
    //================================================================================
    public function index(CandidateDataTable $dataTable)
    {
        $elections = Election::all();
        $candidates = Candidate::with([
            'election',
            'user' => function ($query) {
                $query->withCount(['contractors', 'representatives']);
            },
        ])->latest()->get();
        return $dataTable->render('dashboard.candidates.index', compact('elections', 'candidates'));
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

        $relations = [
            'elections' => Election::all(),
            'roles' => Role::all(),
        ];
        return view('dashboard.candidates.create', compact('relations'));
    }


    public function store(CandidateRequest $request, UserRequest $userRequest)
    {
        $user = User::create($userRequest->getSanitized());
        $user->assignRole($request->get('roles'));
        $request['user_id'] = $user->id;
        $candidate = Candidate::create($request->all());
        $committees = $candidate->election->committees->pluck('id')->toArray();
        $candidate->committees()->sync($committees);
        session()->flash('message', 'Candidate Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.candidates.edit', $candidate);
    }


    public function show(Candidate $candidate)
    {
        //
    }

    public function publicProfile($id)
    {
        $candidate = Candidate::withoutGlobalScopes()
            ->with([
                'election',
                'user' => function ($query) {
                    $query->withCount(['contractors', 'representatives']);
                },
            ])
            ->findOrFail($id);

        return view('public.candidates.profile', compact('candidate'));
    }


    public function edit(Candidate $candidate)
    {
        $relations = [
            'elections' => Election::all(),
            'roles' => Role::all(),
        ];
        return view('dashboard.candidates.edit', compact('candidate', 'relations'));
    }


    public function update(CandidateRequest $request, Candidate $candidate, UserRequest $userRequest)
    {
        $user = $candidate->user;
        $user->update($userRequest->getSanitized());
        $user->syncRoles($userRequest->get('roles'));
        $candidate->update($request->getSanitized());
        session()->flash('message', 'Candidate Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Candidate $candidate)
    {
        $candidate->user()->delete();
        $candidate->delete();
        return response()->json([
            'message' => 'Candidate Deleted Successfully!'
        ]);
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
        if (auth()->user()->representatives()->exists()) {
            $request->merge(['committee' => auth()->user()->representatives()->first()->committee->id]);
        }
        $committees = Committee::all();
        $committee = null;
        if (collect($request->all())->isEmpty()) {
            $candidates = null;
        } else {
            $committee = Committee::where('id', $request->committee)->with('candidates.user')->first();
            $candidates = $committee->candidates()
                ->withoutGlobalScopes()
                ->join('users', 'candidates.user_id', '=', 'users.id')
                ->where('candidates.election_id', auth()->user()->election_id) // Explicit qualification
                ->orderBy('users.name') // Order by users' names
                ->select('candidates.*', 'users.name as user_name') // Select the necessary fields
                ->get()
                ->map(function ($candidate) {
                    return [
                        'id' => $candidate->id,
                        'name' => $candidate->user_name, // Use the aliased name
                        'user_id' => $candidate->user_id,
                        'votes' => $candidate->pivot->votes,
                        'committee' => $candidate->pivot->committee_id,
                    ];
                });
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
                //=======================================================================================
                $candidate_for_result = Setting::where('option_key', 'result_control_candidate')->first();
                if ($candidate_for_result && $candidate_for_result->option_value != NULL) {
                    // dd($candidate_for_result->option_value[0]);
                    $election_id    = $this->fetchElectionFromCandidateId($candidate_for_result->option_value[0]);
                } else {
                    $election_id    = $this->fetchElectionFromCandidate($candidate_name);
                }
                // dd($election_id);
                //=======================================================================================
                $candidates     = $this->fetchCondidatesBasedOnElection($election_id, $candidate_name);
                $committees     = Committee::all();
                $schools        = School::orderBy('id', 'desc')->get();
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
            $user->assignRole(3); //3 is the id of role "مرشح"
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

}
