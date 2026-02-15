<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        $availableGuards = array_keys(config('auth.guards', []));
        $normalizedGuards = [];

        foreach ($guards as $guard) {
            if (in_array($guard, $availableGuards, true)) {
                $normalizedGuards[] = $guard;
                continue;
            }

            if ($guard === 'client_web') {
                if (in_array('client_web', $availableGuards, true)) {
                    $normalizedGuards[] = 'client_web';
                } elseif (in_array('client', $availableGuards, true)) {
                    $normalizedGuards[] = 'client';
                } elseif (in_array('web', $availableGuards, true)) {
                    $normalizedGuards[] = 'web';
                }
            }
        }

        if (empty($normalizedGuards) && !empty($guards) && in_array('web', $availableGuards, true)) {
            $normalizedGuards = ['web'];
        }

        return parent::handle($request, $next, ...$normalizedGuards);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }
}
