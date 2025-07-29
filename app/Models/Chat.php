<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Chat",
 *     type="object",
 *     title="Chat",
 *     description="Chat model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="type", type="string", enum={"private", "group"}, example="private"),
 *     @OA\Property(property="name", type="string", example="Group Chat Name"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="users", type="array", @OA\Items(ref="#/components/schemas/User")),
 *     @OA\Property(property="last_message", ref="#/components/schemas/Message")
 * )
 */
class Chat extends Model
{
    protected $fillable = ['type', 'name','project_id'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}

