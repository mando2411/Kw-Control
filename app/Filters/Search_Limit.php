<?php
namespace App\Filters;

class Search_Limit extends Filter
{
    public function applyFilter($builder)
    {
        $limit = intval(request($this->filterName()));

        if ($limit <= 0) {
            return $builder;
        }

        return $builder->limit($limit);
    }
}

