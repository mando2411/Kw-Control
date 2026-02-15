<?php
namespace App\Filters;

use App\Models\Contractor;

class Mot3ahed extends Filter
{

    public function applyFilter($builder){
        $contractor = Contractor::where('id', request($this->filterName()))->first();

        if (!$contractor) {
            return $builder;
        }
        $voterIds = $contractor->childs()->with('voters')->get()->pluck('voters.*.id')->flatten()->unique();
        return $builder->whereIn('id', $voterIds);
    }

}
