<?php
namespace App\Filters;

use App\Helpers\ArabicHelper;

class Name extends Filter
{

    public function applyFilter($builder){
        $filterValue = request($this->filterName());

        if (!$filterValue) {
            return $builder; // If no filter is provided, return the unmodified query
        }
        $names = explode(' ', $filterValue);
        return $builder->where(function ($query) use ($names) {
            foreach ($names as $name) {
                $normalizedInput = ArabicHelper::normalizeArabic($name);
                $query->where(function ($nameQuery) use ($normalizedInput, $name) {
                    $nameQuery->where('normalized_name', 'LIKE', "%{$normalizedInput}%")
                        ->orWhere('name', 'LIKE', "%{$name}%");
                });
            }
        });
    }

}
