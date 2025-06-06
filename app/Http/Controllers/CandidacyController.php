<?php

namespace App\Http\Controllers;

use App\Http\Resources\CandidacyResource;
use App\Models\Candidacy;
use App\Models\Project;
use App\Models\ProjectRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CandidacyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($projectId, Request $request)
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
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $query = Candidacy::query();

        // Filtre par nom de rôle
        if ($request->has('role_name')) {
            $query->whereHas('projectRole.role', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->role_name . '%');
            });
        }

        // Filtre par nom d'utilisateur
        if ($request->has('user_name')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user_name . '%');
            });
        }

        // Filtre par statut de validation
        if ($request->has('is_validated') && in_array($request->is_validated, [0, 1])) {
            $query->where('is_validated', $request->is_validated);
        }

        // Chargement des relations
        $query->with('projectRole.role', 'user');

        // Pagination (avec 10 éléments par page par défaut)
        $perPage = $request->per_page ?? 10;
        $candidacies = $query->paginate($perPage);

        return response()->json([
            "data" => CandidacyResource::collection($candidacies),
            "meta" => [
                "current_page" => $candidacies->currentPage(),
                "last_page" => $candidacies->lastPage(),
                "per_page" => $candidacies->perPage(),
                "total" => $candidacies->total(),
            ]
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        try {
            $projectRole = ProjectRole::findOrFail($id);

            $existing = Candidacy::where('user_id', auth()->id())
                ->where('project_role_id', $projectRole->id)
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Vous avez déjà postulé à ce rôle.'
                ], 422);
            }

            $candidacy = Candidacy::create([
                'user_id' => auth()->id(),
                'project_role_id' => $projectRole->id,
            ]);

            return response()->json([
                'message' => 'Candidature soumise avec succès.',
                'data' => $candidacy,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Erreur Candidacy store: ' . $e->getMessage());
            return response()->json([
                'message' => 'Une erreur est survenue.',
            ], 500);
        }
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
}
