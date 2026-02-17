<?php

namespace App\Http\Middleware;

use App\Models\Role;
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
        'dashboard.statement.export-async',
        'dashboard.statement.export-download',
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
            $deniedByDefault = admin()->cannot($permission);

            if ($deniedByDefault && $this->listLeaderHasCandidatePermission((string) $permission)) {
                $deniedByDefault = false;
            }

            abort_if($deniedByDefault, 403);
        }

        return $next($request);
    }

    private function listLeaderHasCandidatePermission(string $permission): bool
    {
        if (!admin() || !admin()->hasRole('مرشح رئيس قائمة')) {
            return false;
        }

        $candidateRole = Role::where('name', 'مرشح')->first();
        if (!$candidateRole) {
            return false;
        }

        return $candidateRole->hasPermissionTo($permission);
    }
}
