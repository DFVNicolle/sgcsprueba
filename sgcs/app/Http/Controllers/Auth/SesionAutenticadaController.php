<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SesionAutenticadaController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.iniciar-sesion');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();
        // Si el usuario tiene 2FA activo, redirigir a la verificación
        if ($user->google2fa_secret) {
            $request->session()->put('2fa:user:id', $user->id);
            Auth::logout();
            return redirect()->route('auth.2fa');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
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
