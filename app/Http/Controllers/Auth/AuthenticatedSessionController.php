<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
		  $previousUrl = url()->previous();
    session(['previous_url' => $previousUrl]);
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
public function showLoginForm()
{
    // Store the previous URL in the session for debugging
    $previousUrl = url()->previous();
    session(['previous_url' => $previousUrl]);

    return view('auth.login');
}



public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $request->session()->regenerate();

    // Retrieve the previous URL from the session
    $previousUrl = session('previous_url', '/');

    return redirect($previousUrl);
}




    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
