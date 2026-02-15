<?php
namespace App\Filters;

class Elfar3 extends Filter
{

    public function applyFilter($builder){
        $value = request($this->filterName(), request('alfraa'));
        return $builder->where('alfraa', $value);

    }

}
