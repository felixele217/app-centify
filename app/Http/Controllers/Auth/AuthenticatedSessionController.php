<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Admin;
use App\Models\Agent;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

// @codeCoverageIgnoreStart
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    //TODO test log out for both user groups
    //TODO test all auth routes for both users
    public function destroy(Request $request): RedirectResponse
    {
        match (get_class($request->user())) {
            Admin::class => Auth::guard('admin')->logout(),
            Agent::class => Auth::guard('admin')->logout(),
        };

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
// @codeCoverageIgnoreEnd
