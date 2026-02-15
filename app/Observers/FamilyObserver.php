<?php

namespace App\Observers;

use App\Models\Family;

class FamilyObserver
{
    /**
     * Handle the Family "created" event.
     *
     * @param  \App\Models\Family  $family
     * @return void
     */
    public function created(Family $family)
    {
        //
    }

    /**
     * Handle the Family "updated" event.
     *
     * @param  \App\Models\Family  $family
     * @return void
     */
    public function updated(Family $family)
    {
        //
    }

    /**
     * Handle the Family "deleted" event.
     *
     * @param  \App\Models\Family  $family
     * @return void
     */
    public function deleted(Family $family)
    {
        //
    }

    /**
     * Handle the Family "restored" event.
     *
     * @param  \App\Models\Family  $family
     * @return void
     */
    public function restored(Family $family)
    {
        //
    }

    /**
     * Handle the Family "force deleted" event.
     *
     * @param  \App\Models\Family  $family
     * @return void
     */
    public function forceDeleted(Family $family)
    {
        //
    }
}
