<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/chats",
     *     summary="Get the current user's chats",
     *     tags={"Chat"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Chat")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $chat = Chat::with(['users', 'lastMessage'])
            ->whereHas('users', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->get();
        return response()->json($chat);
    }

    /**
     * @OA\Post(
     *     path="/api/chats",
     *     summary="Create a new chat",
     *     tags={"Chat"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     required={"user_id"},
     *                     @OA\Property(property="user_id", type="integer", description="ID of the other user for a private chat", example=2)
     *                 ),
     *                 @OA\Schema(
     *                     required={"type", "user_ids"},
     *                     @OA\Property(property="type", type="string", enum={"private", "group"}),
     *                     @OA\Property(property="user_ids", type="array", @OA\Items(type="integer")),
     *                     @OA\Property(property="name", type="string", description="Name of the group chat")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chat created or retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Chat")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
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
