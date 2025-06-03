<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\Project;
use App\Models\ProjectRole;
use App\Models\Role;
use App\Models\Skill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Récupérer quelques utilisateurs existants
        $users = User::take(5)->get();

        if ($users->isEmpty()) {
            // Créer un utilisateur si aucun n'existe
            $user = User::factory(10)->create();
            $users = collect([$user]);
        }

        // Domaines possibles
        $possibleDomains = [
            'Développement Web',
            'Intelligence Artificielle',
            'Data Science',
            'Cybersécurité',
            'Blockchain',
            'Mobile',
            'Cloud Computing',
            'DevOps',
            'UI/UX Design',
            'Marketing Digital'
        ];

        // Rôles possibles
        $possibleRoles = [
            'Développeur Frontend',
            'Développeur Backend',
            'Développeur Fullstack',
            'Data Scientist',
            'Chef de projet',
            'Designer UI/UX',
            'DevOps Engineer',
            'Spécialiste SEO',
            'Responsable Marketing',
            'Testeur QA'
        ];

        // Compétences possibles
        $possibleSkills = [
            'PHP', 'Laravel', 'JavaScript', 'Vue.js', 'React',
            'Python', 'Django', 'TensorFlow', 'SQL', 'NoSQL',
            'Docker', 'Kubernetes', 'AWS', 'Git', 'Node.js',
            'HTML/CSS', 'SASS', 'Figma', 'Adobe XD', 'Swift',
            'Kotlin', 'Flutter', 'Machine Learning', 'Big Data'
        ];

        // Créer 10 projets
        for ($i = 1; $i <= 10; $i++) {
            $user = $users->random();

            $dateStart = Carbon::now()->addDays(rand(-30, 30));
            $dateEnd = $dateStart->copy()->addDays(rand(30, 180));

            $project = Project::create([
                'title' => "Projet $i - " . fake()->sentence(3),
                'description' => fake()->paragraphs(3, true),
                'date_start' => $dateStart,
                'date_end' => $dateEnd,
                'budget' => rand(0, 1) ? rand(1000, 50000) : null,
                'location' => rand(0, 1) ? fake()->city() : null,
                'visibility' => rand(0, 1) ? 'public' : 'private',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            // Attacher des domaines aléatoires (1 à 3)
            $domains = collect($possibleDomains)
                ->random(rand(1, 3))
                ->map(fn($name) => Domain::firstOrCreate(['name' => $name])->id);

            $project->domains()->attach($domains);

            // Créer des rôles avec compétences (2 à 5 rôles)
            $rolesCount = rand(2, 5);

            for ($j = 0; $j < $rolesCount; $j++) {
                $roleName = $possibleRoles[array_rand($possibleRoles)];
                $role = Role::firstOrCreate(['name' => $roleName]);

                $skills = collect($possibleSkills)
                    ->random(rand(1, 5))
                    ->map(fn($name) => Skill::firstOrCreate(['name' => $name])->id);

                $projectRole = ProjectRole::create([
                    'project_id' => $project->id,
                    'role_id' => $role->id,
                    'description' =>  fake()->sentence(),
                ]);

                $projectRole->skills()->attach($skills);
            }
        }
    }
}
