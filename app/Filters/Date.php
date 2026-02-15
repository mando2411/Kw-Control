<?php
namespace App\Filters;

class Date extends Filter
{

    public function applyFilter($builder){
        $date=request($this->filterName());
      return $builder->whereHas('contractors', function ($query) use ($date) {
        $query->where('contractor_voter.created_at', '>', $date);
    });
    }

}
