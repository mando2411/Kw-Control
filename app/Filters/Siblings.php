<?php
namespace App\Filters;

use Termwind\Components\Dd;

class Siblings extends Filter
{

    public function applyFilter($builder){
        $data=json_decode(request($this->filterName()), true);
        if(isset($data['father'])){
            $builder->where('father',$data['father']);
        }else{
            $builder->where('grand',$data['grand']);
        }
        return $builder;
    }

}
