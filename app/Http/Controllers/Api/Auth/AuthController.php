<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Resources\AuthResource;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $validation->errors(),
                'message' => "Veuillez corriger les erreurs dans le formulaire."
            ]);
        }

        $email = $request->request->get('email');
        $password = $request->request->get('password');

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
