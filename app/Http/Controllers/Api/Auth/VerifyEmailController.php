<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Resources\AuthResource;
use App\Models\Candidacy;
use App\Models\Invitation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmailController extends Controller
{

    public function __invoke(int $id, string $hash, Request $request): JsonResponse|AuthResource
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

        DB::beginTransaction();
        try {
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }

            $invitation = Invitation::where('token', $token)->first();

            if ($invitation) {
                // Créer la candidature
                $candidacy = Candidacy::create([
                    'user_id' => $user->id,
                    'project_role_id' => $invitation->project_role_id,
                    'is_validated' => true,
                    'status' => 'Invité'
                ]);

                // Charger les relations nécessaires
                $candidacy->load(['projectRole.project.chat.users']);

                // Ajouter l'utilisateur au chat du projet
                $chat = $candidacy->projectRole->project->chat;
                if ($chat && !$chat->users->contains($user->id)) {
                    $chat->users()->attach($user->id);

                    // Envoyer un message de bienvenue dans le chat
                    Message::create([
                        'chat_id' => $chat->id,
                        'sender_id' => $candidacy->projectRole->project->created_by,
                        'message' => "Bienvenue {$user->name} dans le projet en tant que {$candidacy->projectRole->role->name} !"
                    ]);
                }

                // Mettre à jour le statut de l'invitation
                $invitation->update(['status' => 'accepted']);
            }

            DB::commit();

            return $this->getAuthUser($user);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur lors de la vérification email et traitement de l\'invitation: ' . $e->getMessage(), [
                'user_id' => $id,
                'token' => $token
            ]);

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Une erreur est survenue lors du traitement'
            ]);
        }
    }
    private function getAuthUser(User $user): AuthResource
    {
        $token = $user->createToken($user->email)->plainTextToken;

        $user->token = $token;

        return new AuthResource($user);
    }
}
