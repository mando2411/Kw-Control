<?php
namespace App\Filters;

class ID extends Filter
{

    public function applyFilter($builder){
        return $builder->where('alrkm_almd_yn', 'LIKE','%' .request($this->filterName()) . '%');

    }

}
