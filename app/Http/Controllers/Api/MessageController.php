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

    /**
     * @OA\Post(
     *     path="/api/messages",
     *     summary="Send a message to a chat",
     *     tags={"Messages"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"chat_id", "message"},
     *             @OA\Property(property="chat_id", type="integer", example=1),
     *             @OA\Property(property="message", type="string", example="Hello, world!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Message sent successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Message")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'message' => 'required|string'
        ]);

        $chat = Chat::findOrFail($request->chat_id);

        // On vérifie si l'utilisateur appartient au chat
        if (!$chat->users->contains(Auth::id())) {
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

    /**
     * @OA\Get(
     *     path="/api/chats/{chatId}/messages",
     *     summary="Get messages for a chat",
     *     tags={"Messages"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="chatId",
     *         in="path",
     *         required=true,
     *         description="ID of the chat",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index($chatId)
    {
        $chat = Chat::with('messages.sender')->findOrFail($chatId);

        if (!$chat->users->contains(Auth::id())) {

            return response()->json(['error' => 'Accès non autorisé.'], 403);
        }
        return response()->json($chat->messages);
    }
}
