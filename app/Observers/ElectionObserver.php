<?php

namespace App\Observers;

use App\Models\Election;

class ElectionObserver
{
    /**
     * Handle the Election "created" event.
     *
     * @param  \App\Models\Election  $election
     * @return void
     */
    public function created(Election $election)
    {
        //
    }

    /**
     * Handle the Election "updated" event.
     *
     * @param  \App\Models\Election  $election
     * @return void
     */
    public function updated(Election $election)
    {
        //
    }

    /**
     * Handle the Election "deleted" event.
     *
     * @param  \App\Models\Election  $election
     * @return void
     */
    public function deleted(Election $election)
    {
        //
    }

    /**
     * Handle the Election "restored" event.
     *
     * @param  \App\Models\Election  $election
     * @return void
     */
    public function restored(Election $election)
    {
        //
    }

    /**
     * Handle the Election "force deleted" event.
     *
     * @param  \App\Models\Election  $election
     * @return void
     */
    public function forceDeleted(Election $election)
    {
        //
    }
}
