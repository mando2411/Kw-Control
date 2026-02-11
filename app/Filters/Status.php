<?php
namespace App\Filters;

class Status extends Filter
{

    public function applyFilter($builder){
        return $builder->where('status',request($this->filterName()));

    }

}
