<?php

namespace App\Observers;

use App\Models\Committee;

class CommitteeObserver
{
    /**
     * Handle the Committee "created" event.
     *
     * @param  \App\Models\Committee  $committee
     * @return void
     */
    public function created(Committee $committee)
    {
        //
    }

    /**
     * Handle the Committee "updated" event.
     *
     * @param  \App\Models\Committee  $committee
     * @return void
     */
    public function updated(Committee $committee)
    {
        //
    }

    /**
     * Handle the Committee "deleted" event.
     *
     * @param  \App\Models\Committee  $committee
     * @return void
     */
    public function deleted(Committee $committee)
    {
        //
    }

    /**
     * Handle the Committee "restored" event.
     *
     * @param  \App\Models\Committee  $committee
     * @return void
     */
    public function restored(Committee $committee)
    {
        //
    }

    /**
     * Handle the Committee "force deleted" event.
     *
     * @param  \App\Models\Committee  $committee
     * @return void
     */
    public function forceDeleted(Committee $committee)
    {
        //
    }
}
