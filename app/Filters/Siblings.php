<?php
namespace App\Filters;

class Siblings extends Filter
{

    public function applyFilter($builder){
        $raw = request($this->filterName());
        if (empty($raw)) {
            return $builder;
        }

        if (is_array($raw)) {
            $data = $raw;
        } else {
            $data = json_decode((string) $raw, true);
            if (!is_array($data)) {
                return $builder;
            }
        }

        $father = $data['father'] ?? null;
        $grand = $data['grand'] ?? null;

        if(!empty($father)){
            $builder->where('father', $father);
        }elseif(!empty($grand)){
            $builder->where('grand', $grand);
        }
        return $builder;
    }

}
