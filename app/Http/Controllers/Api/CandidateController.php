<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Api\CandidateResource;
use App\Models\Candidate;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\JsonResponse;
use App\Services\Query\QueryBuilder;
use Exception;

class CandidateController extends Controller
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
        $queryBuilder = new QueryBuilder(new Candidate, $request);
        $candidates = $queryBuilder->build()->paginate();
        $collection = CandidateResource::collection($candidates->getCollection());
        $candidates->setCollection(collect($collection));
        return $this->send($candidates);
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $id
     * @return JsonResponse
     */
    public function show(mixed $id)
    {
        $candidate = Candidate::findOrFail($id);
        return $this->send(new CandidateResource($candidate));
    }
}
