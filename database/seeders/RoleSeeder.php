<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $roles = [
    // 🔹 Développement Web
    'Développeur Web',
    'Développeur Frontend',
    'Développeur Backend',
    'Développeur Full Stack',

    // 🔹 Mobile
    'Développeur Mobile',
    

    // 🔹 Intelligence Artificielle / Data
    'Data Scientist',
    'Data Analyst',
    'Ingénieur en Machine Learning',
    'Spécialiste en IA Générative',

    // 🔹 Design / UI/UX
    'Designer UI/UX',
    'Product Designer',
    'Graphiste Web',

    // 🔹 Gestion de projet
    'Chef de projet',
    'Scrum Master',
    'Product Owner',

    // 🔹 DevOps / Systèmes
    'DevOps',
    'Ingénieur Cloud',
    'Administrateur Systèmes & Réseaux',

    // 🔹 Business / Marketing
    'Marketing Digital',
    'Growth Hacker',
    'Content Manager',
];


        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
