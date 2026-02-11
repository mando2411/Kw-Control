<?php
namespace App\Filters;

class Elfar3 extends Filter
{

    public function applyFilter($builder){
        return $builder->where('alfraa',request($this->filterName()));

    }

}
