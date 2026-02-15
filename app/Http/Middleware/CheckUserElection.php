<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserElection
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->election_id) {
            // رسالة واضحة بدل 500
            return response()->view('errors.no-election', [], 403);
        }

        return $next($request);
    }
}
