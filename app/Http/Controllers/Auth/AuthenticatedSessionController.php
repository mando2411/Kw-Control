<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $guard = null;
        if ($request->filled('login_as_client')) {
            $guard = 'client_web';
            config()->set('auth.defaults', $guard);
        }
        $request->authenticate($guard);

        $request->session()->regenerate();

        if ($guard == 'client_web') {

            setcookie(
                'token',
                auth('client_web')->user()->createToken('api')->accessToken,
                now()->addHours(2)->unix()
            );
            return redirect()->intended('/api/documentation');
        }

        if(auth()->user()->representatives()->exists() && auth()->user()->representatives()->get()[0]->status == false ){
            return redirect()->route('change-password');
        }
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $guard = auth()->user() instanceof User ? 'web' : 'client_web';

        Auth::guard($guard)->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $this->cleanCookies();

        return redirect('/');
    }

    private function cleanCookies()
    {
        if (isset($_COOKIE['token'])) {
            unset($_COOKIE['token']);
            setcookie('token', '', time() - 3600, '/');
        }
    }
}
