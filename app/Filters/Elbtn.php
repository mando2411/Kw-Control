<?php
namespace App\Filters;

class Elbtn extends Filter
{

    public function applyFilter($builder){
        $value = request($this->filterName(), request('albtn'));
        return $builder->where('albtn', $value);

    }

}
