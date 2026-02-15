<?php
namespace App\Filters;

class Restricted extends Filter
{

    public function applyFilter($builder){
        return $builder->where('restricted', request($this->filterName()) );

    }

}
