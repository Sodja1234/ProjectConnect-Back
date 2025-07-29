<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\Domain;
use App\Models\Message;
use App\Models\Project;
use App\Models\ProjectRole;
use App\Models\Role;
use App\Models\Skill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Vérifier et créer au moins 6 utilisateurs si nécessaire
        if (User::count() < 6) {
            User::factory(6 - User::count())->create();
        }

        $users = User::all();

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
            "Développeur Frontend",
            "Développeur Backend",
            "Développeur Fullstack",
            "Data Scientist",
            "Chef de projet",
            "Designer UI/UX",
            "DevOps Engineer",
            "Spécialiste SEO",
            "Responsable Marketing",
            "Testeur QA"
        ];

        // Compétences possibles
        $possibleSkills = [
            "PHP",
            "Laravel",
            "JavaScript",
            "Vue.js",
            "React",
            "Python",
            "Django",
            "TensorFlow",
            "SQL",
            "NoSQL",
            "Docker",
            "Kubernetes",
            "AWS",
            "Git",
            "Node.js",
            "HTML/CSS",
            "SASS",
            "Figma",
            "Adobe XD",
            "Swift",
            "Kotlin",
            "Flutter",
            "Machine Learning",
            "Big Data",
            "Cybersecurity",
            "Azure"
        ];

        // Créer les domaines en base
        foreach ($domains as $domain) {
            Domain::firstOrCreate(["name" => $domain["name"]]);
        }

        // Projets réels avec descriptions détaillées
        $realProjects = [
            [
                "title" => "Plateforme Collaborative pour Artistes Indépendants",
                "description" => "Ce projet ambitieux vise à révolutionner la manière dont les artistes indépendants (peintres, sculpteurs, photographes, musiciens) interagissent avec leur public et entre eux. La plateforme web intégrera une galerie virtuelle en 3D permettant des expositions immersives où les visiteurs peuvent naviguer comme dans un véritable espace d'exposition...",
                "domains" => ["Développement Web", "Cloud Computing", "UI/UX Design"],
                "budget" => 120000,
                "duration_months" => 9
            ],
            [
                "title" => "Application IA de Coaching Sportif Personnalisé",
                "description" => "Cette application mobile révolutionnaire combine les dernières avancées en intelligence artificielle avec une compréhension approfondie de la physiologie humaine pour offrir une expérience de coaching entièrement personnalisée...",
                "domains" => ["Mobile", "Intelligence Artificielle", "Cybersécurité"],
                "budget" => 90000,
                "duration_months" => 7
            ],
            [
                "title" => "Suite SaaS de Gestion RH pour PME Innovantes",
                "description" => "Cette suite complète redéfinit la gestion des ressources humaines pour les PME en croissance rapide en automatisant les processus tout en préservant l'aspect humain...",
                "domains" => ["Développement Web", "Cloud Computing", "UI/UX Design"],
                "budget" => 150000,
                "duration_months" => 10
            ],
            [
                "title" => "Plateforme de Formation Sécurisée pour Écoles de Médecine",
                "description" => "Cette plateforme d'e-learning hautement spécialisée transforme l'éducation médicale grâce à des technologies immersives et des protocoles de sécurité sans précédent...",
                "domains" => ["Développement Web", "Cybersécurité", "Cloud Computing"],
                "budget" => 110000,
                "duration_months" => 8
            ],
            [
                "title" => "Système de Gestion Intelligente des Déchets Urbains",
                "description" => "Solution IoT pour optimiser la collecte des déchets en milieu urbain. Des capteurs installés dans les poubelles publiques mesurent le niveau de remplissage et envoient des données en temps réel à une plateforme centrale. L'IA analyse ces données pour établir des trajets optimaux pour les camions de collecte, réduisant ainsi les coûts opérationnels et l'empreinte carbone. Le système inclut également une application citoyenne pour signaler les dépôts sauvages et encourager le tri sélectif via un système de récompenses.",
                "domains" => ["Intelligence Artificielle", "Data Science", "Cloud Computing"],
                "budget" => 85000,
                "duration_months" => 6
            ],
            [
                "title" => "Marketplace de Services pour Seniors",
                "description" => "Plateforme connectant les seniors avec des prestataires de services locaux (aide à domicile, cours informatique, accompagnement médical). L'interface ultra-simplifiée est adaptée aux utilisateurs peu familiarisés avec le numérique, avec assistance vocale intégrée. Le système de matching utilise l'IA pour recommander les prestataires les plus adaptés en fonction des besoins spécifiques et de la personnalité des utilisateurs. La plateforme inclut un système de paiement sécurisé et un suivi qualité rigoureux.",
                "domains" => ["Développement Web", "UI/UX Design", "Mobile"],
                "budget" => 75000,
                "duration_months" => 5
            ],
            [
                "title" => "Outil de Collaboration en Réalité Virtuelle",
                "description" => "Solution VR pour les équipes distantes permettant des réunions immersives dans des espaces virtuels personnalisables. Les utilisateurs peuvent interagir avec des tableaux blancs 3D, manipuler des prototypes virtuels, et participer à des ateliers de brainstorming avec des outils de créativité visuelle. La plateforme intègre la reconnaissance gestuelle et le suivi oculaire pour une interaction naturelle, ainsi que des avatars personnalisables avec expressions faciales réalistes.",
                "domains" => ["UI/UX Design", "Cloud Computing", "Intelligence Artificielle"],
                "budget" => 130000,
                "duration_months" => 8
            ],
            [
                "title" => "Application de Gestion de Patrimoine Personnel",
                "description" => "Outil financier complet permettant aux particuliers de suivre et optimiser leur patrimoine (comptes bancaires, investissements, immobilier). L'application agrège automatiquement les données de différentes sources, fournit des analyses personnalisées et des recommandations d'optimisation fiscale. Un module de simulation permet de projeter l'évolution du patrimoine selon différents scénarios économiques. La sécurité est renforcée par un chiffrement de bout en bout et une authentification biométrique.",
                "domains" => ["Mobile", "Cybersécurité", "Data Science"],
                "budget" => 95000,
                "duration_months" => 7
            ],
            [
                "title" => "Plateforme de Recrutement par Compétences",
                "description" => "Système innovant de matching entre candidats et employeurs basé sur une évaluation approfondie des compétences techniques et soft skills. Les candidats passent des tests pratiques en ligne et des mises en situation professionnelle virtuelles. L'IA analyse non seulement les réponses mais aussi les processus de raisonnement. Les employeurs accèdent à des profils enrichis avec des données prédictives sur la performance et l'adaptation culturelle.",
                "domains" => ["Intelligence Artificielle", "Développement Web", "Data Science"],
                "budget" => 105000,
                "duration_months" => 6
            ],
            [
                "title" => "Système de Surveillance Agricole par Drone",
                "description" => "Solution complète d'analyse des cultures par imagerie multispectrale. Des drones autonomes effectuent des survols réguliers des parcelles, capturant des données détaillées sur la santé des plantes. L'IA détecte précocement les maladies, les carences nutritionnelles et les stress hydriques. Les agriculteurs reçoivent des recommandations d'action précises via une application mobile, avec intégration à leurs systèmes d'irrigation et de fertilisation existants.",
                "domains" => ["Intelligence Artificielle", "Mobile", "Data Science"],
                "budget" => 115000,
                "duration_months" => 9
            ],
            [
                "title" => "Réseau Social pour Passionnés de Niche",
                "description" => "Plateforme communautaire dédiée aux passionnés de hobbies spécifiques (astronomie, modélisme, œnologie). Contrairement aux réseaux généralistes, l'interface et les fonctionnalités sont entièrement adaptées à chaque communauté. Outils spécialisés inclus : calendrier d'événements, système d'échange/vente entre membres, espaces collaboratifs pour projets communs, et base de connaissances crowdsourcée. Modération communautaire avec outils avancés pour maintenir la qualité des discussions.",
                "domains" => ["Développement Web", "UI/UX Design", "Mobile"],
                "budget" => 80000,
                "duration_months" => 6
            ],
            [
                "title" => "Outil de Prototypage Rapide pour Startups",
                "description" => "Suite logicielle permettant de transformer rapidement des idées en prototypes interactifs. Intègre un créateur d'interfaces visuel, un simulateur de base de données, et un générateur d'API mock. Les startups peuvent ainsi tester leurs concepts auprès d'utilisateurs sans investir dans un développement complet. Des templates sectoriels accélèrent le processus, et un système de feedback intégré permet d'itérer rapidement. Export possible vers les principaux frameworks frontend.",
                "domains" => ["Développement Web", "UI/UX Design", "Cloud Computing"],
                "budget" => 70000,
                "duration_months" => 5
            ],
            [
                "title" => "Application de Méditation Guidée par Biométrie",
                "description" => "Solution de bien-être utilisant des capteurs physiologiques (fréquence cardiaque, conductance cutanée) pour adapter en temps réel les séances de méditation. L'IA analyse les réponses du corps pour déterminer les techniques les plus efficaces pour chaque utilisateur. La bibliothèque de contenus inclut des centaines de programmes spécialisés (sommeil, gestion du stress, performance cognitive). Interface minimaliste avec design sonore personnalisable.",
                "domains" => ["Mobile", "Intelligence Artificielle", "UI/UX Design"],
                "budget" => 65000,
                "duration_months" => 4
            ],
            [
                "title" => "Plateforme de Jeux Éducatifs pour Enfants",
                "description" => "Espace d'apprentissage ludique utilisant des techniques de gamification avancées pour engager les enfants. Chaque jeu est conçu par des pédagogues pour développer des compétences spécifiques (logique, créativité, résolution de problèmes). Le système adapte la difficulté en fonction des performances et des préférences d'apprentissage. Tableau de bord parental détaillé avec suivi des progrès et recommandations d'activités complémentaires. Contenu régulièrement mis à jour par une équipe d'enseignants et de game designers.",
                "domains" => ["Mobile", "UI/UX Design", "Intelligence Artificielle"],
                "budget" => 88000,
                "duration_months" => 7
            ],
            [
                "title" => "Système de Gestion de Flotte Automobile",
                "description" => "Solution complète pour les entreprises gérant des parcs de véhicules. Tracking GPS en temps réel, analyse des trajets optimaux, monitoring de l'état des véhicules (consommation, usure, besoin de maintenance). Alertes automatiques pour les comportements de conduite à risque. Intégration avec les systèmes de facturation et les outils de gestion des ressources humaines. Tableaux de bord personnalisables avec indicateurs clés de performance et reporting automatisé.",
                "domains" => ["Data Science", "Cloud Computing", "Mobile"],
                "budget" => 125000,
                "duration_months" => 8
            ],
            [
                "title" => "Outil de Design d'Intérieur en Réalité Augmentée",
                "description" => "Application permettant aux utilisateurs de visualiser des meubles et des décorations dans leur propre espace via leur smartphone. La technologie AR avancée prend en compte les dimensions exactes de la pièce et les conditions d'éclairage. Fonctionnalités sociales pour partager ses créations et recevoir des conseils. Intégration avec les catalogues des principaux retailers et options d'achat direct. Version pro avec outils de mesure avancés pour les designers d'intérieur.",
                "domains" => ["Mobile", "UI/UX Design", "Intelligence Artificielle"],
                "budget" => 110000,
                "duration_months" => 9
            ],
            [
                "title" => "Plateforme de Mentorat Professionnel",
                "description" => "Espace connectant des mentors expérimentés avec des mentees à travers un système de matching intelligent. L'algorithme prend en compte les objectifs professionnels, les personnalités et les disponibilités. Outils intégrés pour suivre les progrès, fixer des objectifs et préparer les sessions. Bibliothèque de ressources pédagogiques organisée par compétences. Fonctionnalités de networking pour élargir son cercle professionnel. Version entreprise avec gestion centralisée des programmes de mentorat interne.",
                "domains" => ["Développement Web", "Data Science", "Mobile"],
                "budget" => 78000,
                "duration_months" => 6
            ],
            [
                "title" => "Application de Journaling Intelligent",
                "description" => "Outil d'écriture réflexive enrichi par l'IA. Analyse des entrées pour identifier les schémas de pensée, les émotions dominantes et les progrès personnels. Rappels contextuels basés sur la localisation et les habitudes. Fonctionnalités de visualisation des données personnelles sur le long terme. Mode export pour partager des insights avec des professionnels de santé. Chiffrement fort des données sensibles avec option de verrouillage biométrique.",
                "domains" => ["Mobile", "Intelligence Artificielle", "Cybersécurité"],
                "budget" => 55000,
                "duration_months" => 4
            ],
            [
                "title" => "Système de Réservation Unifié pour Centres de Loisirs",
                "description" => "Solution centralisée de gestion des réservations pour complexes sportifs, piscines et centres de loisirs. Interface unifiée pour les administrateurs et les usagers. Gestion dynamique des créneaux et des capacités en fonction des contraintes sanitaires. Système de paiement intégré avec options d'abonnement. Application mobile avec QR code d'accès et suivi de la fréquentation en temps réel. Intégration avec les systèmes de caisse existants et outils de business intelligence.",
                "domains" => ["Développement Web", "Cloud Computing", "Mobile"],
                "budget" => 92000,
                "duration_months" => 7
            ]
        ];

        foreach ($realProjects as $projectData) {
            $user = $users->random();
            $startDate = Carbon::now()->addDays(rand(-30, 30));
            $endDate = $startDate->copy()->addMonths($projectData["duration_months"]);

            $project = Project::create([
                "title" => $projectData["title"],
                'slug' => Str::slug($projectData["title"]),
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

            // Création du chat pour le projet
            $groupChat = Chat::create([
                'type' => 'group',
                'name' => 'Équipe '.$project->title,
                'project_id' => $project->id
            ]);

            // Ajout du créateur au chat
            $groupChat->users()->attach($user->id);

            // Message de bienvenue
            Message::create([
                'chat_id' => $groupChat->id,
                'sender_id' => $user->id,
                'message' => "Projet '{$project->title}' créé ! Rejoignez la discussion."
            ]);

            // Ajouter d'autres membres au projet et au chat (entre 1 et 4 autres membres)
            $availableUsers = $users->where('id', '!=', $user->id);
            $additionalMemberCount = min(4, $availableUsers->count()); // Maximum 4 autres membres
            $additionalMemberCount = max(1, $additionalMemberCount); // Minimum 1 autre membre

            if ($additionalMemberCount > 0) {
                $additionalMembers = $availableUsers->random($additionalMemberCount);

                foreach ($additionalMembers as $member) {
                    $groupChat->users()->attach($member->id);

                    // Message de bienvenue pour les nouveaux membres
                    Message::create([
                        'chat_id' => $groupChat->id,
                        'sender_id' => $user->id,
                        'message' => "Bienvenue {$member->name} dans le projet '{$project->title}' !"
                    ]);
                }
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
