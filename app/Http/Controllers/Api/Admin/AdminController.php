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




}
