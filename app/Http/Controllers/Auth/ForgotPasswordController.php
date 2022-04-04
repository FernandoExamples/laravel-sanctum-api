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

        $messages = [
            Password::RESET_LINK_SENT => 'Se te ha enviado un correo para restablecer tu contraseña.',
            Password::RESET_THROTTLED => 'Ya has solicitado previamente un correo de recuperación de contraseña.',
            Password::INVALID_USER => 'No existe un usuario registrado con este correo.',
        ];

        $message = $messages[$status];

        if ($status != Password::RESET_LINK_SENT) return abort(400, $message);

        return response()->json(["message" => $message]);
    }

    public function reset()
    {
        $credentials = request()->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed'
        ], [
            'email.exists' => 'No existe un usuario registrado con este correo',
            'password.confirmed' => 'Las contraseñas no coinciden'
        ]);

        $reset_password_status = Password::reset($credentials, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        $messages = [
            Password::INVALID_TOKEN => 'No existe un token para este usuario o el token ya ha expirado. Solicita un nuevo correo de recuperación.',
            Password::PASSWORD_RESET => 'La contraseña ha sido cambiada. Ya puedes cerrar esta pestaña.',
            Password::INVALID_USER => 'No existe un usuario registrado con este correo.',
        ];

        $message = $messages[$reset_password_status];

        return $message;
    }
}
