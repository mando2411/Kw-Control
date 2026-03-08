<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Committee;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CommitteeRequest;
use App\DataTables\CommitteeDataTable;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\School;
use App\Models\Voter;
use App\Services\Query\CommitteeGenerator;
use App\Events\CommitteeUpdate;


class CommitteeController extends Controller
{
    protected $generator;

    public function __construct(CommitteeGenerator $generator)
    {
        $this->generator = $generator;
    }

    public function index(CommitteeDataTable $dataTable)
    {
        return $dataTable->render('dashboard.committees.index');
    }


    public function create()
    {
        $schoolsQuery = School::query()->select(['id', 'name', 'type']);
        if (Schema::hasColumn('schools', 'election_id')) {
            $schoolsQuery->addSelect('election_id');
        } else {
            $schoolsQuery->selectRaw('NULL as election_id');
        }

        $relations = [
            'elections' => Election::query()->select('id', 'name')->orderBy('name')->get(),
            'schools' => $schoolsQuery->orderBy('name')->get(),
        ];
        return view('dashboard.committees.create', compact('relations'));
    }


    public function store(CommitteeRequest $request)
    {
        $committee = Committee::create($request->getSanitized());
        session()->flash('message', 'Committee Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.committees.edit', $committee);
    }


    public function show(Committee $committee)
    {
        //
    }


    public function edit(Committee $committee)
    {
        $schoolsQuery = School::query()->select(['id', 'name', 'type']);
        if (Schema::hasColumn('schools', 'election_id')) {
            $schoolsQuery->addSelect('election_id');
        } else {
            $schoolsQuery->selectRaw('NULL as election_id');
        }

        $relations = [
            'elections' => Election::query()->select('id', 'name')->orderBy('name')->get(),
            'schools' => $schoolsQuery->orderBy('name')->get(),
        ];
        return view('dashboard.committees.edit', compact('committee', 'relations'));
    }


    public function update(CommitteeRequest $request, Committee $committee)
    {
        $committee->update($request->getSanitized());
        session()->flash('message', 'Committee Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }

    public function generate(){

        $relations = [
            'elections' => Election::query()->select('id', 'name')->orderBy('name')->get(),
        ];

        return view('dashboard.committees.multi', compact('relations'));
    }

    public function multi(Request $request)
    {
        $validateData = $request->validate([
            'men' => ['required', 'integer', 'min:1'],
            'women' => ['required', 'integer', 'min:1'],
            'election_id' => ['required', 'integer', 'exists:elections,id'],
        ], [
            'men.required' => 'يرجى إدخال عدد لجان الذكور.',
            'men.integer' => 'عدد لجان الذكور يجب أن يكون رقمًا صحيحًا.',
            'men.min' => 'عدد لجان الذكور يجب أن يكون 1 على الأقل.',
            'women.required' => 'يرجى إدخال عدد لجان الإناث.',
            'women.integer' => 'عدد لجان الإناث يجب أن يكون رقمًا صحيحًا.',
            'women.min' => 'عدد لجان الإناث يجب أن يكون 1 على الأقل.',
            'election_id.required' => 'يرجى اختيار الحملة الانتخابية.',
            'election_id.integer' => 'قيمة الحملة الانتخابية غير صحيحة.',
            'election_id.exists' => 'الحملة الانتخابية المختارة غير موجودة.',
        ]);

        // Generate Multi Committees Using Enums
        $message = $this->generator->createRecords($validateData);

        session()->flash('message', $message);
        session()->flash('type', 'success');
        return redirect()->back();
    }

    public function destroy(Committee $committee)
    {
        $committee->delete();
        return response()->json([
            'message' => 'Committee Deleted Successfully!'
        ]);
    }
    public function home(Request $request)
    {
        $user = auth('web')->user();
        $schoolId = (string) $request->input('id', 'all');
        $hasSchoolElectionColumn = Schema::hasColumn('schools', 'election_id');

        $dropdownQuery = School::query()->select('id', 'name', 'type');
        $schoolsQuery = School::query()->with([
            'committees' => function ($query) {
                $query->with(['voters', 'representatives.user']);
            },
        ]);

        if ($user && !$user->hasRole('Administrator')) {
            $electionId = (int) $user->election_id;

            if ($hasSchoolElectionColumn) {
                $dropdownQuery->where(function ($query) use ($electionId) {
                    $query->where('election_id', $electionId)
                        ->orWhereNull('election_id');
                });

                $schoolsQuery->where(function ($query) use ($electionId) {
                    $query->where('election_id', $electionId)
                        ->orWhereNull('election_id');
                });
            }

            $dropdownQuery->whereHas('committees');
        }

        $relations = [
            'schools' => $dropdownQuery->orderBy('name')->get(),
        ];

        if ($schoolId !== 'all') {
            $schoolsQuery->where('id', (int) $schoolId);
        }

        $schools = $schoolsQuery->get();
        $committeeIds = $schools->pluck('committees.*.id')->flatten()->unique()->values();

        $summary = [
            'male_voters' => 0,
            'female_voters' => 0,
            'total_voters' => 0,
            'male_attended' => 0,
            'female_attended' => 0,
            'total_attended' => 0,
            'male_remaining' => 0,
            'female_remaining' => 0,
            'total_remaining' => 0,
        ];

        if ($committeeIds->isNotEmpty()) {
            $baseVoters = Voter::query()->whereIn('committee_id', $committeeIds->all());

            $summary['male_voters'] = (clone $baseVoters)->where('type', 'ذكر')->count();
            $summary['female_voters'] = (clone $baseVoters)->where('type', '!=', 'ذكر')->count();
            $summary['total_voters'] = (clone $baseVoters)->count();

            $summary['male_attended'] = (clone $baseVoters)->where('type', 'ذكر')->where('status', 1)->count();
            $summary['female_attended'] = (clone $baseVoters)->where('type', '!=', 'ذكر')->where('status', 1)->count();
            $summary['total_attended'] = (clone $baseVoters)->where('status', 1)->count();

            $summary['male_remaining'] = (clone $baseVoters)->where('type', 'ذكر')->where('status', 0)->count();
            $summary['female_remaining'] = (clone $baseVoters)->where('type', '!=', 'ذكر')->where('status', 0)->count();
            $summary['total_remaining'] = (clone $baseVoters)->where('status', 0)->count();
        }

        return view('dashboard.committees.committee', compact('relations', 'schools', 'summary', 'request'));
    }

    public function status(Request $request,$id){
        $user = auth('web')->user();
        if (!$user) {
            return response()->json([
                'error' => 'يجب تسجيل الدخول أولا.'
            ], 403);
        }

        // Keep backward compatibility and allow existing sorting operators to use the toggle.
        $canUpdateStatus = $user->can('committees.update-status')
            || $user->can('committees.edit')
            || $user->can('sorting');

        if (!$canUpdateStatus) {
            return response()->json([
                'error' => 'ليس لديك صلاحية تعديل حالة الفرز.'
            ], 403);
        }

        $request->validate([
            'status' => ['required', 'boolean'],
        ]);

        $committee=Committee::find($id);
        if(!$committee){
            return response()->json([
                'error' => 'اللجنة غير موجودة.'
            ], 404);
        }

        $committee->update([
            'status'=>(bool) $request->status
        ]);
        event(new CommitteeUpdate($committee));

        return  response()->json(
            [
                'status'=>(bool) $committee->status,
                'message' => 'تم تحديث حالة الفرز بنجاح.'
            ]
        );
    }
}
