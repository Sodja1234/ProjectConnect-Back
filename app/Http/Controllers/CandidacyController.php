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

        $query = Candidacy::with(['user', 'projectRole.role'])
            ->whereHas('projectRole', fn($q) => $q->where('project_id', $projectId));

        // Filtres
        if ($request->has('role_name')) {
            $query->whereHas('projectRole.role', fn($q) =>
            $q->where('name', 'like', '%'.$request->role_name.'%'));
        }

        if ($request->has('user_name')) {
            $query->whereHas('user', fn($q) =>
            $q->where('name', 'like', '%'.$request->user_name.'%'));
        }

        if ($request->has('is_validated')) {
            $query->where('is_validated', $request->is_validated);
        }

        // Pagination
        $perPage = $request->per_page ?? 15;
        $candidacies = $query->paginate($perPage);

        return response()->json([
            'message' => "Candidatures pour le projet: {$project->name}",
            'meta' => [
                'total' => $candidacies->total(),
                'per_page' => $candidacies->perPage(),
                'current_page' => $candidacies->currentPage(),
                'last_page' => $candidacies->lastPage(),
                'from' => $candidacies->firstItem(),
                'to' => $candidacies->lastItem(),
                'links' => [
                    'first' => $candidacies->url(1),
                    'last' => $candidacies->url($candidacies->lastPage()),
                    'prev' => $candidacies->previousPageUrl(),
                    'next' => $candidacies->nextPageUrl(),
                ],
            ],
            'filters' => $request->only(['role_name', 'user_name', 'is_validated', 'per_page']),
            'data' => CandidacyResource::collection($candidacies)
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
