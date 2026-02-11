<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Api\ReportResource;
use App\Models\Report;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\JsonResponse;
use App\Services\Query\QueryBuilder;
use Exception;

class ReportController extends Controller
{
    use HasApiResponse;

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request)
    {
        $queryBuilder = new QueryBuilder(new Report, $request);
        $reports = $queryBuilder->build()->paginate();
        $collection = ReportResource::collection($reports->getCollection());
        $reports->setCollection(collect($collection));
        return $this->send($reports);
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $id
     * @return JsonResponse
     */
    public function show(mixed $id)
    {
        $report = Report::findOrFail($id);
        return $this->send(new ReportResource($report));
    }
}
