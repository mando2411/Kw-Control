<?php
namespace App\Filters;

class Box extends Filter
{

    public function applyFilter($builder){
        return $builder->where('alsndok', 'LIKE','%' .request($this->filterName()) . '%');

    }

}
