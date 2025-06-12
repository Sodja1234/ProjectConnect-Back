<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class FollowersSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Chaque utilisateur va suivre entre 1 et 3 autres utilisateurs
            $toFollow = $users->where('id', '!=', $user->id)->random(rand(1, 3));

            foreach ($toFollow as $followed) {
                // Vérifie que la relation n’existe pas déjà
                if (!$user->following()->where('following_id', $followed->id)->exists()) {
                    $user->following()->attach($followed->id);
                }
            }
        }
    }
}
