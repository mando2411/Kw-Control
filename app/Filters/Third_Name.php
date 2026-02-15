<?php
namespace App\Filters;

class Third_Name extends Filter
{

    public function applyFilter($builder){
        $third_name=request($this->filterName());
        return $builder->where(function ($query) use ($third_name) {
            $query->whereRaw("SUBSTRING_INDEX(SUBSTRING_INDEX(name, ' ', 3), ' ', -1) LIKE ?", [$third_name . '%']);
        });
    }

}
