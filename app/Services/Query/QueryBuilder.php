<?php

namespace App\Services\Query;

use App\Services\Request\UriParser;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Str;
use Unlu\Laravel\Api\QueryBuilder as ApiQueryBuilder;

class QueryBuilder extends ApiQueryBuilder
{
    private array $withQueryParameters;
    private array $withQueryOrders;
    /**
     * @var Repository|Application|mixed
     */
    protected mixed $page_limit;

    /**
     * @param Model $model
     * @param Request $request
     */
    public function __construct(Model $model, Request $request)
    {
        $this->orderBy = config('api-query-builder.orderBy');

        $this->limit = config('api-query-builder.limit');

        $this->excludedParameters = array_merge(
            $this->excludedParameters,
            config('api-query-builder.excludedParameters')
        );

        $this->model = $model;

        $this->uriParser = new UriParser($request);

        $this->query = $this->model->with($this->includes);

        $this->withQueryParameters = [];
        $this->withQueryOrders = [];
    }

    private function setterMethodName($key): string
    {
        return 'set' . Str::studly($key);
    }

    private function customFilterName($key): string
    {
        return 'filterBy' . Str::studly($key);
    }
}
