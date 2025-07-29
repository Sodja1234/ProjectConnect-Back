<?php

namespace App\Http\Controllers\Api;


use App\Models\Chat;
use App\Models\Message;
use App\Models\Skill;

use App\Models\Role;

use App\Models\Domain;
use App\Models\Project;
use App\Models\ProjectRole;
use App\Services\SearchableService;
use Illuminate\Http\Request;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;





use App\Models\User;





class ProjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/projects",
     *     summary="Get a list of projects",
     *     tags={"Projects"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by title, description, or domain",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Project")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = 10;

        $query = Project::with([
            'domains',
            'projectRoles.skills',
            'projectRoles.role',
            'user'
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhereHas('domains', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });
        }


        $projects = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return ProjectResource::collection($projects);
    }

    /**
     * @OA\Post(
     *     path="/api/projects",
     *     summary="Create a new project",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Project")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Project created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Project")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
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

        $user = $request->user();

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Création du projet
            $project = Project::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'description' => $request->description,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'budget' => $request->budget,
                'location' => $request->location,
                'status_id' => 1, // En cours par défaut
                'visibility' => $request->visibility,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            // Attacher les domaines
            if ($request->has('domains')) {
                $domainIds = collect($request->domains)->map(
                    fn($name) => Domain::firstOrCreate(['name' => $name])->id
                );
                $project->domains()->attach($domainIds);
            }

            // Gérer les rôles avec compétences
            foreach ($request->role_skills as $entry) {
                $role = Role::firstOrCreate(['name' => $entry['role']]);

                $projectRole = ProjectRole::create([
                    'project_id' => $project->id,
                    'role_id' => $role->id,
                    'description' => $entry['description'] ?? null,
                ]);

                $skillIds = collect($entry['skill'])->map(
                    fn($name) => Skill::firstOrCreate(['name' => $name])->id
                );
                $projectRole->skills()->attach($skillIds);
            }

            // Création du chat de groupe lié au projet
            $groupChat = Chat::create([
                'type' => 'group',
                'name' => 'Équipe '.$project->title,
                'project_id' => $project->id
            ]);

            // Ajout automatique du créateur au chat
            $groupChat->users()->attach($user->id);

            // Message de bienvenue automatique
            Message::create([
                'chat_id' => $groupChat->id,
                'sender_id' => $user->id,
                'message' => "J'ai créé ce projet '{$project->title}'. Discutons-en ici !"
            ]);

            return response()->json([
                'message' => 'Projet et chat de groupe créés avec succès.',
                'data' => [
                    'project' => $project->load('domains', 'projectRoles.role', 'projectRoles.skills'),
                    'chat' => $groupChat // Retourne les infos du chat créé
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la création du projet',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/projects/{slug}",
     *     summary="Get a specific project",
     *     tags={"Projects"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the project",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Project")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found"
     *     )
     * )
     */
    public function show($slug)
    {
        $project = Project::with(['domains', 'projectRoles', 'projectRoles.skills', 'projectRoles.role', 'user'])
            ->where('slug', $slug)
            ->first();

        if (!$project) {
            return response()->json(['error' => 'Projet non trouvé'], 404);
        }

        return new ProjectResource($project);
    }


    /**
     * @OA\Put(
     *     path="/api/projects/{slug}",
     *     summary="Update a project",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the project",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Project")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Project")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $slug)
    {

        $user = Auth::user();

        $project = Project::where('slug', $slug)->first();

        if (!$project) {
            return response()->json(['error' => 'Projet non trouvé'], 404);
        }
        if ($user->id !== $project->created_by) {
            return response()->json([
                'error' => 'Unauthorized'
            ]);
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
            $project->update([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'description' => $request->description,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'budget' => $request->budget,
                'location' => $request->location,
                'visibility' => $request->visibility,
                'updated_by' => $user->id
            ]);

            // Mettre à jour les domaines si fournis
            if ($request->has('domains')) {
                $domainIds = collect($request->domains)->map(
                    fn($name) =>
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

                    $skillIds = collect($entry['skill'])->map(
                        fn($name) =>
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
    /**
     * @OA\Delete(
     *     path="/api/projects/{slug}",
     *     summary="Delete a project",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the project",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found"
     *     )
     * )
     */
    public function destroy($slug)
    {

        $user = Auth::user();

        $project = Project::where('slug', $slug)->first();
        if ($user->id !== $project->created_by) {
            return response()->json([
                'error' => 'Unauthorized'
            ]);
        }

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

    /**
     * @OA\Get(
     *     path="/api/users/projects/participed",
     *     summary="Get projects a user has participated in",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Project")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function projectparticiped(SearchableService $searchableService)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(["error" => "vous n'êtes pas connecté"], 401);
        }

        $query = Project::query()
            ->whereHas('projectRoles', function ($query) use ($user) {
                $query->whereHas('candidacies', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            });

        $builder = $searchableService->handle($query, ['id', 'title', 'created_at', 'updated_at'], [
            'projectRoles' => ['id', 'role_id'],
            'domains' => ['name'],

        ]);
        $projects = $builder->paginate(1);

        return ProjectResource::collection($projects);
    }


    /**
     * @OA\Get(
     *     path="/api/users/projects",
     *     summary="Get projects created by the current user",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Project")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function myproject()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(["error" => "vous n'êtes pas connecté"], 401);
        }

        // Pagination avec 2 projets par page
        $projects = Project::where('created_by', $user->id)->paginate(2);

        return ProjectResource::collection($projects);
    }
}
