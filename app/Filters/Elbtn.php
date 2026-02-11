<?php
namespace App\Filters;

class Elbtn extends Filter
{

    public function applyFilter($builder){
        return $builder->where('albtn',request($this->filterName()));

    }

}
