<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerfilUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PerfilController extends Controller
{
    /**
     * Update the user's profile information.
     */
    public function update(PerfilUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill([
            'nombre_completo' => $request->validated()['name'],
            'correo' => $request->validated()['correo'],
        ]);

        if ($request->user()->isDirty('correo')) {
            $request->user()->correo_verificado_en = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
    /**
     * Confirmar y activar 2FA verificando el código OTP.
     */
    public function confirmar2fa(Request $request)
    {
        $user = $request->user();
        $codigo = $request->input('codigo');
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $secret = $request->session()->get('google2fa_secret_temp');

        if (!$secret) {
            return redirect()->route('profile.edit')->with('error', 'No se ha generado una clave 2FA.');
        }

        if ($google2fa->verifyKey($secret, $codigo)) {
            // Guardar el secreto solo si el código es válido
            $user->google2fa_secret = $secret;
            $user->save();
            $request->session()->forget('google2fa_secret_temp');
            return redirect()->route('profile.edit')->with('success', '2FA activado correctamente.');
        } else {
            // Código incorrecto
            return redirect()->back()->with('error', 'El código ingresado no es válido. Intenta nuevamente.');
        }
    }

    /**
     * Activar 2FA para el usuario actual.
     */
    public function activar2fa(Request $request)
    {
        $user = $request->user();
        if ($user->google2fa_secret) {
            return redirect()->back()->with('error', '2FA ya está activado.');
        }

        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $secret = $google2fa->generateSecretKey();

        // Mostrar QR para escanear, pero NO guardar aún
        $qrUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->correo,
            $secret
        );

        // Guardar el secreto temporalmente en la sesión
        $request->session()->put('google2fa_secret_temp', $secret);

        return view('perfil.activar2fa', compact('qrUrl', 'secret'));
    }

    /**
     * Desactivar 2FA para el usuario actual.
     */
    public function desactivar2fa(Request $request)
    {
        $user = $request->user();
        $user->google2fa_secret = null;
        $user->save();
        return redirect()->back()->with('success', '2FA desactivado correctamente.');
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('perfil.editar', [
            'user' => $request->user(),
        ]);
    }
    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
