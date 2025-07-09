<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Group;
use App\Models\Message;

class MessagingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer 3 utilisateurs
        $users = User::factory()->count(3)->create();

        // 2. Créer 2 groupes
        $groups = Group::factory()->count(2)->create();

        // 3. Ajouter quelques messages privés
        Message::create([
            'sender_id' => $users[0]->id,
            'receiver_id' => $users[1]->id,
            'message' => 'Salut de ' . $users[0]->name . ' à ' . $users[1]->name,
        ]);

        Message::create([
            'sender_id' => $users[1]->id,
            'receiver_id' => $users[0]->id,
            'message' => 'Hello retour de ' . $users[1]->name,
        ]);

        // 4. Ajouter quelques messages de groupe
        foreach ($groups as $group) {
            Message::create([
                'sender_id' => $users[2]->id,
                'group_id' => $group->id,
                'message' => 'Message de ' . $users[2]->name . ' dans le groupe ' . $group->name,
            ]);
        }
    }
}
