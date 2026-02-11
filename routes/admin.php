<?php
use App\Http\Controllers\Dashboard\AutoTranslationController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;use App\Http\Controllers\Dashboard\ElectionController;
use App\Http\Controllers\Dashboard\ContractorController;
use App\Http\Controllers\Dashboard\RepresentativeController;
use App\Http\Controllers\Dashboard\CandidateController;
use App\Http\Controllers\Dashboard\VoterController;
use App\Http\Controllers\Dashboard\CommitteeController;
use App\Http\Controllers\Dashboard\StatementController;
use App\Http\Controllers\Dashboard\SchoolController;
use App\Http\Controllers\Dashboard\GeneralController;
use App\Http\Controllers\Dashboard\FamilyController;
use App\Http\Controllers\Dashboard\HistoryController;
use App\Models\Group;
use App\Models\User;
use App\Models\Voter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Twilio\Rest\Client;
use App\Http\Controllers\Dashboard\ReportController;
use App\Exports\VotersExport;
//controllers
Route::group(['prefix' => 'dashboard',
    'middleware' => ['auth:web', 'permitted'],
    'as' => 'dashboard.'],
    function () {
        Route::post('translate'  , [AutoTranslationController::class, 'translate'])->name('model.auto.translate');
        Route::get('toggle-theme', [ProfileController::class, 'toggleTheme'])->name('toggle-theme');
        Route::resource('users', UserController::class)->except('show');
        Route::resource('roles', RoleController::class)->except('show');
        Route::post('multi',[CommitteeController::class,'multi'])->name('committees.multi');
        Route::get('committee/multi',[CommitteeController::class,'generate'])->name('committees.generate');
        Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
            Route::get('show', [SettingController::class, 'show'])->name('show');
            Route::put('update', [SettingController::class, 'update'])->name('update');
            Route::get('result', [SettingController::class, 'resultControl'])->name('result');
        });
    Route::resource('elections', ElectionController::class)->except('show');
    Route::resource('contractors', ContractorController::class)->except('show');
    Route::post('contractor/main', [ContractorController::class,'contractor'])->name('con-main');
    Route::resource('representatives', RepresentativeController::class)->except('show');
    Route::resource('candidates', CandidateController::class)->except('show');
    //========================================================================================================================================
    // fake candidates routes
    Route::post('store/fake/candidates', [CandidateController::class,'storeFakeCandidate'])->name('store-fake-candidates');
	//========================================================================================================================================
    Route::resource('voters', VoterController::class)->except('show');
    Route::post('candidates/{id}/votes/change', [CandidateController::class, 'changeVotes'])->name('candidates.changeVotes');
    Route::post('candidates/{id}/votes/set', [CandidateController::class, 'setVotes'])->name('candidates.setVotes');
    Route::post('voters/{id}/status/update', [VoterController::class, 'updateStatus'])->name('voters.change-status');
		
		
	//========================================================================================================================================
    //import voters for contractor routes
    Route::get('import/contractor/voters', [VoterController::class,'importVotersFotContractorForm'])->name('import-contractor-voters-form');
    Route::post('import/contractor/voters', [VoterController::class,'importVotersFotContractor'])->name('import-contractor-voters');
    //========================================================================================================================================
		
    Route::post('rep/{id}/update', [RepresentativeController::class, 'changeRep'])->name('rep.change');
    Route::resource('committees', CommitteeController::class)->except('show');
    Route::get('sorting',[CandidateController::class,'sorting'])->name('sorting');
    Route::get('attending',[RepresentativeController::class,'attending'])->name('attending');
    Route::get('rep-home',[RepresentativeController::class,'home'])->name('rep-home');
    Route::get('results',[CandidateController::class,'result'])->name('results');
    Route::post('school-rep',[RepresentativeController::class,'search'])->name('school-rep');
    Route::post('user-edit',[UserController::class,'change'])->name('user-change');
    Route::post('mot-up/{id}',[ContractorController::class,'change'])->name('mot-up');
    Route::get('statement',[StatementController::class,'index'])->name('statement');
    Route::get('Statistics',[StatementController::class,'stat'])->name('statistics');
    Route::get('cards/{user?}',[UserController::class,'cards'])->name('cards');
    Route::get('statement/search',[StatementController::class,'search'])->name('statement.search');
    Route::get('statement/query',[StatementController::class,'query'])->name('statement.query');
    Route::get('statement/show',[StatementController::class,'show'])->name('statement.show');
    Route::get('committee/home',[CommitteeController::class,'home'])->name('committee.home');
    Route::resource('schools', SchoolController::class)->except('show');
    Route::resource('families', FamilyController::class)->except('show');
    Route::post('import/voters', [VoterController::class,'import'])->name('import-voters');
    Route::get('madameen', [VoterController::class,'madameen'])->name('madameen');
    Route::get('history', [HistoryController::class,'history'])->name('history');
    Route::get('delete', [HistoryController::class,'delete'])->name('delete');
    Route::resource('reports', ReportController::class)->except('show');

    //RoutePlace
});
Route::post('/keep-alive', [UserController::class, 'keepAlive'])->middleware('auth');
Route::post('committee/status/{id}',[CommitteeController::class,'status'])->name('committee.status');
Route::post('ass/{id}',[ContractorController::class,'ass'])->name('ass');
Route::post('delete/mad',[ContractorController::class,'delete_mad'])->name('delete_mad');
Route::get('contract/{token}/profile',[ContractorController::class,'profile'])->name('con-profile');
Route::post('rep/{user}', [UserController::class, 'passUpdate'])->name('rep-user');
Route::get('change-password', [UserController::class,'changePassword'])->name('change-password');
Route::get('con/{id}', function ($id) {
    $con=App\Models\Contractor::where('id',$id)->first();
    $logs = Activity::where('causer_id', $con->id)->get();
    $user=[
        "id"=>$con->id,
        "status"=>$con->status,
        "name"=>$con->name,
        "phone"=>$con->phone,
        "parent"=>$con->parent_id,
        "note"=>$con->note,
        "status"=>$con->status,
        "trust"=>$con->trust,
        "token"=>$con->token,
        "voters"=>$con->voters,
        "softDelete"=>$con->softDelete,
        "logs"=>$logs,
        "creator"=>$con->creator->name ?? ""
    ];
    return response()->json([
        "user"=>$user
    ]);
});
Route::get('user/{id}', function ($id) {
    $i=App\Models\User::where('id',$id)->first();
    $user=[
        "name"=>$i->name,
        "phone"=>$i->phone,
        "id"=>$i->id
    ];
    return response()->json([
        "user"=>$user
    ]);
});
Route::post('group-e/{id}', function (Request $request,$id) {
    $group=Group::find($id);
    $group->update($request->all());
    session()->flash('message', 'تم التعديل بنجاح');
    session()->flash('type', 'success');
    return redirect()->back();
});
Route::get('group-d/{id}', function ($id) {
    $group=Group::find($id);
    $group->delete();
    session()->flash('message', 'تم الحذف بنجاح');
    session()->flash('type', 'success');
    return redirect()->back();
});
Route::get('group/{id}', function ($id) {
    $group=Group::find($id);
    return response()->json([
        "group"=>$group
    ]);
});
Route::get("voter/{id}/{con_id}", function($id,$con_id){
    $voter =Voter::findOrFail($id);
    $percent=$voter->contractors()->where('contractor_id',$con_id)->first()->pivot->percentage;
    // dd($percent);
    return response()->json([
        "voter"=>$voter,
        "committee_name"=>$voter->committee ? $voter->committee->name : null  ,
        "school"=> $voter->committee ? ($voter->committee->school ? $voter->committee->school->name : null) : null ,
        "percent" => $percent,
        'attend'=>$voter->attend
    ]);
});
Route::get("voter/{id}", function($id){
    $voter =Voter::findOrFail($id);
    return response()->json([
        "voter"=>$voter,
        "family"=>$voter->family ? $voter->family->name : "لايوجد",
        "committee"=>$voter->committee ? $voter->committee->name : "لايوجد",
        "school"=>$voter->school ? $voter->school->name : "لايوجد",
        "contractors"=>$voter->contractors() ? $voter->contractors()->get() : "لايوجد",
        'attend'=>$voter->attend
    ]);
});
Route::get("percent/{id}/{con_id}/{val}", function($id,$con_id,$val){
    $voter =Voter::findOrFail($id);
    $voter_pivot=$voter->contractors()->where('contractor_id',$con_id)->first()->pivot;
    $voter_pivot->percentage=$val;
    $voter_pivot->save();
    return response()->json(["message"=>"success"]);
});
Route::get('send-wa', function() {
    $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
    $from = env('TWILIO_WHATSAPP_FROM'); // This should be whatsapp:+14155238886
    try {
        $message = $twilio->messages->create(
            "whatsapp:+201096491646",
            [
                "from" => "whatsapp:+14155238886",
                "body" => "3ayz agazaaaaaaaaaaaa"            ]
        );
        return response()->json(['status' => 'Message sent successfully', 'message' => $message->sid]);
    } catch (\Exception $e) {
        // Log and handle the error
        return response()->json(['status' => 'Error', 'message' => $e->getMessage()]);
    }
});
Route::get("/down" ,function(){
    $html = view('invoice')->toArabicHTML();
$pdf = Pdf::loadHTML($html)->output();
$headers = array(
    "Content-type" => "application/pdf",
);
// Create a stream response as a file download
return response()->streamDownload(
    fn () => print($pdf), // add the content to the stream
    "invoice.pdf", // the name of the file/stream
    $headers
);
});
Route::get('search', [ContractorController::class,'search'])->name('search');
Route::post('group', [ContractorController::class,'group'])->name('group');
Route::post('voter/change', [ContractorController::class,'modify'])->name('modify');
Route::post('voter/change/group', [ContractorController::class,'modify_g'])->name('modify_g');
Route::get('voters/export', [StatementController::class, 'export'])->name('export');
Route::get('/get-users', [UserController::class, 'getUsers']);
Route::get('/users/online', function (Request $request) {
    $users = User::where('creator_id', Auth::user()->id)->paginate(10);
    $users->getCollection()->transform(function ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'is_online' => $user->isOnline(),
            'is_offline' => $user->isOffline(),
            'last_active_at' => $user->LoginTime($user->last_active_at),
        ];
    });
    // Return paginated response in JSON format
    return response()->json($users);
});
Route::get('/voters/load-more', [StatementController::class, 'loadMore'])->name('voters.load-more');
Route::get('/voters/attended', [StatementController::class, 'voters_attends'])->name('voters.voters-attends');
Route::get('/report', function (Request $request) {
    // Step 1: Retrieve voters with contractors
    $votersWithContractors = Voter::whereHas('election',function($q) {
        return $q->where('election_id',auth()->user()->election_id) ;
    })->where('status',1)->get();

    // Step 2: Extract voter IDs and remove duplicates
    $voterIds = $votersWithContractors->pluck('id')->unique()->toArray();
    // Step 3: Get the voters based on IDs
    $voters = Voter::whereIn('id', $voterIds)->get();
    // Step 4: Handle export type (default to PDF)
    $columns = [
        'elmadany'    
    ];
        // Export to Excel
        return Excel::download(new VotersExport($voters, $columns), 'Voters.xlsx');
    
})->name('attend-report');
Route::get('/filter-selections', [\App\Http\Controllers\Dashboard\SelectionController::class, 'filter'])->name('filter.selections');
Route::get('/report-selections', [\App\Http\Controllers\Dashboard\SelectionController::class, 'reportFilter'])->name('report.selections');
Route::post('/save-file-path', function (Request $request) {
    // Validate the incoming request
    $request->validate([
        'file_path' => 'required|string',
    ]);
    return response()->json([
        'message' => 'File path saved successfully!',
        'report' => $request->file_path,
    ]);
})->name('file.store');
Route::get('elec/set',[SettingController::class,'elec'])->name('elec');
Route::POST('elec/up',[SettingController::class,'elecUp'])->name('elecUp');

//========================================================================================================================================
Route::get('user/contractors/{user_id}', [GeneralController::class,'fetchContractorsForUser'])->name('get-contractors');
Route::get('subcontractors/{main_id}', [GeneralController::class,'fetchSubContractorsForMain'])->name('get-subcontractors');
Route::get('all/results',[CandidateController::class,'allResult'])->name('all.results');
Route::get('voters/{committee_id}', [GeneralController::class,'fetchVotersForCommittee'])->name('get-voters');
Route::get('get_attending_counts/{committee_id}', [GeneralController::class,'fetchAttendingCountForCommittee'])->name('get_attending_counts');
Route::post('candidates/setVotes', [CandidateController::class,'changeVotes'])->name('candidates.setVotes');
Route::post('update/voter/phone', [VoterController::class,'updateVoterPhone'])->name('voter.update_phone');
Route::put('attendant/initalize', [SettingController::class, 'initalizeAttendant'])->name('attendant.initalize');

//========================================================================================================================================
