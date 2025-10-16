<?php
/**
 * QuizMusic - Page des résultats
 * Jour 1 : PHP Procédural - Affichage du score et du niveau atteint
 * 
 * OBJECTIF PÉDAGOGIQUE :
 * Ce fichier enseigne :
 * - Gestion avancée des sessions
 * - Fonctions avec paramètres multiples
 * - Structures conditionnelles complexes (if/elseif/else)
 * - Calculs mathématiques en PHP
 * - Logique métier (détermination de niveau)
 * - Tableaux multidimensionnels pour les données
 * - Opérateur ternaire pour l'affichage conditionnel
 */

// Démarrage de la session
session_start();

// 📚 CONCEPT : Vérification de données de session
// On s'assure que l'utilisateur a bien passé un quiz avant d'afficher les résultats
// || (OU logique) : la condition est vraie si AU MOINS une des parties est vraie
if (!isset($_SESSION['dernier_score']) || !isset($_SESSION['dernier_theme'])) {
    // 📚 CONCEPT : Sécurité d'accès aux pages
    // Si quelqu'un essaie d'accéder directement à cette page, on le redirige
    header('Location: index.php');
    exit;
}

// Récupération des données de la session
$score = $_SESSION['dernier_score'];
$theme = $_SESSION['dernier_theme'];
$total_questions = $_SESSION['total_questions'];

// Informations sur les thèmes
$themes_info = [
    'rock' => ['titre' => 'Rock Classique', 'emoji' => '🎸', 'couleur' => 'from-red-500 to-pink-600'],
    'pop_fr' => ['titre' => 'Pop Française', 'emoji' => '🇫🇷', 'couleur' => 'from-blue-500 to-purple-600'],
    'rap_us' => ['titre' => 'Rap US', 'emoji' => '🎤', 'couleur' => 'from-gray-700 to-gray-900'],
    'electro' => ['titre' => 'Électro/Dance', 'emoji' => '🎛️', 'couleur' => 'from-green-400 to-blue-500'],
    'disney' => ['titre' => 'Disney/Dessins animés', 'emoji' => '🎬', 'couleur' => 'from-yellow-400 to-orange-500']
];

$theme_info = $themes_info[$theme] ?? $themes_info['rock'];

// 📚 CONCEPT : Fonction métier complexe
// Cette fonction encapsule la logique métier de détermination du niveau
// Elle prend 2 paramètres et renvoie un tableau d'informations
function determinerNiveau($score, $total) {
    // 📚 CONCEPT : Calcul de pourcentage
    // On divise le score par le total et multiplie par 100
    // Exemple : 4/5 * 100 = 80%
    $pourcentage = ($score / $total) * 100;
    
    // 📚 CONCEPT : Structure conditionnelle étendue
    // On teste les conditions du meilleur score au plus faible
    // if/elseif/else permet de tester plusieurs conditions dans l'ordre
    if ($pourcentage >= 90) {
        // 📚 CONCEPT : Retour de tableau associatif
        // On renvoie toutes les informations nécessaires dans un seul tableau
        // Cela évite de faire plusieurs fonctions séparées
        return [
            'nom' => 'Maître',
            'emoji' => '🏆',
            'badge' => 'Virtuose musical',
            'commentaire' => 'Exceptionnel ! Vous êtes un véritable expert. Bravo pour cette performance parfaite !',
            'couleur' => 'from-yellow-400 to-orange-500',
            'note' => 'A+'
        ];
    } elseif ($pourcentage >= 70) {
        return [
            'nom' => 'Expert',
            'emoji' => '🎸',
            'badge' => 'Connaisseur',
            'commentaire' => 'Impressionnant ! Vous maîtrisez vraiment votre sujet. Peu de choses vous échappent !',
            'couleur' => 'from-green-500 to-green-600',
            'note' => 'A'
        ];
    } elseif ($pourcentage >= 50) {
        return [
            'nom' => 'Confirmé',
            'emoji' => '🎶',
            'badge' => 'Mélomane averti',
            'commentaire' => 'Bravo ! Vous avez de bonnes bases musicales. Votre culture s\'étend bien !',
            'couleur' => 'from-blue-500 to-blue-600',
            'note' => 'B'
        ];
    } elseif ($pourcentage >= 30) {
        return [
            'nom' => 'Amateur',
            'emoji' => '🎵',
            'badge' => 'Auditeur curieux',
            'commentaire' => 'Pas mal ! Vous commencez à reconnaître quelques classiques. Continuez à explorer !',
            'couleur' => 'from-purple-500 to-purple-600',
            'note' => 'C'
        ];
    } else {
        return [
            'nom' => 'Débutant',
            'emoji' => '🔇',
            'badge' => 'Mélomane en herbe',
            'commentaire' => 'C\'est un début ! La musique n\'a pas encore de secrets pour vous, mais c\'est le moment d\'ouvrir grand vos oreilles !',
            'couleur' => 'from-gray-500 to-gray-600',
            'note' => 'D'
        ];
    }
}

// 📚 CONCEPT : Appel de fonction et stockage du résultat
// On appelle notre fonction avec les paramètres nécessaires
$niveau = determinerNiveau($score, $total_questions);

// 📚 CONCEPT : Fonction mathématique round()
// round() arrondit un nombre à l'entier le plus proche
// Exemple : 83.7% devient 84%
$pourcentage = round(($score / $total_questions) * 100);

// 📚 CONCEPT : Fonction de logique métier
// Cette fonction adapte les conseils selon le niveau de l'utilisateur
// Elle utilise un tableau associatif pour mapper niveau -> conseils
function obtenirConseils($niveau_nom) {
    // 📚 CONCEPT : Tableau multidimensionnel de données
    // Chaque clé (niveau) correspond à un tableau de conseils
    $conseils = [
        'Maître' => [
            '🎯 Essayez d\'autres thèmes pour confirmer votre expertise !',
            '📚 Vous pourriez animer des quiz musicaux avec vos amis',
            '🎼 Votre culture musicale est impressionnante !'
        ],
        'Expert' => [
            '🎵 Explorez des sous-genres moins connus pour approfondir',
            '📖 Quelques lectures sur l\'histoire musicale pourraient vous plaire',
            '🎧 Essayez d\'autres thèmes pour étendre vos connaissances'
        ],
        'Confirmé' => [
            '🎶 Écoutez plus d\'artistes de ce genre musical',
            '📺 Les documentaires musicaux sont parfaits pour vous',
            '🎤 Tentez d\'autres quiz pour découvrir vos points forts'
        ],
        'Amateur' => [
            '🎧 Créez des playlists pour découvrir de nouveaux artistes',
            '📱 Les applications musicales peuvent vous aider à explorer',
            '👥 Échangez avec d\'autres mélomanes pour apprendre'
        ],
        'Débutant' => [
            '🎶 Commencez par les "greatest hits" du genre',
            '📚 Les bases de l\'histoire musicale vous aideront',
            '🎵 Écoutez régulièrement et sans pression !'
        ]
    ];
    
    // 📚 CONCEPT : Opérateur null coalescing avec tableau
    // Si le niveau n'existe pas, on renvoie les conseils 'Amateur' par défaut
    // C'est une sécurité pour éviter les erreurs
    return $conseils[$niveau_nom] ?? $conseils['Amateur'];
}

$conseils = obtenirConseils($niveau['nom']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats - Quiz <?php echo $theme_info['titre']; ?> - QuizMusic 🎵</title>
    
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
        
        <!-- Animation d'entrée -->
        <div class="text-center mb-8 animate-fade-in">
            <!-- Titre des résultats -->
            <h1 class="text-4xl font-bold text-white mb-4">
                🏆 Vos résultats
            </h1>
            <p class="text-purple-200 text-lg">Quiz <?php echo $theme_info['titre']; ?></p>
        </div>

        <!-- Card principale des résultats -->
        <main class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-8">
            
            <!-- En-tête avec le score principal -->
            <div class="bg-gradient-to-r <?php echo $niveau['couleur']; ?> p-8 text-white text-center">
                <div class="text-6xl mb-4"><?php echo $niveau['emoji']; ?></div>
                <h2 class="text-3xl font-bold mb-2">Niveau <?php echo $niveau['nom']; ?></h2>
                <p class="text-xl opacity-90 mb-4"><?php echo $niveau['badge']; ?></p>
                
                <!-- Score numérique -->
                <div class="bg-white/20 rounded-2xl p-4 inline-block">
                    <div class="text-4xl font-bold"><?php echo $score; ?>/<?php echo $total_questions; ?></div>
                    <div class="text-sm opacity-80">Score final</div>
                </div>
            </div>
            
            <!-- Contenu détaillé -->
            <div class="p-8">
                
                <!-- Statistiques détaillées -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="text-center p-4 bg-gray-50 rounded-xl">
                        <div class="text-2xl font-bold text-gray-800"><?php echo $pourcentage; ?>%</div>
                        <div class="text-gray-600">Réussite</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-xl">
                        <div class="text-2xl font-bold text-green-600"><?php echo $score; ?></div>
                        <div class="text-gray-600">Bonnes réponses</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-xl">
                        <div class="text-2xl font-bold text-<?php 
                        // 📚 CONCEPT : Opérateur ternaire
                        // condition ? valeur_si_vrai : valeur_si_faux
                        // Plus concis qu'un if/else pour des cas simples
                        echo $niveau['nom'] == 'Maître' ? 'yellow' : 'purple'; 
                        ?>-600">
                            <?php echo $niveau['note']; ?>
                        </div>
                        <div class="text-gray-600">Note</div>
                    </div>
                </div>
                
                <!-- Commentaire personnalisé -->
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-2xl p-6 mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">
                        💬 Votre évaluation personnalisée
                    </h3>
                    <p class="text-gray-700 leading-relaxed text-lg">
                        <?php echo $niveau['commentaire']; ?>
                    </p>
                </div>
                
                <!-- Conseils d'amélioration -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">
                        💡 Conseils pour progresser
                    </h3>
                    <div class="space-y-3">
                        <?php 
                    // 📚 CONCEPT : Boucle foreach simple
                    // Ici on n'a besoin que de la valeur (pas de clé)
                    // $conseil contiendra chaque string du tableau de conseils
                    foreach ($conseils as $conseil): 
                    ?>
                            <div class="flex items-start gap-3 p-3 bg-yellow-50 rounded-lg">
                                <span class="text-yellow-600 text-sm">●</span>
                                <span class="text-gray-700"><?php echo $conseil; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Barre de progression visuelle -->
                <div class="mb-8">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Progression</span>
                        <span><?php echo $pourcentage; ?>%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r <?php echo $niveau['couleur']; ?> h-3 rounded-full transition-all duration-1000 ease-out" 
                             style="width: <?php echo $pourcentage; ?>%"></div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Actions disponibles -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Rejouer le même quiz -->
            <a href="quiz.php?theme=<?php echo $theme; ?>" 
               class="bg-gradient-to-r <?php echo $theme_info['couleur']; ?> hover:scale-105 text-white font-semibold py-4 px-6 rounded-2xl text-center transition-all duration-200 shadow-xl hover:shadow-2xl">
                <div class="text-2xl mb-2"><?php echo $theme_info['emoji']; ?></div>
                <div class="text-lg">Rejouer ce quiz</div>
                <div class="text-sm opacity-80">Améliorer votre score</div>
            </a>
            
            <!-- Changer de thème -->
            <a href="index.php" 
               class="bg-white hover:bg-gray-50 text-gray-800 font-semibold py-4 px-6 rounded-2xl text-center transition-all duration-200 shadow-xl hover:shadow-2xl border-2 border-gray-200 hover:scale-105">
                <div class="text-2xl mb-2">🎵</div>
                <div class="text-lg">Nouveau thème</div>
                <div class="text-sm text-gray-600">Explorer d'autres styles</div>
            </a>
        </div>

        <!-- Encouragement final -->
        <div class="text-center mt-8 p-6 bg-white/10 backdrop-blur-sm rounded-2xl">
            <p class="text-purple-200 text-lg">
                <?php 
                // 📚 CONCEPT : Structure conditionnelle complexe en PHP/HTML
                // On peut utiliser if/elseif/else dans le HTML pour afficher différents contenus
                if ($pourcentage >= 80): 
                ?>
                    🌟 Fantastique ! Vous êtes vraiment doué(e) !
                <?php elseif ($pourcentage >= 60): ?>
                    🎯 Très bien joué ! Continuez sur cette lancée !
                <?php elseif ($pourcentage >= 40): ?>
                    💪 Bon travail ! L'entraînement paie toujours !
                <?php else: ?>
                    🚀 Chaque expert a commencé quelque part ! À vous de jouer !
                <?php 
                // 📚 CONCEPT : Fin de structure conditionnelle
                // endif; termine le if commencé avec if:
                endif; 
                ?>
            </p>
        </div>
    </div>

    <!-- JavaScript pour les animations -->
    <script>
        // Animation de la barre de progression
        window.addEventListener('load', function() {
            const progressBar = document.querySelector('.bg-gradient-to-r.h-3');
            if (progressBar) {
                progressBar.style.width = '0%';
                setTimeout(() => {
                    progressBar.style.width = '<?php echo $pourcentage; ?>%';
                }, 500);
            }
        });

        // Animation des cartes au chargement
        const cards = document.querySelectorAll('.bg-gray-50, .bg-gradient-to-r');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease-out';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });

        // Confettis virtuels pour les excellents scores
        <?php if ($pourcentage >= 80): ?>
        function createConfetti() {
            const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#f9ca24', '#6c5ce7'];
            for (let i = 0; i < 15; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.style.position = 'fixed';
                    confetti.style.width = '10px';
                    confetti.style.height = '10px';
                    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.left = Math.random() * window.innerWidth + 'px';
                    confetti.style.top = '-10px';
                    confetti.style.pointerEvents = 'none';
                    confetti.style.borderRadius = '50%';
                    confetti.style.zIndex = '9999';
                    
                    document.body.appendChild(confetti);
                    
                    const animation = confetti.animate([
                        { transform: 'translateY(0) rotate(0deg)', opacity: 1 },
                        { transform: `translateY(${window.innerHeight + 20}px) rotate(360deg)`, opacity: 0 }
                    ], {
                        duration: 3000,
                        easing: 'ease-in'
                    });
                    
                    animation.onfinish = () => confetti.remove();
                }, i * 100);
            }
        }
        
        // Lancer les confettis après un délai
        setTimeout(createConfetti, 1000);
        <?php endif; ?>
    </script>
</body>
</html>