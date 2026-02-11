<?php
namespace App\Filters;

class Fourth_Name extends Filter
{

    public function applyFilter($builder){
        $fourth_name=request($this->filterName());
        return $builder->where(function ($query) use ($fourth_name) {
            $query->whereRaw("SUBSTRING_INDEX(SUBSTRING_INDEX(name, ' ', 4), ' ', -1) LIKE ?", [$fourth_name . '%']);
        });
    }

}
