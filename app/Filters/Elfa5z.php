<?php
namespace App\Filters;

class Elfa5z extends Filter
{

    public function applyFilter($builder){
        $value = request($this->filterName(), request('alfkhd'));
        return $builder->where('alfkhd', $value);

    }

}
