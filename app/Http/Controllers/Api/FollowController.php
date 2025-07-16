<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class FollowController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }

    /**
     * @OA\Post(
     *     path="/api/users/{user}/follow",
     *     summary="Follow a user",
     *     tags={"Follow"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="ID of the user to follow",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User followed successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="You cannot follow yourself"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="You are already following this user"
     *     )
     * )
     */
    public function follow(User $user): JsonResponse
    {
        $follower = Auth::user();

        if ($follower->id === $user->id) {
            return response()->json(['message' => 'Vous ne pouvez pas vous suivre vous-même.'], 403);
        }

        if ($follower->following()->where('following_id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous suivez déjà cet utilisateur.'], 409);
        }

        $follower->following()->attach($user->id);

        return response()->json([
            'message' => 'Utilisateur suivi avec succès.',
            'is_following' => true,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{user}/unfollow",
     *     summary="Unfollow a user",
     *     tags={"Follow"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="ID of the user to unfollow",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User unfollowed successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="You are not following this user"
     *     )
     * )
     */
    public function unfollow(User $user): JsonResponse
    {
        $follower = Auth::user();

        if (! $follower->following()->where('following_id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous ne suivez pas cet utilisateur.'], 404);
        }

        $follower->following()->detach($user->id);

        return response()->json([
            'message' => 'Utilisateur désuivi avec succès.',
            'is_following' => false,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{user}/followers",
     *     summary="Get a user's followers",
     *     tags={"Follow"},
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     )
     * )
     */
    public function followers(User $user): JsonResponse
    {
        return response()->json(UserResource::collection($user->followers));
    }

    /**
     * @OA\Get(
     *     path="/api/users/{user}/following",
     *     summary="Get users a user is following",
     *     tags={"Follow"},
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     )
     * )
     */
    public function following(User $user): JsonResponse
    {
        return response()->json(UserResource::collection($user->following));
    }

    /**
     * @OA\Get(
     *     path="/api/users/{user}/is-following",
     *     summary="Check if the current user is following another user",
     *     tags={"Follow"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="ID of the user to check",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="is_following", type="boolean")
     *         )
     *     )
     * )
     */
    public function isFollowing(User $user): JsonResponse
    {
        $follower = Auth::user();
        $isFollowing = $follower->following()->where('following_id', $user->id)->exists();

        return response()->json(['is_following' => $isFollowing]);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{user}/follow-counts",
     *     summary="Get follower and following counts for a user",
     *     tags={"Follow"},
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
     *         @OA\JsonContent(
     *             @OA\Property(property="followers_count", type="integer"),
     *             @OA\Property(property="following_count", type="integer")
     *         )
     *     )
     * )
     */
    public function followCounts(User $user): JsonResponse
    {
        $followersCount = $user->followers()->count();
        $followingCount = $user->following()->count();

        return response()->json([
            'followers_count' => $followersCount,
            'following_count' => $followingCount,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/users/suggestions",
     *     summary="Get user suggestions for the current user",
     *     tags={"Follow"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     )
     * )
     */
    public function suggestions(): JsonResponse
    {
        $user = Auth::user();

        // IDs des utilisateurs déjà suivis + le sien
        $excludedIds = $user->following()->pluck('users.id')->toArray();
        $excludedIds[] = $user->id;

        // Récupère les intérêts de l'utilisateur connecté
        $interestIds = $user->interests()->pluck('interests.id');

        // Trouve d'autres utilisateurs qui partagent ces intérêts
        $suggestions = User::whereNotIn('id', $excludedIds)
            ->whereHas('interests', function ($query) use ($interestIds) {
                $query->whereIn('interests.id', $interestIds);
            })
            ->with('interests')
            ->inRandomOrder()
            ->limit(5)
            ->get();

        return response()->json(UserResource::collection($suggestions));
    }
}
