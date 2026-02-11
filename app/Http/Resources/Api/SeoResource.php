<?php

namespace App\Http\Resources\Api;

use App\Models\Seo;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Seo $resource
 */
class SeoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'meta_title' => $this->resource->meta_title,
            'meta_description' => $this->resource->meta_description,
            'meta_keywords' => $this->resource->meta_keywords,
            'og_title' => $this->resource->og_title,
            'og_description' => $this->resource->og_description,
            'og_image' => $this->resource->og_image,
        ];
    }
}
