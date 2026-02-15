<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Contractor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class HistoryController extends Controller
{
    public function history(Request $request){
        $activity=Activity::query();

        if (collect($request)->isNotEmpty()) {
            $dateFrom = Carbon::parse($request->dateFrom);
            $dateTo = Carbon::parse($request->dateTo);
            $activity = $activity->whereBetween('created_at', [$dateFrom,$dateTo]);
        }
        $authUserLogs = $activity->where('causer_id', auth()->user()->id)->get();
        $createdUsers = User::where('creator_id', auth()->user()->id)->pluck('id');
        $createdUserLogs = $activity->whereIn('causer_id', $createdUsers)->get();


        $allLogs = $authUserLogs->merge($createdUserLogs);
        return view('dashboard.history.history',compact('allLogs'));
    }
    public function delete(){
        if(auth()->user()->hasRole("Administrator")){
            $children=Contractor::Children()->whereHas('softDelete')->with('user')->get();
        }else{
            $children = auth()->user()->contractors()->Children()->whereHas('softDelete')->with('user')->get();
        }

        return view('dashboard.history.delete',compact('children'));
    }
}
