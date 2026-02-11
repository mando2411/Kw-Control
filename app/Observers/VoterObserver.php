<?php

namespace App\Observers;

use App\Models\Voter;

class VoterObserver
{
    /**
     * Handle the Voter "created" event.
     *
     * @param  \App\Models\Voter  $voter
     * @return void
     */
    public function created(Voter $voter)
    {
        //
    }

    /**
     * Handle the Voter "updated" event.
     *
     * @param  \App\Models\Voter  $voter
     * @return void
     */
    public function updated(Voter $voter)
    {
        //
    }

    /**
     * Handle the Voter "deleted" event.
     *
     * @param  \App\Models\Voter  $voter
     * @return void
     */
    public function deleted(Voter $voter)
    {
        //
    }

    /**
     * Handle the Voter "restored" event.
     *
     * @param  \App\Models\Voter  $voter
     * @return void
     */
    public function restored(Voter $voter)
    {
        //
    }

    /**
     * Handle the Voter "force deleted" event.
     *
     * @param  \App\Models\Voter  $voter
     * @return void
     */
    public function forceDeleted(Voter $voter)
    {
        //
    }
}
