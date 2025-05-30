<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Events\RegisteredUserEvent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class RegisterController extends Controller
{

    public function __invoke(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);


        if ($validation->fails()) {
            return response()->json([
                'status' => HttpResponse::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $validation->errors(),
                'message' => "Veuillez corriger les erreurs dans le formulaire."
            ]);
        }


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
        ]);

        event(new RegisteredUserEvent($user));

        return response()->json([
            'status' => HttpResponse::HTTP_CREATED,
        ]);
    }
}
