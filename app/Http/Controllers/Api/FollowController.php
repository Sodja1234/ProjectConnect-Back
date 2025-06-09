<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function follow(User $user)
    {
        $follower = Auth::user();

        if ($follower->id === $user->id) {
            return response()->json(['message' => 'Vous ne pouvez pas vous suivre vous-même.'], 403);
        }

        $follower->following()->syncWithoutDetaching([$user->id]);

        return response()->json(['message' => 'Utilisateur suivi avec succès.']);
    }

    public function unfollow(User $user)
    {
        $follower = Auth::user();

        $follower->following()->detach($user->id);

        return response()->json(['message' => 'Utilisateur désuivi avec succès.']);
    }

    public function followers(User $user)
    {
        return response()->json($user->followers);
    }

    public function following(User $user)
    {
        return response()->json($user->following);
    }
}
