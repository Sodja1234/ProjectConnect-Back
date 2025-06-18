<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Resources\AuthResource;
use App\Models\Candidacy;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmailController extends Controller
{

    public function __invoke(int $id, string $hash,Request $request): JsonResponse|AuthResource
    {

        $token = $request->query('token');
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
        $invitation =Invitation::where('token', $token)->first();

        if ($invitation) {

            Candidacy::create([
                'user_id' => $user->id,
                'project_role_id'=> $invitation->project_role_id,
                'is_validated'=>true,
                'status'=>'Invité'

            ]);

            $invitation->delete();
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
