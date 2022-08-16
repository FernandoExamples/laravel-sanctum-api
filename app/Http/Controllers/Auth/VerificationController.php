<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class VerificationController extends Controller
{
    public function verify(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            throw new UnauthorizedHttpException("La url proporcionada es inválida o ha expirado.");
        }

        $user = User::findOrFail($id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return 'Email Verificado. Ya puedes cerrar esta pestaña';
    }

    public function resend()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            throw new BadRequestHttpException("Este email ya ha sido verificado");
        }

        auth()->user()->sendEmailVerificationNotification();

        return response()->json(["message" => "Se ha enviado un correo de verificación a tu correo electrónico."]);
    }
}
