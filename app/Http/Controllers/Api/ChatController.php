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
        if ($request->has('user_id')) {
            $otherUserId = $request->user_id;
            $currentUserId = Auth::id();

            if ($otherUserId == $currentUserId) {
                return response()->json(['error' => 'Impossible de créer un chat avec soi-même.'], 422);
            }

            $chat = Chat::where('type', 'private')
                ->whereHas('users', function ($query) use ($currentUserId, $otherUserId) {
                    $query->whereIn('user_id', [$currentUserId, $otherUserId]);
                }, '=', 2)
                ->first();

            if ($chat) {
                return response()->json($chat->load('users'));
            }

            $newChat = Chat::create([
                'type' => 'private',
            ]);

            $newChat->users()->attach([$currentUserId, $otherUserId]);

            return response()->json($newChat->load('users'));
        }

        $request->validate([
            'type' => 'required|in:private,group',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'name' => 'nullable|string'
        ]);

        if (count(array_unique($request->user_ids)) === 1 && $request->user_ids[0] == Auth::id()) {
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
