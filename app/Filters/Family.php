<?php
namespace App\Filters;

class Family extends Filter
{

    public function applyFilter($builder){
        return $builder->where('family_id', request($this->filterName()));

    }

}
