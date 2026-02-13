<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermittedMiddleware
{
    private array $excluded =[
        'dashboard.model.auto.translate',
        'dashboard.toggle-theme',
        'dashboard.settings.result',
        'dashboard.store-fake-candidates',
        'dashboard.notifications.index',
        'dashboard.notifications.page',
        'dashboard.notifications.read-all',
        'dashboard.notifications.read',
    ];
    public function handle(Request $request, Closure $next)
    {
        try {
            $permission = Str::of($request->route()->getName())
                ->remove('dashboard.')
                ->replace('statement.search-modern', 'statement.search')
                ->replace('store', 'create')
                ->replace('index', 'list')
                ->replace('update', 'edit')
                ->replace('destroy', 'delete')
                ->replace('import-contractor-voters-form','import.contractor.voters')
                ->replace('import-contractor-voters','import.contractor.voters');

        } catch (\Exception $exception) {
            report($exception);
            $permission = '';
        }

        if ($permission && !in_array($request->route()->getName(), $this->excluded)) {
            //dd(['no permission',$permission,$request->route()->getName(),admin()->getPermissionsViaRoles()]);
            abort_if(admin()->cannot($permission), 403);
        }

        return $next($request);
    }
}
