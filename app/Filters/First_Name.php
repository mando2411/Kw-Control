<?php
namespace App\Filters;

use App\Helpers\ArabicHelper;

class First_Name extends Filter
{

    public function applyFilter($builder){

        $normalizedInput = ArabicHelper ::normalizeArabic(request($this->filterName()));
        $soundexInput = ArabicHelper::arabicSoundex(request($this->filterName()));

        return $builder->where('normalized_name', 'LIKE', $normalizedInput . '%');

    }

}
