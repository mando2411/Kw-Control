<?php

namespace App\Jobs;

use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TranslateCategoriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $ids;

    public function __construct($ids = [])
    {
        $this->ids = $ids;
    }

    public function handle(): void
    {
        $query = Category::query();

        if (!empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        $query->chunkById(5, function (Collection $categories) {
            $categories->each(fn(Category $category) =>  TranslateResourceJob::dispatch($category->id, Category::class));
        });
    }
}
