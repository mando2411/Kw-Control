<?php
namespace App\Filters;

class Cod1 extends Filter
{

    public function applyFilter($builder){
        return $builder->where('cod1', 'LIKE','%' .request($this->filterName()) . '%');

    }

}
