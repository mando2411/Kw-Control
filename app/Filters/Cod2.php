<?php
namespace App\Filters;

class Cod2 extends Filter
{

    public function applyFilter($builder){
        return $builder->where('cod2', 'LIKE','%' .request($this->filterName()) . '%');

    }

}
