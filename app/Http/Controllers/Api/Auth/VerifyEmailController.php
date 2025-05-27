<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmailController extends Controller
{

    public function __invoke(int $id): JsonResponse|AuthResource
    {
        $user = User::findOrFail($id);


        if ($user->hasVerifiedEmail()) {
            return response()->json(['status' => Response::HTTP_GONE]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }


        $token = $user->createToken($user->email)->plainTextToken;

        $user->token = $token;

        return new AuthResource($user);

    }
}
