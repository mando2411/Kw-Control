<?php

namespace App\Services;

use App\Enums\Type;
use App\Models\Committee;
use App\Models\Voter;
use Illuminate\Http\Request;

class Attendance
{
    /**
     * Get all committees.
     */
    public function getCommittees()
    {
        return Committee::select('name', 'id', 'type')->get()->map(function ($committee) {
            $committee->title = "{$committee->name} ({$committee->type}) - {$committee->id}";
            return $committee;
        });
    }

    /**
     * Get filtered voters using the pipeline filter.
     */
    public function getVoters($request)
    {
        if ($request->committee) {
            $committee= Committee::find($request->committee);
            $request['type']= Type::normalize($committee->type);
        }

        // Use the static Filter method from the Voter model
        return $request->filled('name') || $request->filled('box') ? Voter::Filter() : null;
    }

    public function status($id ,Request $request)
    {
        $voter = Voter::findOrFail($id);
        $status = $request->input('status') === '1'; // True if '1', otherwise False
        $committeeId = $status ? $request->input('committee') : null;

        $voter->user_id = auth()->user()->id;
        $voter->status = $status;
        $this->counting($status);

        if ($status && $committeeId) {
            $committee = Committee::find($committeeId);
            $voter->committee_id = $committee ? $committee->id : null;
            $voter->attend_id=auth()->user()->id ?? null;
        } else {

            $voter->committee_id = null;
            $voter->attend_id= null;

        }


        return $voter->save();
    }
    function counting(bool $status) :void
    {
        if (auth()->user()->representatives()->exists()){
            $value=$status ? 1 : -1;
            $rep=auth()->user()->representatives()->first();
            $attendance=$rep->attendance;
            if ($status || ($value != -1 && $attendance > 0)) {
                $count = $attendance + $value;
                $rep->update(['attendance'=>$count]);
            }
        }
    }
}
