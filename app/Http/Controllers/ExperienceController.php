<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
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

            $user = auth()->user();
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
    public function show(string $id)
    {
        $user = auth()->user();

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
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $experience = Experience::where('created_by', '=', $user->id)->findOrFail($id);

        $experience->delete();

        return response()->json(['message' => 'Experience supprimée avec succès.']);
    }
}
