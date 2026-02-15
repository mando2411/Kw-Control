<?php
namespace App\Filters;

class Age extends Filter
{

    public function applyFilter($builder){
        if (!is_null(request($this->filterName())['from'])) {
            $builder->where('age', '>=', request($this->filterName())['from']);
        }

        if (!is_null(request($this->filterName())['to'])) {
            $builder->where('age', '<=', request($this->filterName())['to']);
        }
        return $builder;

    }

}
