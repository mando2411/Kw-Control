<?php
namespace App\Filters;

use Termwind\Components\Dd;

class Siblings extends Filter
{

    public function applyFilter($builder){
        $raw = request($this->filterName());
        if (empty($raw)) {
            return $builder;
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return $builder;
        }

        if(isset($data['father'])){
            $builder->where('father',$data['father']);
        }elseif(isset($data['grand'])){
            $builder->where('grand',$data['grand']);
        }
        return $builder;
    }

}
