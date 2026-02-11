<?php
namespace App\Filters;

class Committee extends Filter
{

    public function applyFilter($builder){
      return $builder->where('committee_id',request($this->filterName()));

    }

}
