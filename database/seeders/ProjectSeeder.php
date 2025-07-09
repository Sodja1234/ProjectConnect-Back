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
        $users = User::take(5)->get();

        if ($users->isEmpty()) {
            $user = User::factory(10)->create();
            $users = collect([$user]);
        }

        // Domaines avec descriptions
        $domains = [
            ["name" => "Développement Web"],
            ["name" => "Intelligence Artificielle"],
            ["name" => "Data Science"],
            ["name" => "Cybersécurité"],
            ["name" => "Mobile"],
            ["name" => "Cloud Computing"],
            ["name" => "UI/UX Design"]
        ];

        // Rôles possibles
        $possibleRoles = [
            "Développeur Frontend", "Développeur Backend", "Développeur Fullstack",
            "Data Scientist", "Chef de projet", "Designer UI/UX", "DevOps Engineer",
            "Spécialiste SEO", "Responsable Marketing", "Testeur QA"
        ];

        // Compétences possibles
        $possibleSkills = [
            "PHP", "Laravel", "JavaScript", "Vue.js", "React", "Python", "Django",
            "TensorFlow", "SQL", "NoSQL", "Docker", "Kubernetes", "AWS", "Git",
            "Node.js", "HTML/CSS", "SASS", "Figma", "Adobe XD", "Swift", "Kotlin",
            "Flutter", "Machine Learning", "Big Data", "Cybersecurity", "Azure"
        ];

        // Créer les domaines en base
        foreach ($domains as $domain) {
            Domain::firstOrCreate(["name" => $domain["name"]]);
        }

        // Projets réels avec descriptions détaillées
        $realProjects = [
            [
                "title" => "Plateforme Collaborative pour Artistes Indépendants",
                "description" => "Ce projet ambitieux vise à révolutionner la manière dont les artistes indépendants (peintres, sculpteurs, photographes, musiciens) interagissent avec leur public et entre eux. La plateforme web intégrera une galerie virtuelle en 3D permettant des expositions immersives où les visiteurs peuvent naviguer comme dans un véritable espace d'exposition. Le système de messagerie sécurisé inclura des fonctionnalités avancées comme la signature électronique de contrats et la négociation en temps réel. La place de marché proposera plusieurs modèles de monétisation (vente directe, locations, licences) avec un système de paiement multi-devises et un suivi blockchain des transactions pour garantir la traçabilité des œuvres. Les fonctionnalités de streaming seront optimisées pour des performances HD même sur connexion limitée, avec des outils interactifs permettant aux artistes d'engager leur audience pendant les performances. Le forum communautaire sera modéré par IA pour maintenir un environnement bienveillant, avec des espaces thématiques et des événements virtuels réguliers. Des outils analytiques poussés donneront aux artistes des insights sur leur audience et les tendances du marché. La plateforme sera développée avec une architecture microservices sur AWS, utilisant React pour le frontend et Node.js pour le backend, avec une attention particulière portée à l'accessibilité et au responsive design.",
                "domains" => ["Développement Web", "Cloud Computing", "UI/UX Design"],
                "budget" => 120000,
                "duration_months" => 9
            ],
            [
                "title" => "Application IA de Coaching Sportif Personnalisé",
                "description" => "Cette application mobile révolutionnaire combine les dernières avancées en intelligence artificielle avec une compréhension approfondie de la physiologie humaine pour offrir une expérience de coaching entièrement personnalisée. L'IA analyse en temps réel les données provenant de wearables (fréquence cardiaque, qualité du sommeil, activité quotidienne) et des capteurs de mouvement intégrés aux vêtements sportifs pour adapter dynamiquement les programmes d'entraînement. Le chatbot NLP comprend non seulement les questions textuelles mais aussi le ton et l'émotion derrière les messages vocaux, permettant des interactions naturelles. La bibliothèque de vidéos inclut des tutoriels en réalité augmentée qui superposent les mouvements corrects directement sur l'image de l'utilisateur. Le système de gamification est basé sur des neurosciences comportementales pour maximiser la motivation à long terme, avec des défis adaptés au niveau et aux préférences de chacun. La sécurité des données est garantie par un chiffrement de bout en bout et un stockage décentralisé, avec des audits de sécurité trimestriels par des experts indépendants. L'application sera développée en Flutter pour une expérience cross-platform optimale, avec des modules natifs pour les fonctionnalités avancées de traitement d'image et de son.",
                "domains" => ["Mobile", "Intelligence Artificielle", "Cybersécurité"],
                "budget" => 90000,
                "duration_months" => 7
            ],
            [
                "title" => "Suite SaaS de Gestion RH pour PME Innovantes",
                "description" => "Cette suite complète redéfinit la gestion des ressources humaines pour les PME en croissance rapide en automatisant les processus tout en préservant l'aspect humain. Le module de recrutement utilise l'IA pour analyser non seulement les CV mais aussi les vidéos de présentation des candidats, évaluant le langage corporel et les soft skills. L'onboarding est entièrement personnalisable avec des parcours interactifs incluant réalité virtuelle pour les visites d'entreprise à distance. La gestion des performances s'appuie sur des objectifs SMART dynamiques et des feedbacks 360° automatisés. Le module bien-être propose des enquêtes sentiment analysis et des recommandations personnalisées, avec intégration aux applications de santé mentale. Le tableau de bord executive offre des visualisations de données en temps réel avec capacité de drill-down jusqu'au niveau individuel. Les workflows peuvent être modélisés graphiquement sans codage, avec des déclencheurs basés sur des centaines de paramètres. L'intégration API permet des connexions sécurisées avec plus de 50 systèmes de paie et outils professionnels. L'interface utilisateur a été conçue après 18 mois de recherche UX avec des tests utilisateurs itératifs, garantissant une adoption rapide par les équipes. La solution est hébergée sur Azure avec une disponibilité garantie de 99.99%, et des sauvegardes géo-redondantes.",
                "domains" => ["Développement Web", "Cloud Computing", "UI/UX Design"],
                "budget" => 150000,
                "duration_months" => 10
            ],
            [
                "title" => "Plateforme de Formation Sécurisée pour Écoles de Médecine",
                "description" => "Cette plateforme d'e-learning hautement spécialisée transforme l'éducation médicale grâce à des technologies immersives et des protocoles de sécurité sans précédent. Les cours interactifs incluent des modèles anatomiques 3D manipulables à 360°, avec des couches superposables pour visualiser muscles, vaisseaux sanguins et systèmes nerveux. Les vidéos chirurgicales en 4K HDR sont enrichies de métadonnées techniques et peuvent être visionnées en réalité virtuelle avec des angles de caméra personnalisables. Le simulateur de cas cliniques utilise l'IA pour adapter la complexité des scénarios en fonction des performances de l'étudiant, avec des patients virtuels dotés de réponses émotionnelles réalistes. Le système de certification repose sur la blockchain pour une vérification infalsifiable des diplômes. La visioconférence intégrée offre des outils collaboratifs avancés comme l'annotation partagée d'images médicales et la télémanipulation de modèles 3D. La sécurité est renforcée par une authentification biométrique multi-facteurs, un chiffrement homomorphique pour les données sensibles, et un système de watermarking traçable pour tous les documents. La plateforme est conforme aux normes HIPAA, GDPR et aux réglementations médicales internationales, avec des audits de sécurité mensuels et une équipe dédiée à la veille des vulnérabilités. L'infrastructure cloud utilise des serveurs dédiés avec accès physique restreint, situés dans des bunkers hautement sécurisés.",
                "domains" => ["Développement Web", "Cybersécurité", "Cloud Computing"],
                "budget" => 110000,
                "duration_months" => 8
            ],
            // [...] (Les autres projets avec leurs descriptions enrichies)
        ];

        foreach ($realProjects as $projectData) {
            $user = $users->random();
            $startDate = Carbon::now()->addDays(rand(-30, 30));
            $endDate = $startDate->copy()->addMonths($projectData["duration_months"]);

            $project = Project::create([
                "title" => $projectData["title"],
                "description" => $projectData["description"],
                "date_start" => $startDate,
                "date_end" => $endDate,
                "budget" => $projectData["budget"],
                "status_id" => 1,
                "location" => rand(0, 1) ? fake()->city() : "Remote",
                "visibility" => "public",
                "created_by" => $user->id,
                "updated_by" => $user->id,
            ]);

            // Attacher les domaines
            $domainIds = collect($projectData["domains"])
                ->map(fn($name) => Domain::where("name", $name)->first()->id);

            $project->domains()->attach($domainIds);

            // Créer des rôles avec compétences (2 à 5 rôles)
            $rolesCount = rand(2, 5);

            for ($j = 0; $j < $rolesCount; $j++) {
                $roleName = $possibleRoles[array_rand($possibleRoles)];
                $role = Role::firstOrCreate(["name" => $roleName]);

                $skills = collect($possibleSkills)
                    ->random(rand(2, 5))
                    ->map(fn($name) => Skill::firstOrCreate(["name" => $name])->id);

                $projectRole = ProjectRole::create([
                    "project_id" => $project->id,
                    "role_id" => $role->id,
                    "description" => $this->generateRoleDescription($roleName, $projectData["title"]),
                ]);

                $projectRole->skills()->attach($skills);
            }
        }
    }

    protected function generateRoleDescription($roleName, $projectTitle)
    {
        $descriptions = [
            "Développeur Frontend" =>
                "En tant que Développeur Frontend sur \"$projectTitle\", vous serez responsable de la conception et de l'implémentation des interfaces utilisateur. Vos missions incluront : le développement de composants React/Vue.js hautement interactifs, l'optimisation des performances pour des temps de chargement inférieurs à 2 secondes, l'implémentation rigoureuse des maquettes Figma avec une précision pixel-perfect, et la garantie d'une accessibilité conforme aux normes WCAG 2.1 AA. Vous travaillerez en étroite collaboration avec les designers UX pour itérer sur les prototypes, et avec les développeurs backend pour optimiser les appels API. Vous participerez également aux revues de code et contribuerez à l'évolution de l'architecture frontend vers une approche micro-frontends. Une expérience avec les Progressive Web Apps et les techniques de rendering côté serveur serait un plus.",
            "Développeur Backend" =>
                "En tant que Développeur Backend sur \"$projectTitle\", vous concevrez et maintiendrez les API robustes et sécurisées qui alimentent notre application. Vos responsabilités incluent : la modélisation de la base de données relationnelle/noSQL, l'implémentation de microservices avec Node.js/Python, la mise en place de systèmes d'authentification JWT/OAuth2, et l'optimisation des requêtes pour gérer des charges de 10 000 requêtes/minute. Vous développerez des systèmes de cache distribués, implémenterez des files d'attente pour le traitement asynchrone, et mettrez en place une surveillance complète avec logging centralisé. Vous travaillerez avec l'équipe DevOps pour automatiser les déploiements et avec l'équipe sécurité pour réaliser des audits de code réguliers. Une expérience avec l'architecture event-driven et les patterns CQRS serait appréciée.",
            "Chef de projet" =>
                "En tant que Chef de projet pour \"$projectTitle\", vous serez le pivot central entre les parties prenantes, l'équipe technique et les utilisateurs finaux. Vos missions principales incluront : la planification détaillée des sprints avec estimation des charges, la gestion des risques et des dépendances, le suivi budgétaire avec une tolérance de ±5%, et l'animation des rituels Agile (daily standups, sprint planning, retrospectives). Vous créerez des tableaux de bord de suivi avec des indicateurs clés (velocity, burn-down, taux de résolution des bugs) et assurerez une communication transparente via des reporting hebdomadaires. Vous faciliterez les ateliers de conception avec les utilisateurs et veillerez à l'alignement constant entre les livrables et la vision produit. Une certification PMP ou Scrum Master serait un atout, de même qu'une expérience avec Jira et Confluence.",
            "Designer UI/UX" =>
                "En tant que Designer UI/UX pour \"$projectTitle\", vous serez responsable de l'ensemble de l'expérience utilisateur, depuis la recherche utilisateur jusqu'aux prototypes haute fidélité. Vous conduirez des interviews utilisateurs, créerez des personas détaillés, et élaborerez des user journeys complètes. Vous produirez des wireframes interactifs avec Figma, des design systems cohérents, et des prototypes cliquables pour les tests utilisateurs. Vous travaillerez en étroite collaboration avec les développeurs pour assurer une implémentation fidèle, et participerez aux tests d'utilisabilité itératifs. Vous veillerez à l'accessibilité (contrastes, taille des textes, navigation au clavier) et à la cohérence de l'identité visuelle. Une expérience avec les animations micro-interactions et les principes de motion design serait appréciée, de même qu'une connaissance des outils de heatmap et d'eye-tracking.",
            "Data Scientist" =>
                "En tant que Data Scientist sur \"$projectTitle\", vous développerez des modèles prédictifs et des algorithmes d'analyse avancée. Vous serez responsable du prétraitement des données (nettoyage, feature engineering), de la sélection et de l'entraînement des modèles (réseaux de neurones, forêts aléatoires, SVM), et de l'évaluation rigoureuse des performances. Vous implémenterez des pipelines de données reproductibles avec MLflow et créerez des visualisations interactives des résultats. Vous travaillerez avec les ingénieurs pour industrialiser les modèles en production et mettrez en place des systèmes de monitoring des dérives. Une expertise en NLP ou computer vision serait un plus, de même qu'une expérience avec TensorFlow/PyTorch et les techniques d'optimisation hyperparamétrique.",
            "DevOps Engineer" =>
                "En tant qu'Ingénieur DevOps sur \"$projectTitle\", vous bâtirez et maintiendrez l'infrastructure cloud hautement disponible. Vos missions incluront : l'automatisation des déploiements avec CI/CD (GitHub Actions/Jenkins), la configuration d'infrastructure as code (Terraform), la mise en place de conteneurs Docker orchestrés par Kubernetes, et l'optimisation des coûts cloud. Vous implémenterez des solutions de monitoring complet (Prometheus/Grafana), de logging centralisé (ELK) et d'alerting intelligent. Vous travaillerez avec l'équipe sécurité pour renforcer les politiques IAM, les règles de sécurité réseau et les scans de vulnérabilités. Une expérience avec le serverless et les architectures multi-cloud serait appréciée, de même qu'une connaissance des bonnes pratiques FinOps.",
            "Testeur QA" =>
                "En tant que Testeur QA sur \"$projectTitle\", vous garantirez la qualité du produit via des tests rigoureux. Vous élaborerez des plans de test complets, créerez des cas de test automatisés (Selenium/Cypress), et effectuerez des tests manuels exploratoires. Vous documenterez précisément les bugs avec étapes de reproduction et evidences, et validerez les corrections. Vous mettrez en place des tests de performance (LoadRunner) et de sécurité (OWASP ZAP). Vous travaillerez en amont avec les développeurs pour définir des critères d'acceptation clairs et participerez aux revues de spécifications. Une certification ISTQB serait un plus, de même qu'une expérience avec les stratégies de test dans les environnements Agile."
        ];

        return $descriptions[$roleName] ?? "En tant que contributeur clé sur \"$projectTitle\", vous apporterez votre expertise pour faire avancer les différentes dimensions du projet. Votre rôle polyvalent vous amènera à collaborer avec les différentes équipes (technique, design, produit) pour assurer la cohérence et la qualité de l'ensemble. Vous participerez aux décisions architecturales, aux revues de code, et aux ateliers de conception, tout en veillant au respect des bonnes pratiques et des standards de l'industrie. Votre capacité à comprendre les enjeux techniques et fonctionnels fera de vous un maillon essentiel dans la réussite de ce projet ambitieux.";
    }
}
