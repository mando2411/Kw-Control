<?php

namespace App\Observers;

use App\Models\FaqCategory;

class FaqCategoryObserver
{
    /**
     * Handle the FaqCategory "created" event.
     *
     * @param  \App\Models\FaqCategory  $faqCategory
     * @return void
     */
    public function created(FaqCategory $faqCategory)
    {
        //
    }

    /**
     * Handle the FaqCategory "updated" event.
     *
     * @param  \App\Models\FaqCategory  $faqCategory
     * @return void
     */
    public function updated(FaqCategory $faqCategory)
    {
        //
    }

    /**
     * Handle the FaqCategory "deleted" event.
     *
     * @param  \App\Models\FaqCategory  $faqCategory
     * @return void
     */
    public function deleted(FaqCategory $faqCategory)
    {
        //
    }

    /**
     * Handle the FaqCategory "restored" event.
     *
     * @param  \App\Models\FaqCategory  $faqCategory
     * @return void
     */
    public function restored(FaqCategory $faqCategory)
    {
        //
    }

    /**
     * Handle the FaqCategory "force deleted" event.
     *
     * @param  \App\Models\FaqCategory  $faqCategory
     * @return void
     */
    public function forceDeleted(FaqCategory $faqCategory)
    {
        //
    }
}
