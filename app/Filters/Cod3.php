<?php
namespace App\Filters;

class Cod3 extends Filter
{

    public function applyFilter($builder){
        return $builder->where('cod3', 'LIKE','%' .request($this->filterName()) . '%');

    }

}
