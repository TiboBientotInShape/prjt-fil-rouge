<?php
/**
 * Page de quiz refactorisée avec POO
 * Utilise les classes Quiz, Question et leurs dérivées
 */

session_start();

// 📚 Vérification de connexion
// Si l'utilisateur n'est pas connecté, on le redirige vers login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 📚 Chargement de toutes les classes nécessaires
require_once 'classes/Database.php';
require_once 'classes/Question.php';
require_once 'classes/QuestionTexte.php';
require_once 'classes/QuestionImage.php';
require_once 'classes/QuestionAudio.php';
require_once 'classes/Quiz.php';

$theme = $_GET['theme'] ?? '';

if (empty($theme)) {
    header('Location: index.php');
    exit;
}

try {
    // 📚 CONCEPT POO : Instanciation du quiz
    // En une seule ligne, on :
    // - Charge les infos du thème
    // - Charge 5 questions aléatoires
    // - Instancie les bons objets (QuestionTexte, Image ou Audio)
    $quiz = new Quiz($theme);

    // 📚 Traitement du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $reponsesUtilisateur = $_POST['reponses'] ?? [];
        $tempsTotal = $_POST['temps_total'] ?? null;

        // 📚 CONCEPT POO : Appel de méthode
        // Le calcul du score est encapsulé dans la classe Quiz
        $score = $quiz->calculerScore($reponsesUtilisateur);

        // 📚 Sauvegarde en BDD
        $quiz->sauvegarderScore($_SESSION['user_id'], $score, $tempsTotal);

        // Sauvegarde en session pour la page résultat
        $_SESSION['dernier_score'] = $score;
        $_SESSION['dernier_theme'] = $theme;
        $_SESSION['total_questions'] = count($quiz->getQuestions());

        header('Location: resultat.php');
        exit;
    }

    $themeInfo = $quiz->getThemeInfo();
    $questions = $quiz->getQuestions();

} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz <?php echo htmlspecialchars($themeInfo['titre']); ?> - QuizMusic 🎵</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-4xl">

        <header class="text-center mb-8">
            <a href="index.php" class="inline-block text-purple-300 hover:text-white transition-colors mb-4">
                ← Retour à l'accueil
            </a>

            <div class="bg-gradient-to-r <?php echo $themeInfo['couleur']; ?> rounded-2xl p-6 text-white mb-8">
                <div class="text-4xl mb-2"><?php echo $themeInfo['emoji']; ?></div>
                <h1 class="text-3xl font-bold mb-2">Quiz <?php echo htmlspecialchars($themeInfo['titre']); ?></h1>
                <p class="text-white/80">Bonjour <?php echo htmlspecialchars($_SESSION['user_pseudo']); ?> ! Répondez aux 5 questions suivantes</p>
            </div>
        </header>

        <main>
            <form method="POST" class="space-y-8">
                <!-- Champ caché pour enregistrer le temps -->
                <input type="hidden" name="temps_total" id="temps_total" value="0">

                <?php foreach ($questions as $index => $question): ?>
                    <!-- 📚 CONCEPT POO : POLYMORPHISME EN ACTION -->
                    <!-- Peu importe si c'est QuestionTexte, QuestionImage ou QuestionAudio -->
                    <!-- On appelle afficherHTML() et chaque objet génère son propre HTML -->
                    <!-- C'est la MAGIE du polymorphisme ! -->
                    <?php echo $question->afficherHTML($index); ?>
                <?php endforeach; ?>

                <div class="text-center">
                    <button type="submit"
                            class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-bold py-4 px-8 rounded-2xl text-lg shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-200">
                        🏆 Voir mes résultats !
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
        // Chronomètre pour mesurer le temps de réponse
        let tempsDebut = Date.now();

        document.querySelector('form').addEventListener('submit', function() {
            let tempsTotal = Math.round((Date.now() - tempsDebut) / 1000);
            document.getElementById('temps_total').value = tempsTotal;
        });
    </script>
</body>
</html>
