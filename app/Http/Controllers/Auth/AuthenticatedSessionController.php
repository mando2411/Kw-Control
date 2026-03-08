<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Candidate;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $loginCandidates = Candidate::withoutGlobalScopes()
            ->with(['user:id,name,image'])
            ->latest('id')
            ->limit(12)
            ->get()
            ->filter(fn ($candidate) => !empty(optional($candidate->user)->name))
            ->map(function ($candidate) {
                $name = trim((string) $candidate->user->name);
                $slug = Str::slug($name ?: ('candidate-' . $candidate->id)) . '-' . $candidate->id;

                return [
                    'name' => $name,
                    'image' => $candidate->user->image,
                    'profile_url' => route('candidates.public-profile', ['slug' => $slug]),
                ];
            })
            ->values();

        return view('auth.login', compact('loginCandidates'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse|JsonResponse
    {
        $guard = null;
        if ($request->filled('login_as_client')) {
            $guard = 'client';
            config()->set('auth.defaults', $guard);
        }
        $request->authenticate($guard);

        if ($guard !== 'client' && $this->candidateStopColumnsAvailable()) {
            $stoppedCandidate = Candidate::withoutGlobalScopes()
                ->with('stoppedByCandidate.user:id,name')
                ->where('user_id', (int) auth()->id())
                ->where('candidate_type', 'candidate')
                ->whereNotNull('list_leader_candidate_id')
                ->where('is_stopped', true)
                ->first();

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

                return back()
                    ->withInput($request->only('login'))
                    ->withErrors(['login' => $message]);
            }
        }

        $request->session()->regenerate();

        if ($guard == 'client') {

            setcookie(
                'token',
                auth('client')->user()->createToken('api')->accessToken,
                now()->addHours(2)->unix()
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'user' => [
                        'name' => auth('client')->user()->name,
                        'image' => auth('client')->user()->image,
                    ],
                    'redirect' => url('/api/documentation'),
                ]);
            }

            return redirect()->intended('/api/documentation');
        }

        if ($this->shouldRedirectRepresentativeToProfileForPasswordReset()) {
            $warningMessage = 'يرجى تغيير كلمة السر الافتراضية وهي "1" قبل متابعة استخدام النظام.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'user' => [
                        'name' => auth()->user()->name,
                        'image' => auth()->user()->image,
                    ],
                    'message' => $warningMessage,
                    'redirect' => route('profile.edit'),
                ]);
            }

            session()->flash('message', $warningMessage);
            session()->flash('type', 'warning');

            return redirect()->route('profile.edit');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'user' => [
                    'name' => auth()->user()->name,
                    'image' => auth()->user()->image,
                ],
                'redirect' => url(RouteServiceProvider::HOME),
            ]);
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $guard = auth()->user() instanceof User ? 'web' : 'client';

        Auth::guard($guard)->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $this->cleanCookies();

        return redirect('/');
    }

    private function candidateStopColumnsAvailable(): bool
    {
        static $checked = false;
        static $available = false;

        if ($checked) {
            return $available;
        }

        $checked = true;

        try {
            $available = Schema::hasColumns('candidates', [
                'is_stopped',
                'stopped_by_candidate_id',
                'stopped_at',
            ]);
        } catch (\Throwable $exception) {
            $available = false;
        }

        return $available;
    }

    private function cleanCookies()
    {
        if (isset($_COOKIE['token'])) {
            unset($_COOKIE['token']);
            setcookie('token', '', time() - 3600, '/');
        }
    }

    private function shouldRedirectRepresentativeToProfileForPasswordReset(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        $isRepresentative = $user->hasRole('مندوب') || $user->representatives()->exists();
        if (!$isRepresentative) {
            return false;
        }

        return Hash::check('1', (string) $user->password);
    }
}
