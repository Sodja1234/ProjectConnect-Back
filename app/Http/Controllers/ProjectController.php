<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Skill;
use App\Models\Domain;
use App\Models\Project;
use App\Models\ProjectRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
      // Liste tous les projets avec relations
    public function index()
    {
        $projects = Project::with(['domains', 'projectRoles.role', 'projectRoles.skills'])->get();

        return response()->json($projects);
    }

   public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255|unique:projects',
        'description' => 'required|string',
        'date_start' => 'required|date',
        'date_end' => 'required|date|after:date_start',
        'budget' => 'nullable|numeric|min:0',
        'location' => 'nullable|string|max:255',
        'visibility' => 'in:public,private',
        'domains' => 'array',
        'domains.*' => 'string',
        'role_skills' => 'required|array',
        'role_skills.*.role' => 'required|string|max:255',
        'role_skills.*.skill' => 'required|array',
        'role_skills.*.skill.*' => 'required|string|max:255',
        'role_skills.*.description' => 'nullable|string',
    ]);

    $user = User::find(1);  //@TODO : ajout de auth pour recuperer l'utilisateur connecté

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
        $project = Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->date_start,
            'end_date' => $request->date_end,
            'budget' => $request->budget,
            'location' => $request->location,
            'visibility' => $request->visibility,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // Attacher les domaines
        if ($request->has('domains')) {
            $domainIds = collect($request->domains)->map(fn($name) =>
                Domain::firstOrCreate(['name' => $name])->id
            );
            $project->domains()->attach($domainIds);
        }

        // Gérer les rôles avec compétences
        foreach ($request->role_skills as $entry) {
            $role = Role::firstOrCreate(['name' => $entry['role']]);

            // Créer une entrée project_role
            $projectRole = ProjectRole::create([
                'project_id' => $project->id,
                'role_id' => $role->id,
                'description' => $entry['description'] ?? null,
            ]);

            // Attacher les skills à ce project_role
            $skillIds = collect($entry['skill'])->map(fn($name) =>
                Skill::firstOrCreate(['name' => $name])->id
            );
            $projectRole->skills()->attach($skillIds);
        }

        return response()->json([
            'message' => 'Projet créé avec succès.',
            'data' => $project->load('domains', 'roles'),
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erreur lors de la création du projet',
            'details' => $e->getMessage(),
        ], 500);
    }
}

    // Détail d'un projet avec relations
    public function show($id)
    {
        $project = Project::with(['domains', 'projectRoles.role', 'projectRoles.skills'])->find($id);

        if (!$project) {
            return response()->json(['error' => 'Projet non trouvé'], 404);
        }

        return response()->json($project);
    }

  
    // Mise à jour du projet et relations
    public function update(Request $request, $id)
    {
         //@TODO : ajout de la condition de modification
        $project = Project::find($id);

        if (!$project) {
            return response()->json(['error' => 'Projet non trouvé'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:projects,title,' . $project->id,
            'description' => 'required|string',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after:date_start',
            'budget' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'visibility' => 'in:public,private',
            'domains' => 'array',
            'domains.*' => 'string',
            'role_skills' => 'array',
            'role_skills.*.role' => 'required_with:role_skills|string|max:255',
            'role_skills.*.skill' => 'required_with:role_skills|array',
            'role_skills.*.skill.*' => 'required|string|max:255',
            'role_skills.*.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Mise à jour simple des champs du projet
            $project->update($request->only([
                'title', 'description', 'start_date', 'end_date', 'budget', 'location', 'visibility'
            ]));

            // Mettre à jour les domaines si fournis
            if ($request->has('domains')) {
                $domainIds = collect($request->domains)->map(fn($name) =>
                    Domain::firstOrCreate(['name' => $name])->id
                );
                $project->domains()->sync($domainIds);
            }

            // Mettre à jour les roles & skills si fournis
            if ($request->has('role_skills')) {
                // Supprimer d'abord les anciennes relations
                $project->projectRoles()->each(function ($pr) {
                    $pr->skills()->detach();
                    $pr->delete();
                });

                // Ajouter les nouvelles relations
                foreach ($request->role_skills as $entry) {
                    $role = Role::firstOrCreate(['name' => $entry['role']]);

                    $projectRole = ProjectRole::create([
                        'project_id' => $project->id,
                        'role_id' => $role->id,
                        'description' => $entry['description'] ?? null,
                    ]);

                    $skillIds = collect($entry['skill'])->map(fn($name) =>
                        Skill::firstOrCreate(['name' => $name])->id
                    );
                    $projectRole->skills()->attach($skillIds);
                }
            }

            return response()->json([
                'message' => 'Projet mis à jour avec succès.',
                'data' => $project->load('domains', 'projectRoles.role', 'projectRoles.skills'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la mise à jour du projet',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
 // Suppression d'un projet avec relations pivot
    public function destroy($id)
    {
        //@TODO : ajout de la condition de suppression
        $project = Project::find($id);

        if (!$project) {
            return response()->json(['error' => 'Projet non trouvé'], 404);
        }

        try {
            // Supprimer les relations pivot (project_roles et project_role_skill)
            $project->projectRoles()->each(function ($pr) {
                $pr->skills()->detach();
                $pr->delete();
            });

            // Supprimer les relations domaines
            $project->domains()->detach();

            // Supprimer le projet
            $project->delete();

            return response()->json(['message' => 'Projet supprimé avec succès.']);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la suppression du projet',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

}