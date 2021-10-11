<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(["msg" => "Invalid/Expired url provided."], 401);
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
            return response()->json(["msg" => "Email already verified."], 400);
        }

        auth()->user()->sendEmailVerificationNotification();

        return response()->json(["msg" => "Email verification link sent on your email"]);
    }
}
