<?php

namespace App\Observers;

use App\Models\Candidate;

class CandidateObserver
{
    /**
     * Handle the Candidate "created" event.
     *
     * @param  \App\Models\Candidate  $candidate
     * @return void
     */
    public function created(Candidate $candidate)
    {
        //
    }

    /**
     * Handle the Candidate "updated" event.
     *
     * @param  \App\Models\Candidate  $candidate
     * @return void
     */
    public function updated(Candidate $candidate)
    {
        //
    }

    /**
     * Handle the Candidate "deleted" event.
     *
     * @param  \App\Models\Candidate  $candidate
     * @return void
     */
    public function deleted(Candidate $candidate)
    {
        //
    }

    /**
     * Handle the Candidate "restored" event.
     *
     * @param  \App\Models\Candidate  $candidate
     * @return void
     */
    public function restored(Candidate $candidate)
    {
        //
    }

    /**
     * Handle the Candidate "force deleted" event.
     *
     * @param  \App\Models\Candidate  $candidate
     * @return void
     */
    public function forceDeleted(Candidate $candidate)
    {
        //
    }
}
