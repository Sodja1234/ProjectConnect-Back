<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer des utilisateurs
        $users = User::factory()->count(5)->create();

        // 2. Créer une conversation privée entre 2 utilisateurs
        $privateChat = Chat::create([
            'type' => 'private',
        ]);

        $privateChat->users()->attach([$users[0]->id, $users[1]->id]);

        Message::create([
            'chat_id' => $privateChat->id,
            'sender_id' => $users[0]->id,
            'message' => "Salut, tu es dispo pour un appel ?"
        ]);

        Message::create([
            'chat_id' => $privateChat->id,
            'sender_id' => $users[1]->id,
            'message' => "Oui, appelle-moi !"
        ]);

        // 3. Créer un chat de groupe
        $groupChat = Chat::create([
            'type' => 'group',
            'name' => 'Équipe Dev'
        ]);

        $groupChat->users()->attach($users->pluck('id'));

        foreach ($users as $user) {
            Message::create([
                'chat_id' => $groupChat->id,
                'sender_id' => $user->id,
                'message' => "Bonjour à tous, ici " . $user->name
            ]);
        }
    }
}
