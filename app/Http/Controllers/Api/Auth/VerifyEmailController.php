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

    public function __invoke(int $id, string $hash): JsonResponse|AuthResource
    {
        $user = User::findOrFail($id);

        if (sha1($user->email) !== $hash) {
            return response()->json([
                'status' => Response::HTTP_FOUND,
                'message' => 'Le lien de vérification est invalide ou a été modifié.'
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->getAuthUser($user);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->getAuthUser($user);

    }

    private function getAuthUser(User $user): AuthResource
    {
        $token = $user->createToken($user->email)->plainTextToken;

        $user->token = $token;

        return new AuthResource($user);
    }
}
