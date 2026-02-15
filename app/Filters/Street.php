<?php
namespace App\Filters;

class Street extends Filter
{
    public function applyFilter($builder)
    {
        $street = request($this->filterName());

        return $builder->whereExists(function ($query) use ($street) {
            $query->selectRaw('1')
                ->from('selections')
                ->where('selections.street', $street)
                ->whereColumn('selections.alfkhd', 'voters.alfkhd')
                ->whereColumn('selections.alfraa', 'voters.alfraa')
                ->whereColumn('selections.albtn', 'voters.albtn')
                ->whereColumn('selections.alktaa', 'voters.alktaa');
        });
    }
}
