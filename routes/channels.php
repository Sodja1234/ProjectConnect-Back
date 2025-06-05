<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('group.{groupId}', function ($user, $groupId) {
    return $user->groups()->where('groups.id', $groupId)->exists();
});

Broadcast::channel('private-chat.{user1}.{user2}', function ($user, $user1, $user2) {
    return in_array($user->id, [(int)$user1, (int)$user2]);
});

