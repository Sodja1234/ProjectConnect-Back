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

    public function followers(User $user): JsonResponse
    {
        return response()->json(UserResource::collection($user->followers));
    }

    public function following(User $user): JsonResponse
    {
        return response()->json(UserResource::collection($user->following));
    }

    public function isFollowing(User $user): JsonResponse
    {
        $follower = Auth::user();
        $isFollowing = $follower->following()->where('following_id', $user->id)->exists();

        return response()->json(['is_following' => $isFollowing]);
    }

    public function followCounts(User $user): JsonResponse
    {
        $followersCount = $user->followers()->count();
        $followingCount = $user->following()->count();

        return response()->json([
            'followers_count' => $followersCount,
            'following_count' => $followingCount,
        ]);
    }

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
