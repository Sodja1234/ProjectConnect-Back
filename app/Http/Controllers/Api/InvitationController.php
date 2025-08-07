<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CandidacyResource;
use App\Models\Candidacy;
use App\Models\Invitation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class InvitationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/invitations/candidacies/{candidacyId}",
     *     summary="Validate or decline an invitation",
     *     tags={"Invitations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="candidacyId",
     *         in="path",
     *         required=true,
     *         description="ID of the candidacy to validate",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"accepted", "declined"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invitation status updated successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="This candidacy does not come from a valid invitation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Candidacy not found or already validated"
     *     )
     * )
     */
    public function validate(Request $request, $candidacyId)
    {
        // Valider le champ status en se basant sur les enum autorisé
        $request->validate([
            'status' => 'required|in:pending,accepted,declined',
        ]);

        $user = Auth::user();

        // Récupérer la candidature non validée de l'utilisateur connecté
        $candidacy = Candidacy::with(['projectRole.project.chat.users'])->where('id', $candidacyId)
            ->where('user_id', $user->id)
            ->where('is_validated', false)
            ->first();

        if (!$candidacy) {
            return response()->json([
                'message' => 'Candidature non trouvée ou déjà validée.'
            ], 404);
        }

        // Vérifier qu'une invitation correspondante existe
        $invitation = Invitation::where('email', $user->email)
            ->where('project_role_id', $candidacy->project_role_id)
            ->first();

        if (!$invitation) {
            return response()->json([
                'message' => 'Cette candidature ne provient pas d\'une invitation valide.'
            ], 403); // Accès refusé
        }

        DB::beginTransaction();
        try {
            $chat = $candidacy->projectRole->project->chat;
            $isCurrentlyInChat = $chat && $chat->users->contains($user->id);

            // Mettre à jour la candidature selon le statut
            if ($request->status === 'accepted') {
                $candidacy->update([
                    'is_validated' => true,
                    'status' => 'Invité', // Statut spécifique pour la candidature
                ]);

                // Ajouter l'utilisateur au chat du projet s'il n'y est pas déjà
                if ($chat && !$isCurrentlyInChat) {
                    $chat->users()->attach($user->id);

                    // Envoyer un message de bienvenue dans le chat
                    Message::create([
                        'chat_id' => $chat->id,
                        'sender_id' => $candidacy->projectRole->project->created_by,
                        'message' => "Bienvenue {$user->name} dans le projet en tant que {$candidacy->projectRole->role->name} !"
                    ]);
                }

            } elseif ($request->status === 'declined') {
                $candidacy->update([
                    'is_validated' => false,
                    'status' => 'Refusé', // Statut spécifique pour la candidature
                ]);

                // Retirer l'utilisateur du chat s'il y est
                if ($chat && $isCurrentlyInChat) {
                    $chat->users()->detach($user->id);

                    // Envoyer un message d'au revoir
                    Message::create([
                        'chat_id' => $chat->id,
                        'sender_id' => $candidacy->projectRole->project->created_by,
                        'message' => "{$user->name} a refusé l'invitation pour le rôle de {$candidacy->projectRole->role->name}."
                    ]);
                }
            }

            // Mettre à jour le statut de l'invitation
            $invitation->update(['status' => $request->status]);

            DB::commit();

            return response()->json([
                'message' => $request->status === 'accepted'
                    ? 'Invitation acceptée avec succès.'
                    : 'Invitation refusée.',
                'candidacy' => $candidacy,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur lors de la validation de l\'invitation: ' . $e->getMessage(), [
                'candidacy_id' => $candidacyId,
                'user_id' => $user->id,
                'request' => $request->all()
            ]);
            return response()->json([
                'message' => 'Une erreur est survenue lors de la validation de l\'invitation',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/invitations/candidacies",
     *     summary="Get pending invitations for the current user",
     *     tags={"Invitations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Candidacy")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $user = Auth::user();


        //Renvoyer uniquement les candidatures non validées venant des invitations en attente
        $candidacies = Candidacy::where('user_id', $user->id)
            ->where('is_validated', false)
            ->whereExists(function ($query) use ($user) {
                $query->select('*')
                    ->from('invitations')
                    ->whereColumn('invitations.project_role_id', 'candidacies.project_role_id')
                    ->where('invitations.email', $user->email)
                    ->where('invitations.status', 'pending');
            })
            ->get();

        return response()->json([
            'data' => CandidacyResource::collection($candidacies),
        ]);
    }
}
