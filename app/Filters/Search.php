<?php
namespace App\Filters;

class Search extends Filter
{

    public function applyFilter($builder){
        if(request('search') == "1"){
            return $builder->whereHas('contractors');
           }elseif(request('search') == "0"){
            return $builder->whereDoesntHave('contractors');
        }
        return $builder;

    }

}
