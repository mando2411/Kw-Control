<?php
namespace App\Filters;

use App\Helpers\ArabicHelper;

class First_Name extends Filter
{

    public function applyFilter($builder){

        $normalizedInput = ArabicHelper::normalizeArabic(request($this->filterName()));

        return $builder->where(function ($query) use ($normalizedInput) {
            $query->where('normalized_name', 'LIKE', $normalizedInput . '%')
                ->orWhere('name', 'LIKE', request($this->filterName()) . '%');
        });

    }

}
