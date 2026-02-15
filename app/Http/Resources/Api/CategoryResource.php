<?php

namespace App\Http\Resources\Api;

use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @property Category $resource
 */
class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'slug' => $this->resource->slug,
            'enabled' => $this->resource->enabled,
            'featured' => $this->resource->featured,
            'banner' => $this->resource->banner,
            'featured_image' => $this->resource->featured_image,
            'gallery' => $this->resource->gallery,
            $this->mergeWhen($this->resource->relationLoaded('seo'), [
                'seo' => new SeoResource($this->resource->seo)
            ]),
        ];
    }
}
