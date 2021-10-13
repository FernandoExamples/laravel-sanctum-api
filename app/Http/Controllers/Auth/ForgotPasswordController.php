<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function forgot()
    {
        $credentials = request()->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($credentials);

        $message = $status === Password::RESET_LINK_SENT ? 'Reset password link sent on your email.' : 'It not has been posible send a recovery email. Please try again later.';

        return response()->json(["msg" => $message]);
    }

    public function reset()
    {
        $credentials = request()->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $reset_password_status = Password::reset($credentials, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return "No existe un token para este usuario o el token ya ha expirado. Solicita un nuevo correo de recuperación";
        } else if ($reset_password_status == Password::PASSWORD_RESET) {
            return "La contraseña ha sido cambiada. Ya puedes cerrar esta pestaña.";
        } else {
            return back()->withErrors(['email' => [__($reset_password_status)]]);
        }
    }
}
