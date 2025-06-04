<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->validate(
            [
                'captcha' => 'required|numeric',
            ],
            [
                'captcha.required' => 'El captcha és obligatori.',
                'captcha.numeric' => 'El captcha ha de ser un número.',
            ]
        );

        if ((int) $request->captcha !== (int) session('captcha_answer')) {
            return back()->withErrors(['captcha' => 'Captcha incorrecte. Torna-ho a intentar.']);
        }

        $request->authenticate();
        if (in_array($request->user()->status, ['rejected', 'pending'])) {

            if ($request->user()->status === 'rejected') {
                Auth::guard('web')->logout();
                return redirect()->intended('/rejectedUser');
            } else {
                Auth::guard('web')->logout();
                return redirect()->intended('/pendingUser');
            }
        }
        $request->session()->regenerate();

        return ($request->user()->is_admin())
            ? redirect()->intended('/afiliats')
            : redirect()->intended('/info-afiliat');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
