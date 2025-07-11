<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegisteredUserMail;
use App\Models\User;
use Illuminate\Http\Request;



class OtpVerifyEmailController extends Controller
{
    public function verify(Request $request){

        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',

        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user->email_otp ||now()->greaterThan($user->email_otp_expires_at)){
            return response()->json(['message' => 'Code OTP expiré ou non généré'], 400);
        }
        if($request->otp != $user->email_otp) {
            return response()->json(['message' => 'Code OTP invalide'], 401);
        }
        $user->email_verified_at = now();
        $user->email_otp = null;
        $user->email_otp_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Adresse e-mail verifiée avec succès']);

    }

    public function resend(Request $request){

        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        //Generer le code OTP
        $otp = rand(100000, 999999);
        $user->email_otp = $otp;
        $user->email_otp_expires_at = now()->addMinutes(10);
        $user->save();


        //Envoyer le code OTP par email
        \App\Mail::to($user->email)->send(new RegisteredUserMail($user, $otp,$user->token));

        return response()->json(['message' => 'Code OTP envoyé par email']);

    }
}
