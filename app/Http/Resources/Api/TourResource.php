<?php

namespace App\Http\Resources\Api;

use App\Models\Tour;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Tour $resource
 */
class TourResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            'title' => $this->resource->title,
            'slug' => $this->resource->slug,
            'overview' => $this->resource->overview,
            'highlights' => $this->resource->highlights,
            'included' => $this->resource->included,
            'excluded' => $this->resource->excluded,
            'duration' => $this->resource->duration,
            'type' => $this->resource->type,
            'run' => $this->resource->run,
            'pickup_time' => $this->resource->pickup_time,
            'enabled' => $this->resource->enabled,
            'featured' => $this->resource->featured,
            'featured_image' => $this->resource->featured_image,
            'gallery' => $this->resource->gallery,
            'start_from' => $this->resource->start_from,
            'adult_price' => $this->resource->adult_price,
            'child_price' => $this->resource->child_price,

            'pricing_groups' => $this->resource->isEmptyPricingGroups() ? [] : $this->resource->pricing_groups,
            $this->mergeWhen($this->resource->relationLoaded('days'), [
                'destinations' => DestinationResource::collection($this->resource->destinations)
            ]),
            $this->mergeWhen($this->resource->relationLoaded('destinations'), [
                'destinations' => DestinationResource::collection($this->resource->destinations)
            ]),
            $this->mergeWhen($this->resource->relationLoaded('categories'), [
                'categories' => CategoryResource::collection($this->resource->categories)
            ]),

            $this->mergeWhen($this->resource->relationLoaded('seo'), [
                'seo' => new SeoResource($this->resource->seo)
            ]),
        ];
    }
}
