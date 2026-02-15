<?php
namespace App\Filters;

class Phone extends Filter
{

    public function applyFilter($builder){
        return $builder->where('phone1', 'LIKE','%' .request($this->filterName()) . '%');

    }

}
