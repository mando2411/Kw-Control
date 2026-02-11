<?php

namespace App\Jobs;

use App\Models\Tour;
use App\Models\TourDay;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TranslateResourceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private mixed $resource_id;
    private mixed $resource_class;

    public function __construct($resource_id, $resource_class)
    {
        $this->resource_id = $resource_id;
        $this->resource_class = $resource_class;
    }

    public function handle(): void
    {
        $resource = (new $this->resource_class)->newQuery()->find($this->resource_id);
        $resource->autoTranslate();

        if ($resource->seo) {
            $resource->seo->autoTranslate();
        }
    }
}
