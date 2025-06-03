<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;
use Auth;

class ChatController extends Controller
{
    public function index(){
        $chat = Chat::with(['users', 'lastMessage'])
        ->whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->get();
        return response()->json($chat);
    }

    public function store(Request $request)
    {
        // Valider formulaire
        $request->validate([
            'type' => 'required|in:private,group',
            'user_ids'=>'required|array|min:1',
            'user_ids.*'=>'exists:users,id',
            'name'=>'nullable|string'
        ]);

        if (count(array_unique($request->user_ids)) === 1 && $request->user_ids[0] == Auth::id()) 
        {
            return response()->json(['error' => 'Impossible de créer un chat avec soi-même uniquement.'], 422);
        }

        $chat = Chat::create([
            'type' => $request->type,
            'name' => $request->name,
        ]);

        $chat->users()->attach(array_merge($request->user_ids, [Auth::id()]));
        return response()->json($chat->load('users'));
    }
}
