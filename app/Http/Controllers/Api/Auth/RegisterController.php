<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\Candidacy;
use App\Models\Invitation;
use App\Models\User;
use App\Events\RegisteredUserEvent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class RegisterController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password"),
     *             @OA\Property(property="token", type="string", example="some-invitation-token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function __invoke(RegisterUserRequest $request)
    {
        $name = $request->validated('name');
        $token = $request->validated('token');

        $user = User::create([
            'name' => $name,
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'role'=>$request->role??'user',
        ]);

        $user->update([
            'slug' => Str::slug(sprintf("%s-%s", $name, $user->id))
        ]);


        event(new RegisteredUserEvent($user, $token,$user->otp));

        return response()->json([
            'status' => HttpResponse::HTTP_CREATED,
        ]);
    }
}
