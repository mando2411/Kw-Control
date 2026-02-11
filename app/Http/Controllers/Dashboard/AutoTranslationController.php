<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AutoTranslationController extends Controller
{
    public function translate(): JsonResponse
    {
        if (!request('model')) {

            return response()
                ->json([
                    'message' => "Model Field is required"
                ], Response::HTTP_BAD_REQUEST);
        }
        $model = Str::of(request('model'))->plural();

        $modelJob = '\App\Jobs\Translate'.$model.'Job';

        if (class_exists($modelJob)) {
            $modelJob::dispatch((array) request('id', []));
            return response()->json([
                'message'=> 'Translation job is running in background now, Please wait'
            ]);
        }

        return response()
            ->json([
                'resource' => $modelJob,
                'message' => 'Translation not implemented for '. $model
            ], Response::HTTP_BAD_REQUEST);
    }
}
