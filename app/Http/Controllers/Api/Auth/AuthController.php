<?php

namespace App\Http\Controllers\Api\Auth;

use Hash;
use App\Models\User;
use App\Events\RegisteredUserEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use App\Http\Requests\Auth\LoginRequest;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {

        $email = $request->validated('email');
        $password = $request->validated('password');

        $user = User::whereEmail($email)->first();

        if (!$user instanceof User || !Hash::check($password, $user->password)) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => "Adresse e-mail ou mot de passe incorrect"
            ]);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'status' => Response::HTTP_FORBIDDEN,
                'message' => "Veuillez confirmer votre adresse e-mail pour activer votre compte"
            ]);
        }

        $token = $user->createToken($email)->plainTextToken;

        $user->token = $token;

        return new AuthResource($user);

    }
}
