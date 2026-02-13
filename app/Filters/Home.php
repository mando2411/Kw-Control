<?php
namespace App\Filters;

class Home extends Filter
{
    public function applyFilter($builder)
    {
        $home = request($this->filterName());

        return $builder->whereExists(function ($query) use ($home) {
            $query->selectRaw('1')
                ->from('selections')
                ->where('selections.home', $home)
                ->whereColumn('selections.alfkhd', 'voters.alfkhd')
                ->whereColumn('selections.alfraa', 'voters.alfraa')
                ->whereColumn('selections.albtn', 'voters.albtn')
                ->whereColumn('selections.alktaa', 'voters.alktaa');
        });
    }
}
