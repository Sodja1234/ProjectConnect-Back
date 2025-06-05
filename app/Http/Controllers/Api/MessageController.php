<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Group;
use Illuminate\Http\Request;
use Auth;


class MessageController extends Controller
{

   public function store(Request $request)
   {
    $request->validate([
        'chat_id' => 'required|exists:chats,id',
        'message' => 'required|string'
    ]);

    $chat = Chat::findOrFail($request->chat_id);

    // On vérifie si l'utilisateur appartient au chat
    if (!$chat->users->contains(Auth::id()))
    {
        return response()->json(['error' => 'Accès non autorisé'], 403);
    }

    $message = Message::create([
        'chat_id' => $chat->id,
        'sender_id' => Auth::id(),
        'message' => $request->message,
    ]);
    return response()->json($message->load('sender'), 201);

   }

   // Récupérer les message d'un chat

   public function index($chatId)
   {
    $chat = Chat::with('messages.sender')->findOrFail($chatId);

    if (!$chat->users->contains(Auth::id())){

        return response()->json(['error' => 'Accès non autorisé.'], 403);

    }
    return response()->json($chat->messages);
   }


}
