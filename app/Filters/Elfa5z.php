<?php
namespace App\Filters;

class Elfa5z extends Filter
{

    public function applyFilter($builder){
        return $builder->where('alfkhd',request($this->filterName()));

    }

}
