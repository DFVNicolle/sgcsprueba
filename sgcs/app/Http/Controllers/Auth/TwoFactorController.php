<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class TwoFactorController extends Controller
{
    public function showForm(Request $request)
    {
        return view('auth.2fa');
    }

    public function verify(Request $request)
    {
        $request->validate(['codigo' => 'required|string']);
        $userId = $request->session()->get('2fa:user:id');
        $user = Usuario::find($userId);
        if (!$user || !$user->google2fa_secret) {
            return redirect()->route('login')->withErrors(['email' => 'Usuario o 2FA inválido.']);
        }
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        if ($google2fa->verifyKey($user->google2fa_secret, $request->input('codigo'))) {
            Auth::login($user);
            $request->session()->forget('2fa:user:id');
            return redirect()->intended(route('dashboard', absolute: false));
        } else {
            return back()->withErrors(['codigo' => 'Código 2FA incorrecto.']);
        }
    }
}
