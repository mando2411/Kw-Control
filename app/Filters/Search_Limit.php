<?php
namespace App\Filters;

class Search_Limit extends Filter
{
    public function applyFilter($builder)
    {
        $limit = intval(request($this->filterName()));

        return $builder->limit($limit);
    }
}

