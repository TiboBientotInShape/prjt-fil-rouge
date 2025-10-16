<?php
/**
 * QuizMusic - Page de quiz
 * Jour 1 : PHP Procédural - Affichage des questions et traitement des réponses
 * 
 * OBJECTIF PÉDAGOGIQUE :
 * Ce fichier enseigne :
 * - Récupération de paramètres GET ($_GET)
 * - Validation et sécurité des données d'entrée
 * - Redirections avec header() et exit
 * - Traitement de formulaires POST ($_POST)
 * - Variables superglobales ($_SERVER)
 * - Boucles foreach pour traiter des données
 * - Sessions pour conserver l'état
 * - Sécurité XSS avec htmlspecialchars()
 */

// Démarrage de la session
session_start();

// 📚 CONCEPT : Récupération de paramètres GET
// $_GET est une variable superglobale qui contient les paramètres de l'URL
// L'opérateur ?? (null coalescing) renvoie '' si $_GET['theme'] n'existe pas
$theme = $_GET['theme'] ?? '';

// 📚 CONCEPT : Validation des données d'entrée
// TOUJOURS valider les données reçues de l'utilisateur !
// empty() vérifie si la variable est vide, null ou n'existe pas
if (empty($theme)) {
    // 📚 CONCEPT : Redirection HTTP
    // header() envoie un en-tête HTTP au navigateur
    // 'Location:' indique au navigateur de changer de page
    header('Location: index.php');
    
    // 📚 CONCEPT : exit / die
    // Arrête l'exécution du script immédiatement
    // OBLIGATOIRE après une redirection !
    exit;
}

// 📚 CONCEPT : Structure de données complexe
// Tableau associatif à 3 dimensions :
// 1. Clé = thème ('rock', 'pop_fr'...)
// 2. Tableau numériqü des questions (0, 1, 2...)
// 3. Tableau associatif pour chaque question
$questions_par_theme = [
    'rock' => [
        [
            'question' => 'Qui est le chanteur du groupe Queen ?',
            'reponses' => ['Freddie Mercury', 'John Lennon', 'Mick Jagger', 'Robert Plant'],
            'bonne_reponse' => 0
        ],
        [
            'question' => 'Quel groupe a composé "Stairway to Heaven" ?',
            'reponses' => ['Deep Purple', 'Led Zeppelin', 'Black Sabbath', 'Pink Floyd'],
            'bonne_reponse' => 1
        ],
        [
            'question' => 'Dans quel groupe jouait John Lennon ?',
            'reponses' => ['The Rolling Stones', 'The Who', 'The Beatles', 'The Kinks'],
            'bonne_reponse' => 2
        ],
        [
            'question' => 'Quel instrument joue principalement Slash ?',
            'reponses' => ['Basse', 'Batterie', 'Piano', 'Guitare'],
            'bonne_reponse' => 3
        ],
        [
            'question' => 'Qui chante "Bohemian Rhapsody" ?',
            'reponses' => ['Queen', 'AC/DC', 'Metallica', 'Iron Maiden'],
            'bonne_reponse' => 0
        ]
    ],
    
    'pop_fr' => [
        [
            'question' => 'Qui chante "Alors on danse" ?',
            'reponses' => ['Stromae', 'Soprano', 'Maître Gims', 'Black M'],
            'bonne_reponse' => 0
        ],
        [
            'question' => 'Quel groupe interprète "L\'Aventurier" ?',
            'reponses' => ['Téléphone', 'Trust', 'Indochine', 'Noir Désir'],
            'bonne_reponse' => 2
        ],
        [
            'question' => 'Qui a écrit "Ne me quitte pas" ?',
            'reponses' => ['Charles Aznavour', 'Georges Brassens', 'Jacques Brel', 'Léo Ferré'],
            'bonne_reponse' => 2
        ],
        [
            'question' => 'Quel groupe chante "Ton invitation" ?',
            'reponses' => ['Louise Attaque', 'Noir Désir', 'Bénabar', 'Miossec'],
            'bonne_reponse' => 0
        ],
        [
            'question' => 'Qui interprète "Mistral gagnant" ?',
            'reponses' => ['Alain Souchon', 'Renaud', 'Francis Cabrel', 'Jean-Jacques Goldman'],
            'bonne_reponse' => 1
        ]
    ],
    
    'rap_us' => [
        [
            'question' => 'Comment s\'appelle le vrai nom d\'Eminem ?',
            'reponses' => ['Marshall Mathers', 'Calvin Broadus', 'Curtis Jackson', 'Shawn Carter'],
            'bonne_reponse' => 0
        ],
        [
            'question' => 'Quel rappeur a sorti l\'album "The Chronic" ?',
            'reponses' => ['Snoop Dogg', 'Dr. Dre', 'Ice Cube', 'Eazy-E'],
            'bonne_reponse' => 1
        ],
        [
            'question' => 'Qui a créé le label "Roc-A-Fella Records" ?',
            'reponses' => ['Kanye West', 'Nas', 'Jay-Z', 'Biggie'],
            'bonne_reponse' => 2
        ],
        [
            'question' => 'Quel rappeur est surnommé "King of the South" ?',
            'reponses' => ['Lil Wayne', 'Outkast', 'Ludacris', 'T.I.'],
            'bonne_reponse' => 3
        ],
        [
            'question' => 'Qui a sorti l\'album "good kid, m.A.A.d city" ?',
            'reponses' => ['Kendrick Lamar', 'J. Cole', 'Drake', 'Big Sean'],
            'bonne_reponse' => 0
        ]
    ],
    
    'electro' => [
        [
            'question' => 'Quel duo français a composé "Around the World" ?',
            'reponses' => ['Daft Punk', 'Justice', 'Modjo', 'Air'],
            'bonne_reponse' => 0
        ],
        [
            'question' => 'David Guetta est originaire de quelle ville ?',
            'reponses' => ['Lyon', 'Paris', 'Marseille', 'Nice'],
            'bonne_reponse' => 1
        ],
        [
            'question' => 'Quel DJ est connu pour ses masques colorés ?',
            'reponses' => ['Deadmau5', 'Skrillex', 'Marshmello', 'Daft Punk'],
            'bonne_reponse' => 2
        ],
        [
            'question' => 'Avicii était originaire de quel pays ?',
            'reponses' => ['Norvège', 'Danemark', 'Finlande', 'Suède'],
            'bonne_reponse' => 3
        ],
        [
            'question' => 'Qui a composé "Levels" ?',
            'reponses' => ['Avicii', 'Calvin Harris', 'Tiësto', 'Armin van Buuren'],
            'bonne_reponse' => 0
        ]
    ],
    
    'disney' => [
        [
            'question' => 'Dans quel film Disney entend-on "Libérée, délivrée" ?',
            'reponses' => ['La Reine des Neiges', 'Moana', 'Raiponce', 'La Belle et la Bête'],
            'bonne_reponse' => 0
        ],
        [
            'question' => 'Qui chante "Je veux y croire" dans Pocahontas ?',
            'reponses' => ['Pocahontas', 'John Smith', 'Grand-mère Feuillage', 'Ratcliffe'],
            'bonne_reponse' => 0
        ],
        [
            'question' => 'Dans quel film trouve-t-on la chanson "Hakuna Matata" ?',
            'reponses' => ['Tarzan', 'Le Livre de la Jungle', 'Le Roi Lion', 'Mulan'],
            'bonne_reponse' => 2
        ],
        [
            'question' => 'Qui compose les musiques des films Pixar ?',
            'reponses' => ['Alan Menken', 'Randy Newman', 'Michael Giacchino', 'Les trois'],
            'bonne_reponse' => 3
        ],
        [
            'question' => 'Dans Aladdin, comment s\'appelle le singe ?',
            'reponses' => ['Rajah', 'Iago', 'Abu', 'Jafar'],
            'bonne_reponse' => 2
        ]
    ]
];

// Informations sur les thèmes pour l'affichage
$themes_info = [
    'rock' => ['titre' => 'Rock Classique', 'emoji' => '🎸', 'couleur' => 'from-red-500 to-pink-600'],
    'pop_fr' => ['titre' => 'Pop Française', 'emoji' => '🇫🇷', 'couleur' => 'from-blue-500 to-purple-600'],
    'rap_us' => ['titre' => 'Rap US', 'emoji' => '🎤', 'couleur' => 'from-gray-700 to-gray-900'],
    'electro' => ['titre' => 'Électro/Dance', 'emoji' => '🎛️', 'couleur' => 'from-green-400 to-blue-500'],
    'disney' => ['titre' => 'Disney/Dessins animés', 'emoji' => '🎬', 'couleur' => 'from-yellow-400 to-orange-500']
];

// 📚 CONCEPT : Vérification d'existence de clé
// isset() vérifie si une clé existe dans un tableau
// Le ! (NOT) inverse le résultat : true devient false, false devient true
if (!isset($questions_par_theme[$theme])) {
    // Si le thème n'existe pas, redirection de sécurité
    header('Location: index.php');
    exit;
}

// Récupération des questions du thème choisi
$questions = $questions_par_theme[$theme];
$theme_info = $themes_info[$theme];

// 📚 CONCEPT : Traitement de formulaire POST
// $_SERVER['REQUEST_METHOD'] indique comment la page a été appelée
// 'POST' = formulaire soumis, 'GET' = page chargée normalement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 📚 CONCEPT : Initialisation de variables
    // On commence toujours le score à 0
    $score = 0;
    
    // 📚 CONCEPT : Récupération de données POST
    // $_POST contient les données du formulaire soumis
    // ?? [] renvoie un tableau vide si 'reponses' n'existe pas
    $reponses_utilisateur = $_POST['reponses'] ?? [];
    
    // 📚 CONCEPT : Boucle de vérification
    // On parcourt chaque question pour vérifier la réponse
    foreach ($questions as $index => $question) {
        // 📚 CONCEPT : Conversion de type et valeur par défaut
        // (int) convertit en nombre entier
        // ?? -1 donne -1 si la réponse n'existe pas (valeur impossible)
        $reponse_utilisateur = (int)($reponses_utilisateur[$index] ?? -1);
        
        // 📚 CONCEPT : Comparaison stricte
        // === compare valeur ET type (plus sécurisé que ==)
        if ($reponse_utilisateur === $question['bonne_reponse']) {
            $score++;  // Réponse correcte : on incrémente le score
        }
    }
    
    // 📚 CONCEPT : Sauvegarde en session
    // Les sessions permettent de conserver des données entre les pages
    $_SESSION['dernier_score'] = $score;
    $_SESSION['dernier_theme'] = $theme;
    $_SESSION['total_questions'] = count($questions);  // count() = nombre d'éléments
    
    // 📚 CONCEPT : Redirection après traitement
    // Pattern POST-REDIRECT-GET : toujours rediriger après un POST
    header('Location: resultat.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz <?php echo $theme_info['titre']; ?> - QuizMusic 🎵</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    backgroundImage: {
                        'gradient-quiz': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        
        <!-- En-tête du quiz -->
        <header class="text-center mb-8">
            <a href="index.php" class="inline-block text-purple-300 hover:text-white transition-colors mb-4">
                ← Retour à l'accueil
            </a>
            
            <div class="bg-gradient-to-r <?php echo $theme_info['couleur']; ?> rounded-2xl p-6 text-white mb-8">
                <div class="text-4xl mb-2"><?php echo $theme_info['emoji']; ?></div>
                <h1 class="text-3xl font-bold mb-2">Quiz <?php echo $theme_info['titre']; ?></h1>
                <p class="text-white/80">Répondez aux 5 questions suivantes</p>
            </div>
        </header>

        <!-- Formulaire du quiz -->
        <main>
            <form method="POST" class="space-y-8">
                
                <?php foreach ($questions as $index => $question): ?>
                    <!-- Question individuelle -->
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <div class="flex items-start gap-4 mb-6">
                            <span class="bg-gradient-to-r <?php echo $theme_info['couleur']; ?> text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                <?php echo $index + 1; ?>
                            </span>
                            <h3 class="text-xl font-semibold text-gray-800 leading-relaxed">
                                <?php 
                                // 📚 CONCEPT : Sécurité XSS
                                // htmlspecialchars() protège contre les attaques XSS
                                // Convertit les caractères spéciaux HTML (<, >, &, ", ')
                                // TOUJOURS utiliser cette fonction pour afficher du contenu utilisateur !
                                echo htmlspecialchars($question['question']); 
                                ?>
                            </h3>
                        </div>
                        
                        <!-- Réponses multiples -->
                        <div class="space-y-3 ml-12">
                            <?php foreach ($question['reponses'] as $reponse_index => $reponse): ?>
                                <label class="flex items-center p-4 rounded-xl border-2 border-gray-200 hover:border-purple-300 hover:bg-purple-50 cursor-pointer transition-all duration-200 group">
                                    <input type="radio" 
                                           name="reponses[<?php echo $index; ?>]" 
                                           value="<?php echo $reponse_index; ?>"
                                           class="w-5 h-5 text-purple-600 border-gray-300 focus:ring-purple-500 focus:ring-2"
                                           required>
                                    <span class="ml-3 text-gray-700 group-hover:text-purple-700 font-medium">
                                        <?php 
                                        // 📚 CONCEPT : Protection systématique
                                        // Chaque donnée affichée doit être protégée
                                        echo htmlspecialchars($reponse); 
                                        ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <!-- Bouton de soumission -->
                <div class="text-center">
                    <button type="submit" 
                            class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-bold py-4 px-8 rounded-2xl text-lg shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-200">
                        🏆 Voir mes résultats !
                    </button>
                </div>
            </form>
        </main>
    </div>

    <!-- JavaScript pour améliorer l'expérience utilisateur -->
    <script>
        // Animation des réponses sélectionnées
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Retirer la classe active de toutes les labels du même groupe
                const name = this.name;
                document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                    r.closest('label').classList.remove('border-purple-500', 'bg-purple-100');
                });
                
                // Ajouter la classe active au label sélectionné
                this.closest('label').classList.add('border-purple-500', 'bg-purple-100');
            });
        });

        // Confirmation avant soumission
        document.querySelector('form').addEventListener('submit', function(e) {
            const totalQuestions = <?php echo count($questions); ?>;
            const reponsesSelectionnees = document.querySelectorAll('input[type="radio"]:checked').length;
            
            if (reponsesSelectionnees < totalQuestions) {
                e.preventDefault();
                alert('Veuillez répondre à toutes les questions avant de continuer !');
                return;
            }
            
            // Animation du bouton de soumission
            const btn = document.querySelector('button[type="submit"]');
            btn.innerHTML = '⏳ Calcul en cours...';
            btn.disabled = true;
        });
    </script>
</body>
</html>