<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class ExperienceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/experiences",
     *     summary="Get the current user's experiences",
     *     tags={"Experiences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Experience")
     *         )
     *     )
     * )
     */
    public  function index(Request $request)
    {
        $user = $request->user();

        $experiences = Experience::with(['user'])
            ->where('created_by', $user->id)
            ->get();

        return ExperienceResource::collection($experiences);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Store a newly created resource in storage.
     */

    //  methode pour cree une experience
    /**
     * @OA\Post(
     *     path="/api/experiences",
     *     summary="Create a new experience",
     *     tags={"Experiences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Experience")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Experience created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Experience")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'position'    => 'required|string|max:255',
                'company'     => 'required|string|max:255',
                'date_start'  => 'nullable|date',
                'date_end'    => 'nullable|date|after_or_equal:date_start',
                'description' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            $experience = Experience::create(array_merge($validated, [
                'created_by' => $user->id,
            ]));

            if (!$experience) {
                return response()->json(['error' => 'Erreur lors de la création de l\'expérience'], 500);
            }

            return (new ExperienceResource($experience->load('user')))
                ->response()
                ->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/experiences/{id}",
     *     summary="Get a specific experience",
     *     tags={"Experiences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the experience",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Experience")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Experience not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        $user = Auth::user();

        try {
            $experience = Experience::with(['user'])
                ->where('created_by', '=', $user->id)
                ->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Experience non trouvé'], 404);
        }


        return new ExperienceResource($experience);
    }

    /**
     * Show the form for editing the specified resource.
     */
    /**
     * @OA\Put(
     *     path="/api/experiences/{id}",
     *     summary="Update an experience",
     *     tags={"Experiences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the experience",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Experience")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Experience updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Experience not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $experience = Experience::where('created_by', '=', $user->id)->findOrFail($id);

        try {
            $validator = Validator::make($request->all(), [
                'position'    => 'required|string|max:255',
                'company'     => 'required|string|max:255',
                'date_start'  => 'nullable|date',
                'date_end'    => 'nullable|date|after_or_equal:date_start',
                'description' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();
            $experience->update($validated);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'experience modifier avec succès']);
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/experiences/{id}",
     *     summary="Delete an experience",
     *     tags={"Experiences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the experience",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Experience deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Experience not found"
     *     )
     * )
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $experience = Experience::where('created_by', '=', $user->id)->findOrFail($id);

        $experience->delete();

        return response()->json(['message' => 'Experience supprimée avec succès.']);
    }
}
