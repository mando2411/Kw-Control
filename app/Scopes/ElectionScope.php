<?php

namespace App\Scopes;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ElectionScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (auth()->check() && !auth()->user()->hasRole('Administrator')) {
            $builder->whereHas('election', function ($query) {
                $query->where('election_id', auth()->user()->election_id);
            });
        }
    }
}
