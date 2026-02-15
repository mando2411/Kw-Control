<?php
namespace App\Services;

use App\Models\Committee;
use App\Models\Candidate;
use App\Events\VoteUpdated;


class VoteService
{
    //========================================================================
    public function updateVotes($committeeId, $candidateId, $votes, $isIncrement = false)
    {
        $committee = Committee::with(['candidates' => function($query) use ($candidateId) {
            $query->where('candidate_id', $candidateId);
        }])->find($committeeId);

        $candidate = $committee->candidates->first();

        if (!$candidate) {
            return ['error' => 'Candidate not found.', 'status' => 404];
        }

        $newVotes = $isIncrement ? ($candidate->pivot->votes + $votes) : $votes;

        if ($newVotes < 0) {
            return ['error' => 'Votes cannot be negative.', 'status' => 400];
        }

        $candidate->pivot->votes = $newVotes;
        $candidate->pivot->save();

        $candidate->votes = $candidate->committees->sum('pivot.votes');
        $candidate->save();
        event(new VoteUpdated($candidate));


        return ['success' => 'Votes updated successfully.', 'status' => 200];
    }
    //========================================================================
    //update votes using ajax
    public function updateVotes2($committee, $candidate_id, $count_status, $vote_count){
        //========================================================================
        //confirm that candidate is in the committee
        $committee = Committee::with(['candidates' => function($query) use ($candidate_id) {
            $query->where('candidate_id', $candidate_id);
        }])->find($committee);
        
        $candidate = $committee->candidates->first();
        
        if (!$candidate) {
            return ['error' => 'تاكد من تواجد المرشح', 'status' => 404];
        }
        //========================================================================
        $old_vote  = $candidate->pivot->votes;
        
        if($count_status == 'increment'){
            $newVotes = $old_vote + $vote_count;
        }elseif($count_status == 'decrement'){
            $newVotes = $old_vote - $vote_count;
        }else{//set
            $newVotes = $vote_count;
        }
        
        if ($newVotes < 0) {
            return ['error' => 'لا يمكن حذف عدد اصوات اكبر من العدد الموجود', 'status' => 404,];
        }
        //========================================================================
        $candidate->pivot->votes = $newVotes;
        $candidate->pivot->save();

        $candidate->votes = $candidate->committees->sum('pivot.votes');
        $candidate->save();
        //========================================================================
        event(new VoteUpdated($candidate));
        //========================================================================
        return ['success' => 'تم التصويت بنجاح', 'status' => 200,'vote_count'=> $newVotes];
    }
    //========================================================================
}