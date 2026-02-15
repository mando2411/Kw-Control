<?php

namespace App\Jobs;

use App\Models\Seo;
use App\Models\Tour;
use App\Models\TourDay;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TranslateToursJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $ids;

    public function __construct($ids = [])
    {
        $this->ids = $ids;
    }

    public function handle(): void
    {

        $query = Tour::query()->with('days');

        if (!empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        $query->chunk(10, function (Collection $tours) {

            $tours->each(function (Tour $tour) {

                TranslateResourceJob::dispatch($tour->id, Tour::class);

                $tour->days->each(function (TourDay $tourDay) {
                    TranslateResourceJob::dispatch($tourDay->id, TourDay::class);
                });

            });

        });
    }
}
