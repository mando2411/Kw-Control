<?php

namespace App\Models;

use App\Traits\Models\AutoTranslate;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
/**
* @property string $meta_title
* @property string $meta_description
* @property string $meta_keywords
* @property string $og_title
* @property string $og_description
 */
class Seo extends Model
{
    use Translatable, AutoTranslate;

    protected $table = 'seos';

    protected $fillable = [
        'og_image',
    ];

    public array $translatedAttributes = [
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
    ];

    /**
     * Get the parent seoable model (tour or post...etc).
     */
    public function seo(): MorphTo
    {
        return $this->morphTo();
    }
}
