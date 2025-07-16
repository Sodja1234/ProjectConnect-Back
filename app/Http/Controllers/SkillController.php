<?php

namespace App\Http\Controllers;

use App\Http\Resources\SkillsResource;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SkillController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/skills",
     *     summary="Get a list of skills",
     *     tags={"Skills"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Skill")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $skills = Skill::all();
        return response()->json([
            "data" => $skills
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/skills",
     *     summary="Create a new skill",
     *     tags={"Skills"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="PHP")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Skill created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Skill")
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
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $skill = Skill::create($request->all());
        return response()->json([
            "data" => $skill
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/skills/{skill}",
     *     summary="Get a specific skill",
     *     tags={"Skills"},
     *     @OA\Parameter(
     *         name="skill",
     *         in="path",
     *         required=true,
     *         description="ID of the skill",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Skill")
     *     )
     * )
     */
    public function show(Skill $skill)
    {
        return response()->json([
            "data" => $skill
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/skills/{skill}",
     *     summary="Update a skill",
     *     tags={"Skills"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="skill",
     *         in="path",
     *         required=true,
     *         description="ID of the skill",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="PHP")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Skill updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Skill")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, Skill $skill)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $skill->update($request->all());
        return response()->json([
            "data" => $skill
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/skills/{skill}",
     *     summary="Delete a skill",
     *     tags={"Skills"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="skill",
     *         in="path",
     *         required=true,
     *         description="ID of the skill",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Skill deleted successfully"
     *     )
     * )
     */
    public function destroy(Skill $skill)
    {
        $skill->delete();
        return response()->json([], 200);
    }

    public function myskill(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Utilisateur non connecté'], 401);
        }

        $skills = $user->skills;

        // return response()->json($skills, 200);
        return SkillsResource::collection($skills);
    }
}
