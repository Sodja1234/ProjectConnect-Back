<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/profiles/{user}",
     *     summary="Get a user's profile",
     *     tags={"Profiles"},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     )
     * )
     */
    public function show(User $user)
    {
        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/profile",
     *     summary="Get the current user's profile",
     *     tags={"Profiles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     )
     * )
     */
    public function myProfile()
    {
        $user = Auth::user();

        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/profile",
     *     summary="Update the current user's profile",
     *     tags={"Profiles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="job_title", type="string"),
     *                 @OA\Property(property="location", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="portfolio_url", type="string", format="url"),
     *                 @OA\Property(property="availability", type="string"),
     *                 @OA\Property(property="profile_photo", type="string", format="binary"),
     *                 @OA\Property(property="about", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'portfolio_url' => 'nullable|url',
            'availability' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'about' => 'nullable|text|max:255',
        ]);

        if ($request->hasFile('profile_photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $validated['profile_photo'] = $path;
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'user' => $user
        ]);
    }
}
