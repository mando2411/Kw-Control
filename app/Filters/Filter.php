<?php
namespace App\Filters;
use Closure;
use Illuminate\Support\Str;

abstract class Filter{

    public function handle($request , Closure $next){
        if(! request()->filled($this->filterName())){
            return $next($request);
        }
        $builder= $next($request);

        return $this->applyFilter($builder);
    }
    protected abstract function applyFilter($builder);
    protected function filterName(){
        return strtolower(class_basename($this));
    }
}
