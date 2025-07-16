<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function index(Request $request)
    {
        if(auth()->user()->role != 'admin') {
            return response()->json(['message' => 'You are not an admin'], 401);
        }

        $query = User::query();


        // Filtre par email si le paramètre 'email' est présent dans la requête
        if ($request->has('email')) {
            $query= $query->whereLike('email',  '%' . $request->email . '%');
        }
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('state') && in_array($request->state, [0, 1])) {
            $query->where('state', $request->state);
        }


        $users = $query->paginate($request->get('per_page', 10));

        return UserResource::collection($users);
    }



    public function toggleState(Request $request, $id)
    {
        try {
            $userConnect = auth()->user();

            if ($userConnect->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            if ($userConnect->id == $id) {
                return response()->json(['message' => 'Vous ne pouvez pas vous désactiver'], 403);
            }

            $user = User::find($id);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user->state = !$user->state;
            $user->save();

            return response()->json([
                'message' => 'Utilisateur ' . ($user->state ? 'activé' : 'désactivé') . ' avec succès.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


}
