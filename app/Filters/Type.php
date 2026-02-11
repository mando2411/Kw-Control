<?php
namespace App\Filters;

use App\Models\Voter;

class Type extends Filter
{

    public function applyFilter($builder){
        if(request($this->filterName()) != "ذكر" &&  request($this->filterName()) != "all"){
            return $builder->where('type', "!=" ,"ذكر");
        }elseif(request($this->filterName()) == "ذكر"){
            return $builder->where('type', "ذكر");
        }
        return $builder;
    }

}
