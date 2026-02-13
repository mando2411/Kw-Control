<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\VotersExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Family;
use App\Models\Committee;
use App\Models\Contractor;
use App\Models\Voter;
use App\Models\Selection;
use App\Services\VoterService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use ArPHP\Arabic;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;




class StatementController extends Controller
{
    public function index(Request $request){
        if ($request->collect()->isNotEmpty()) {
            $family = Family::query();
            if (!empty($request->family)) {
                $family->where('name', 'LIKE', '%' . $request->family . '%');
            }
            $family = $family->get();
        } else {
            $family = Family::with('voters')->get();
        }
        $families = $family->map(function ($family) {
            $menCount = $family->voters->where('type', 'ذكر')->count();
            $womenCount = $family->voters->where('type', '!=', 'ذكر')->count();
            return [
                'id' => $family->id,
                'name' => $family->name,
                'men' => $menCount,
                'women' => $womenCount,
                'total' => $menCount + $womenCount
            ];
        })->sortByDesc('total')->values();

        $relations=[
            'families' =>$families
        ];
        $voters=Voter::all();
        return view('dashboard.statements.index',compact('relations','voters') );
       }

       public function search(Request $request){
        if(auth()->user()->hasRole("Administrator")){
            $contractors=Contractor::Children()->get();
        }else{
            $contractors = auth()->user()->contractors()->Children()->get();
        }
        $relations=[
            'families'=>Family::all(),
            'committees' =>Committee::all(),
            'contractors' =>$contractors,
        ];
        $filters = collect(['alfkhd', 'alfraa', 'albtn', 'cod1', 'cod2', 'cod3','alktaa']); // Convert to a collection
        $voters=Voter::whereHas('election',function($q){
            $q->where('election_id',auth()->user()->election_id);
        });
        $selectionData = $filters->mapWithKeys(function ($filter) use ($voters) {
            return [
                $filter => $voters->pluck($filter)->filter()->unique()->values()->toArray(),
            ];
        });
        $cod1 = Selection::select('cod1')
        ->whereNotNull('cod1')
        ->distinct()
        ->get();


        $voters=null;
        return view('dashboard.statements.search' , compact('relations','voters','selectionData'));

       }

       public function query(Request $request)
       {
        if(auth()->user()->hasRole("Administrator")){
            $contractors=Contractor::Children()->get();
        }else{
            $contractors = auth()->user()->contractors()->Children()->get();
        }

        $effectiveFilters = collect($request->all())
            ->except(['page', '_token'])
            ->filter(function ($value, $key) {
                if (is_array($value)) {
                    return collect($value)->filter(function ($nested) {
                        return !is_null($nested) && $nested !== '';
                    })->isNotEmpty();
                }

                if ($key === 'type' && $value === 'all') {
                    return false;
                }

                return !is_null($value) && $value !== '';
            });

        if ($effectiveFilters->isEmpty()) {
            $voters = collect();
            session()->flash('message', 'لم يتم ادخال اي بيانات للبحث');
            session()->flash('type', 'danger');
        } else {
            $voters = Voter::Filter();
        }


        activity()
            ->causedBy(auth()->user())
            ->performedOn(new Voter())
            ->withProperties(['search_data' => $request->all()])
            ->event('Search')
            ->log('بحث عن ناخبين');

           return response()->json([
               'voters' => collect($voters)->values(),
               'message' => 'Search completed successfully',
           ]);
       }

       public function voterDetails(Voter $voter): JsonResponse
       {
           $voter->load(['family', 'committee.school', 'attend', 'contractors.user']);

           return response()->json([
               'voter' => $voter,
               'family' => optional($voter->family)->name,
               'committee' => optional($voter->committee)->name,
               'school' => optional(optional($voter->committee)->school)->name,
               'attend' => $voter->attend,
               'contractors' => $voter->contractors,
           ]);
       }
public function stat(Request $request)
{
    $Voters = Voter::query();
    $trustVoters = Voter::whereHas('contractors');

    if ($request->filled('committee')) {
        $committeeId = $request->input('committee');
        $Voters->where('committee_id', $committeeId);
        $trustVoters->where('committee_id', $committeeId);
    }

    if ($request->filled('family')) {
        $familyId = $request->input('family');
        $Voters->where('family_id', $familyId);
        $trustVoters->where('family_id', $familyId);
    }


    $allVoters = $this->calculateStatistics($Voters);

    $allTrustVoters = $this->calculateStatistics($trustVoters);

    $data = [
        'allVoters' => $allVoters,
        'allTrustVoters' => $allTrustVoters,
    ];
    $relations=[
        'families'=>Family::all(),
        'committees'=>Committee::all()
    ];

    return view('dashboard.statements.statistics', compact('data','relations'));
}

private function calculateStatistics($query)
{
    $statistics = $query->selectRaw("
    COUNT(*) as total_voters,
    SUM(CASE WHEN type = 'ذكر' THEN 1 ELSE 0 END) as male_voters,
    SUM(CASE WHEN type != 'ذكر' THEN 1 ELSE 0 END) as female_voters,
    SUM(CASE WHEN type = 'ذكر' AND status = 1 THEN 1 ELSE 0 END) as male_voters_present,
    SUM(CASE WHEN type != 'ذكر' AND status = 1 THEN 1 ELSE 0 END) as female_voters_present,
    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as total_present,
    SUM(CASE WHEN type = 'ذكر' AND status = 0 THEN 1 ELSE 0 END) as male_voters_absent,
    SUM(CASE WHEN type != 'ذكر' AND status = 0 THEN 1 ELSE 0 END) as female_voters_absent,
    SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as total_absent
")->first();

    $statistics->male_attendance_percentage = ($statistics->male_voters > 0) ? ($statistics->male_voters_present / $statistics->male_voters) * 100 : 0;
    $statistics->female_attendance_percentage = ($statistics->female_voters > 0) ? ($statistics->female_voters_present / $statistics->female_voters) * 100 : 0;
    $statistics->overall_attendance_percentage = ($statistics->total_voters > 0) ? ($statistics->total_present / $statistics->total_voters) * 100 : 0;

    return $statistics;


}

public function show(Request $request, VoterService $voterService)
{
    $user = auth()->user();

    $parents = $voterService->getParents();
    $children = $voterService->getChildren($user);

   $voterIds = $request->get('voters', []);

   if (!empty($voterIds)) {
       $voters = Voter::whereIn('id', $voterIds)
           ->withCount('contractors')
           ->orderBy('contractors_count', 'desc')
           ->with('contractors')->get();
   } else {
       $voters = $voterService->getVotersQuery($user, $request)->with('contractors')->get();
   }
    return view('dashboard.statements.show', compact('voters', 'parents', 'children'));
}

public function export(Request $request)
{
    if($request->family_id){
        $voters=Voter::where('family_id',request('family_id'))
        ->get();
    }elseif($request->voter){
        $voters_ids= $request->input('voter');
        $voters = Voter::whereIn('id', $voters_ids)->get();
    }elseif($request->school_id){
        $school=School::find(request('school_id'));
        $voters=$school->voters()->get();
    }else{
        session()->flash('message', 'لايوجد اي ناخبين');
        session()->flash('type', 'danger');
        return redirect()->back();


    }
    if ($request->type == "Excel") {
        return Excel::download(new VotersExport($voters, request('columns')), 'Voters.xlsx');
    } elseif ($request->type == "PDF" || $request->type == "Send") {
        $token = bin2hex(random_bytes(16));
        $VoterFile = $token . ".pdf";
        $VoterPath = public_path("Pdf/" . $VoterFile);
        ini_set('max_execution_time', 300); // 5 minutes
        ini_set('memory_limit', '512M');
        ini_set('max_input_vars', 5000);


        $html = view('dashboard.exports.pdf', ['voters' => $voters, 'mode' => 'pdf', 'columns' => request("columns")])->toArabicHTML();
        $pdf = Pdf::loadHTML($html)
            // ->setPaper('a4', 'landscape')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);
            $pdf->setPaper([0, 0, 841.89, 1190.55], 'portrait');

        if ($request->type == "Send") {
            $pdf->save($VoterPath);
            $phoneNumber = request('to') ?? "55150551"; // Replace with your phone number in international format
            $message = env("APP_URL") . "/Pdf/" . $VoterFile;
            $encodedMessage = urlencode($message);
            $whatsappUrl = "https://wa.me/965" . $phoneNumber . "?text=" . $encodedMessage;
            return response()->json(
                [
                    'Redirect_Url'=>$whatsappUrl
                ]
            );
        } else {
            return response()->streamDownload(
                fn() => print($pdf->output()),
                "Voters.pdf",
                ["Content-type" => "application/pdf"]
            );
        }


    } else {
        $columns = request('columns');
        return view('dashboard.exports.pdf', compact('voters', 'columns'));
    }
    return redirect()->back();
}
public function loadMore(Request $request, VoterService $voterService)
{
    $user = auth()->user();

    // Get voters for the next page
    $voters = $voterService->getVotersQuery($user, $request)->paginate(50);

    $currentPage = $voters->currentPage();
    $perPage = $voters->perPage();
    $html = view('dashboard.statements.component.voters-list',compact('voters', 'currentPage', 'perPage'))->render();

    return response()->json([
        'html' => $html,
        'hasMorePages' => $voters->hasMorePages(),
    ]);
}

public function voters_attends(Request $request){
    $voters=Voter::paginate(50);

    if ($request->ajax()) {
        $view = view('dashboard.statements.component.table', compact('voters'))->render();  // Load only rows
        return response()->json([
            'html' => $view,
            'hasMorePages' => $voters->hasMorePages(),
            'nextPageUrl' => $voters->nextPageUrl(),  // Ensure this is properly returned
        ]);
    }
    return view('dashboard.committees.voter-list',compact('voters'));
}

}
