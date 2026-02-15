<?php
namespace App\Filters;

class Second_Name extends Filter
{

    public function applyFilter($builder){
        $second_name=request($this->filterName());
        return $builder->where(function ($query) use ($second_name) {
            $query->whereRaw("SUBSTRING_INDEX(SUBSTRING_INDEX(name, ' ', 2), ' ', -1) LIKE ?", [$second_name . '%']);
        });
    }

}
