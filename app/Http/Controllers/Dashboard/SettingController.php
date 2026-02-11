<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Setting;
use App\Models\Election;
use App\Enums\SettingKey;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SettingsRequest;

class SettingController extends Controller
{
    public function show()
    {
        $settings = Setting::all();
        return view('dashboard.settings.show', compact('settings'));
    }

    public function update(SettingsRequest $request)
    {
        foreach (SettingKey::all() as $key) {
            Setting::where('option_key', $key)->updateOrCreate([
                'option_key' => $key,
            ], [
                'option_key' => $key,
                'option_value' => $request->get($key)
            ]);
        }
        session()->flash('message', 'Settings Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }
    //================================================================================================
    public function resultControl(){
        $settings = Setting::all();
        $candidates = Candidate::get();
        $elections  = Election::get();
        return view('dashboard.settings.result_control', compact('settings','candidates','elections'));
    }
    //================================================================================================
    public function elec(){
        $elections=Election::select('id','name')->get();
        return view('dashboard.settings.election',compact('elections'));
    }
    public function elecUp(Request $request){
        $election =Election::find($request->election_id);
        if($election){
            
    // Reset all candidates and their related data
        $election->candidates()->chunkById(100, function ($candidates) {
            $candidates->each(function ($candidate) {
                $candidate->update(['votes' => 0]);
                $candidate->committees()->each(function ($committee) {
                    $committee->pivot->update(['votes' => 0]);
                });
            });
        });

    // Reset all voters
        $election->voters()->chunkById(100, function ($voters) {
            $voters->each(function ($voter) {
                $voter->update([
                    'status' => 0,
                    'committee_id' => null,
                    'attend_id' => null
                ]);
            });
        });

        
        }   
        return redirect()->back()->with('success', 'Election data has been successfully reset.');
    }
    //================================================================================================
    public function initalizeAttendant(Request $request){
        // dd($request->all());
        $election_id=$request->election_id;
        try {  
            
            DB::beginTransaction();
            // Update voters
            DB::update("
                UPDATE voters 
                SET status = 0, 
                    committee_id = NULL, 
                    attend_id = NULL 
                WHERE status = 1 
                AND committee_id IS NOT NULL 
                AND attend_id IS NOT NULL 
                AND id IN (SELECT voter_id FROM election_voter WHERE election_id = ?)", 
                [$election_id]
            );
            
            // Update candidates
            DB::update("
                UPDATE candidates 
                SET votes = 0 
                WHERE election_id = ?", 
                [$election_id]
            );
            DB::commit();
            // dd('done');
            session()->flash('message', 'تم تصفير الحضور بنجاح');
            session()->flash('type', 'success');
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('message', 'حدث خطا اثناء التصفير');
            session()->flash('type', 'error');
            return back();

        }
    }
    //================================================================================================
}
