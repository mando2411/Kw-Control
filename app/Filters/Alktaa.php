<?php
namespace App\Filters;

class Alktaa extends Filter
{

    public function applyFilter($builder){
        return $builder->where('alktaa',request($this->filterName()));

    }

}
