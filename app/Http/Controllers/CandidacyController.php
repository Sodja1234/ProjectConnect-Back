<?php

namespace App\Http\Controllers;

use App\Http\Resources\CandidacyResource;
use App\Http\Resources\InvitationResource;
use App\Mail\ProjectInvitationMail;
use App\Models\Candidacy;
use App\Models\Invitation;
use App\Models\Project;
use App\Models\ProjectRole;
use App\Models\User;
use App\Notifications\JobApplicationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CandidacyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($projectId, Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['message' => 'Projet non trouvé'], 404);
        }

        if ($project->created_by !== $user->id) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        // Requête principale filtrée
        $query = Candidacy::whereHas('projectRole', function ($q) use ($projectId) {
            $q->where('project_id', $projectId);
        });

        if ($request->has('role_name')) {
            $query->whereHas('projectRole.role', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->role_name . '%');
            });
        }

        if ($request->has('user_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user_name . '%');
            });
        }

        if ($request->has('is_validated') && in_array($request->is_validated, [0, 1])) {
            $query->where('is_validated', $request->is_validated);
        }

        $query->with(['projectRole.role', 'user']);

        // Pagination
        $perPage = $request->per_page ?? 10;
        $candidacies = $query->paginate($perPage);

        // Total par rôle
        $roleCounts = Candidacy::whereHas('projectRole', function ($q) use ($projectId) {
            $q->where('project_id', $projectId);
        })
            ->with('projectRole.role')
            ->get()
            ->groupBy(function ($candidacy) {
                return $candidacy->projectRole->role->name ?? 'Unknown';
            })
            ->map(function ($group) {
                return count($group);
            });

        return response()->json([
            "data" => CandidacyResource::collection($candidacies),
            "meta" => [
                "current_page" => $candidacies->currentPage(),
                "last_page" => $candidacies->lastPage(),
                "per_page" => $candidacies->perPage(),
                "total" => $candidacies->total(),
            ],
            "totals_by_role" => $roleCounts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        try {
            $user = $request->user();

            // Récupération du ProjectRole avec son projet associé
            $projectRole = ProjectRole::with('project')->findOrFail($id);

            // Vérifie si l'utilisateur a déjà postulé au même rôle dans le même projet
            $existing = Candidacy::where('user_id', $user->id)
                ->whereHas('projectRole', function ($query) use ($projectRole) {
                    $query->where('role_id', $projectRole->role_id)
                        ->where('project_id', $projectRole->project_id);
                })
                ->exists();

            if ($existing) {
                return response()->json([
                    'message' => 'Vous avez déjà postulé à ce rôle dans ce projet.'
                ], 422);
            }

            // Création de la nouvelle candidature
            $candidacy = Candidacy::create([
                'user_id' => $user->id,
                'project_role_id' => $projectRole->id,
                'status' => 'En attente',
            ]);

            $user->notify(new JobApplicationNotification($candidacy, $projectRole));

            return response()->json([
                'message' => 'Candidature soumise avec succès.',
                'data' => $candidacy,
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Erreur Candidacy store: ' . $e->getMessage());
            return response()->json([
                'message' => 'Une erreur est survenue.'.' '.$e->getMessage(),
            ], 500);
        }
    }
    public function invite(Request $request, $projectRoleId)
    {
        $token = Str::random(60);
        $request->validate([
            'email' => 'required|email',
        ]);

        $projectRole = ProjectRole::with('project', 'role')->findOrFail($projectRoleId);
        $mail = $request->email;

        // Vérification des droits d'accès
        if ($projectRole->project->created_by !== auth()->user()->id) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $existingUser = User::where('email', $request->email)->first();

        // Vérification d'une éventuelle candidature ou invitation existante
        if ($existingUser) {
            $already = Candidacy::where('user_id', $existingUser->id)
                ->where('project_role_id', $projectRoleId)
                ->exists();

            if ($already) {
                return response()->json(['message' => 'Utilisateur déjà candidat ou invité.'], 422);
            }
        }

        // Création de l'invitation (unique pour les deux cas)
        $invitation = Invitation::create([
            'email' => $request->email,
            'project_role_id' => $projectRoleId,
            'token' => $token,
            'status' => 'pending',
            'created_by' => auth()->user()->id,
        ]);

        if ($existingUser) {
            // Si l'utilisateur existe, créer une candidature et envoyer une notification
            $candidacy = Candidacy::create([
                'user_id' => $existingUser->id,
                'project_role_id' => $projectRoleId,
                'is_validated' => false,
                'status' => 'Invité en attente',
            ]);

            $existingUser->notify(new JobApplicationNotification($candidacy, $projectRole));
        } else {
            // Si l'utilisateur n'existe pas, envoyer un email d'invitation
            Mail::to($mail)->send(new ProjectInvitationMail($mail, $projectRole, $token));

        }

        return response()->json(['message' => 'Invitation envoyée avec succès.']);
    }



    /**
     * Display the specified resource.
     */
    public function show(Candidacy $candidacy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Candidacy $candidacy)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidacy $candidacy)
    {
        //
    }
    /**
     * Validate a candidacy (only project owner can do this)
     */
    public function validateCandidacy(Request $request, $candidacyId)
    {

        $validator =Validator::make($request->all(),[
            'status'=>'required|in:accepted,declined'

        ]);
        if ($validator->fails()) {
            return response()->json(["error"=>$validator->errors()], 422);
        }
        try {
            $user = auth()->user();

            $candidacy = Candidacy::with(['projectRole.project'])->findOrFail($candidacyId);

            // Vérifier que l'utilisateur est bien le créateur du projet
            if ($candidacy->projectRole->project->created_by !== $user->id) {
                return response()->json(['message' => 'Seul le propriétaire du projet peut valider cette candidature'], 403);
            }

            // Valider la candidature
            if ($request->status === 'accepted') {
                $candidacy->update([
                    'is_validated' => true,
                    'status' => 'Accepté',
                ]);
            } elseif ($request->status === 'declined') {
                $candidacy->update([
                    'is_validated' => false,
                    'status' => 'Refusé',
                ]);
            }

            // Notifier l'utilisateur dont la candidature a été validée
            $candidacy->user->notify(new JobApplicationNotification($candidacy, $candidacy->projectRole));

            return response()->json([
                'message' => 'Candidature traitée avec succès',
                'data' => new CandidacyResource($candidacy)
            ]);

        } catch (\Throwable $e) {
            Log::error('Erreur lors de la validation de la candidature: ' . $e->getMessage());
            return response()->json([
                'message' => 'Une erreur est survenue lors de la validation de la candidature'
            ], 500);
        }
    }

    public function pendingInvitations($projectId, Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Authentification requise'], 401);
        }

        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['message' => 'Projet non trouvé'], 404);
        }

        if ($project->created_by !== $user->id) {
            return response()->json(['message' => 'Accès refusé : vous n\'êtes pas le propriétaire du projet'], 403);
        }

        // Requête principale filtrée
        $query = Invitation::where('status', 'pending')
            ->whereHas('projectRole', function ($q) use ($projectId) {
                $q->where('project_id', $projectId);
            });

        // Filtres spécifiques
        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->has('role')) {
            $query->whereHas('projectRole.role', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->role . '%');
            });
        }

        // Recherche globale
        if ($request->has('search')) {
            $query->where(function ($subQuery) use ($request) {
                $subQuery->where('email', 'like', '%' . $request->search . '%')
                    ->orWhereHas('projectRole.role', function ($r) use ($request) {
                        $r->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $query->with(['projectRole.role']);

        // Pagination
        $perPage = $request->per_page ?? 10;
        $invitations = $query->paginate($perPage);

        // Total par rôle
        $roleCounts = Invitation::where('status', 'pending')
            ->whereHas('projectRole', function ($q) use ($projectId) {
                $q->where('project_id', $projectId);
            })
            ->with('projectRole.role')
            ->get()
            ->groupBy(function ($invitation) {
                return $invitation->projectRole->role->name ?? 'Unknown';
            })
            ->map(function ($group) {
                return count($group);
            });

        return response()->json([
            "data" => InvitationResource::collection($invitations),
            "meta" => [
                "current_page" => $invitations->currentPage(),
                "last_page" => $invitations->lastPage(),
                "per_page" => $invitations->perPage(),
                "total" => $invitations->total(),
            ],
            "totals_by_role" => $roleCounts
        ]);
    }



    public function cancelInvitation($invitationId, Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Authentification requise'], 401);
            }

            $invitation = Invitation::with('projectRole.project')->findOrFail($invitationId);

            // Vérifie que l'utilisateur est bien le propriétaire du projet
            if ($invitation->projectRole->project->created_by !== $user->id) {
                return response()->json(['message' => 'Seul le propriétaire du projet peut annuler cette invitation'], 403);
            }

            // Vérifie que l'invitation est encore en attente
            if ($invitation->status !== 'pending') {
                return response()->json(['message' => 'Seules les invitations en attente peuvent être annulées'], 422);
            }

            $invitation->delete();

            return response()->json(['message' => 'Invitation annulée avec succès.']);

        } catch (\Throwable $e) {

            return response()->json(['message' => 'Une erreur est survenue.'.$e->getMessage()], 500);
        }
    }








}
