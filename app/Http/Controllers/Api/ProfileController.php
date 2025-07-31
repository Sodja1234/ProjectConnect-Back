<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProfileUserEditRequest;
use App\Http\Resources\ProfileUserResource;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Auth;
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
        $user->load('profile');

        return new ProfileUserResource($user);
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
     *         description="Profil mis à jour avec succès."
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(ProfileUserEditRequest $request, FileUploadService $upload)
    {
        $user = $request->user();

        $profile = $user->profile()->firstOrNew([], ['user_id' => $user->id]);

        $newProfileImage = $upload->update(
            $request->file('profile_photo'),
            'profiles',
            $profile->profile_photo ?? ''
        );

        $data = [...$request->validated(), 'profile_photo' => $newProfileImage];

        $hasUpdate = $profile->fill($data)->isDirty()
            ? $profile->save()
            : false;

        $message = $hasUpdate ? 'Profil mis à jour avec succès.' : 'Échec de la mise à jour du profil.';

        return response()->json([
            'message' => $message,
            'state' => $hasUpdate ? 'success' : 'error',
        ]);
    }
}
