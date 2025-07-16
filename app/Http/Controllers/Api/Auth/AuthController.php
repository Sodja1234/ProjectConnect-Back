<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Events\RegisteredUserEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use App\Http\Requests\Auth\LoginRequest;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate user and return a token",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="test@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentication successful",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invalid credentials"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Email not verified"
     *     )
     * )
     */
    public function __invoke(LoginRequest $request)
    {

        $email = $request->validated('email');
        $password = $request->validated('password');

        $user = User::where('email', $email)->first();

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
