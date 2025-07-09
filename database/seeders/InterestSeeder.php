<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Interest;
use App\Models\User;

class InterestSeeder extends Seeder
{
    public function run()
    {
        $interests = [
            'Musique',
            'Sport',
            'Programmation',
            'Lecture',
            'Voyage',
            'Cuisine',
            'Cinéma',
            'Photographie',
            'Gaming',
            'Design',
        ];

        // Crée les intérêts
        foreach ($interests as $interest) {
            Interest::firstOrCreate(['name' => $interest]);
        }

        // Récupère tous les intérêts
        $allInterests = Interest::all();

        // Associe de 1 à 3 intérêts aléatoires à chaque utilisateur
        User::all()->each(function ($user) use ($allInterests) {
            $randomInterests = $allInterests->random(rand(1, 3))->pluck('id');
            $user->interests()->syncWithoutDetaching($randomInterests);
        });
    }
}
