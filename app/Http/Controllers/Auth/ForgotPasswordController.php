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

        Password::sendResetLink($credentials);

        return response()->json(["msg" => 'Reset password link sent on your email.']);
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
            return "Este token ya ha expirado. Solicita un nuevo correo de recuperación";
        }

        return "La contraseña ha sido cambiada. Ya puedes cerrar esta pestaña.";
    }
}
