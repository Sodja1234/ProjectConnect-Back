<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            // 🔹 Frontend
            'HTML',
            'CSS',
            'JavaScript',
            'TypeScript',
            'React',
            'Vue.js',
            'Angular',
            'Svelte',
            'Next.js',
            'Nuxt.js',
            'Tailwind CSS',
            'Bootstrap',
            'Figma (intégration)',

            // 🔹 Backend
            'PHP',
            'Laravel',
            'Symfony',
            'Node.js',
            'Express.js',
            'Python',
            'Django',
            'Flask',
            'Java',
            'Spring Boot',
            '.NET Core',
            'MySQL',
            'PostgreSQL',
            'MongoDB',
            'Redis',
            'API REST',
            'GraphQL',
            'JWT',

            // 🔹 Mobile
            'Flutter',
            'React Native',
            'Kotlin',
            'Swift',
            'Dart',
            'Xamarin',

            // 🔹 IA / Data
            'Python',
            'TensorFlow',
            'PyTorch',
            'Scikit-learn',
            'Keras',
            'Pandas',
            'Numpy',
            'Computer Vision',
            'NLP',
            'Data Mining',
            'Big Data',
            'MLflow',
            'Data Visualization',
            'Prompt Engineering',

            // 🔹 DevOps / Cloud
            'Docker',
            'Kubernetes',
            'GitHub Actions',
            'Jenkins',
            'GitLab CI/CD',
            'AWS',
            'Azure',
            'Google Cloud',
            'Terraform',
            'Linux',
            'Bash',

            // 🔹 UI/UX / Design
            'Figma',
            'Adobe XD',
            'Sketch',
            'Illustrator',
            'Photoshop',
            'Design Thinking',
            'Wireframing',
            'Prototypage',

            // 🔹 Gestion / Produit
            'Agile Scrum',
            'Jira',
            'Trello',
            'Gestion de projet',
            'Analyse fonctionnelle',
            'Conduite de réunion',
            'User Stories',
            'Product Roadmap',
            'Communication',
            'Leadership',
        ];

        foreach ($skills as $skill) {
            Skill::create(['name' => $skill]);
        }
    }
    
}
