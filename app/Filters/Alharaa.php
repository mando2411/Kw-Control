<?php
namespace App\Filters;

class Alharaa extends Filter
{
    public function applyFilter($builder)
    {
        $alharaa = request($this->filterName());

        return $builder->whereExists(function ($query) use ($alharaa) {
            $query->selectRaw('1')
                ->from('selections')
                ->where('selections.alharaa', $alharaa)
                ->whereColumn('selections.alfkhd', 'voters.alfkhd')
                ->whereColumn('selections.alfraa', 'voters.alfraa')
                ->whereColumn('selections.albtn', 'voters.albtn')
                ->whereColumn('selections.alktaa', 'voters.alktaa');
        });
    }
}
