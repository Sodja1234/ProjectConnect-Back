<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CandidacyResource;
use App\Models\Candidacy;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class InvitationController extends Controller
{
    //validation ou rejet de la candidature
    public function validate(Request $request, $candidacyId)
    {
        // Valider le champ status en se basant sur les enum autorisé
        $request->validate([
            'status' => 'required|in:pending,accepted,declined',
        ]);

        $user = auth()->user();

        // Récupérer la candidature non validée de l'utilisateur connecté
        $candidacy = Candidacy::where('id', $candidacyId)
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

        // Mettre à jour la candidature selon le statut
        if ($request->status === 'accepted') {
            $candidacy->update([
                'is_validated' => true,
                'status' => 'Invité', // Statut spécifique pour la candidature
            ]);
        } elseif ($request->status === 'declined') {
            $candidacy->update([
                'is_validated' => false,
                'status' => 'Refusé', // Statut spécifique pour la candidature
            ]);
        }

        // Mettre à jour le statut de l'invitation
        $invitation->update(['status' => $request->status]);

        return response()->json([
            'message' => $request->status === 'accepted'
                ? 'Invitation acceptée avec succès.'
                : 'Invitation refusée.',
            'candidacy' => $candidacy,
        ]);
    }

    public function index()
    {
        $user = auth()->user();


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
            'data' =>CandidacyResource::collection($candidacies),
        ]);
    }




}
