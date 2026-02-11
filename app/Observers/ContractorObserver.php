<?php

namespace App\Observers;

use App\Models\Contractor;

class ContractorObserver
{
    /**
     * Handle the Contractor "created" event.
     *
     * @param  \App\Models\Contractor  $contractor
     * @return void
     */
    public function created(Contractor $contractor)
    {
        //
    }

    /**
     * Handle the Contractor "updated" event.
     *
     * @param  \App\Models\Contractor  $contractor
     * @return void
     */
    public function updated(Contractor $contractor)
    {
        //
    }

    /**
     * Handle the Contractor "deleted" event.
     *
     * @param  \App\Models\Contractor  $contractor
     * @return void
     */
    public function deleted(Contractor $contractor)
    {
        //
    }

    /**
     * Handle the Contractor "restored" event.
     *
     * @param  \App\Models\Contractor  $contractor
     * @return void
     */
    public function restored(Contractor $contractor)
    {
        //
    }

    /**
     * Handle the Contractor "force deleted" event.
     *
     * @param  \App\Models\Contractor  $contractor
     * @return void
     */
    public function forceDeleted(Contractor $contractor)
    {
        //
    }
}
