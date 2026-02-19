<?php

namespace App\Http\Middleware;

use App\Models\Candidate;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PermittedMiddleware
{
    private array $excluded =[
        'dashboard.model.auto.translate',
        'dashboard.toggle-theme',
        'dashboard.settings.result',
        'dashboard.settings.demo-data.enable',
        'dashboard.settings.demo-data.disable',
        'dashboard.list-management',
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
        $stoppedCandidate = $this->stoppedCandidateForCurrentUser();
        if ($stoppedCandidate) {
            $listLeaderName = trim((string) ($stoppedCandidate->stoppedByCandidate?->user?->name ?? 'غير معروف'));
            $message = 'تم إيقافك من قبل مرشح رئيس القائمة: ' . $listLeaderName;

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 403);
            }

            return redirect()->route('login')->withErrors(['login' => $message]);
        }

        try {
            $permission = Str::of($request->route()->getName())
                ->remove('dashboard.')
                ->replace('statement.search-modern', 'statement.search')
                ->replace('import-voters-data', 'import-voters')
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

    private function stoppedCandidateForCurrentUser(): ?Candidate
    {
        if (!admin()) {
            return null;
        }

        return Candidate::withoutGlobalScopes()
            ->with('stoppedByCandidate.user:id,name')
            ->where('user_id', (int) admin()->id)
            ->where('candidate_type', 'candidate')
            ->whereNotNull('list_leader_candidate_id')
            ->where('is_stopped', true)
            ->first();
    }

    private function listLeaderHasCandidatePermission(string $permission): bool
    {
        if (!admin()) {
            return false;
        }

        $isListLeaderUser = admin()->hasRole('مرشح رئيس قائمة')
            || Candidate::withoutGlobalScopes()
                ->where('user_id', (int) admin()->id)
                ->where('candidate_type', 'list_leader')
                ->exists();

        if (!$isListLeaderUser) {
            return false;
        }

        if (Str::startsWith($permission, 'candidates.')) {
            return true;
        }

        $candidateRole = Role::where('name', 'مرشح')->first();
        if (!$candidateRole) {
            return false;
        }

        return $candidateRole->hasPermissionTo($permission);
    }
}
