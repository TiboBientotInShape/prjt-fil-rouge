<?php
/**
 * QuizMusic - Page d'historique
 * Jour 4 : Affichage de l'historique des scores depuis la BDD
 *
 * 📚 CONCEPT : Utilisation de la méthode User::getHistorique()
 * pour récupérer les scores avec jointure SQL
 */

session_start();

// Vérification de connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 📚 Chargement des classes
require_once 'classes/Database.php';
require_once 'classes/User.php';

// 📚 CONCEPT : Récupération de l'utilisateur depuis la session
// On a besoin de l'objet User pour appeler getHistorique()
$user = new User(
    $_SESSION['user_id'],
    $_SESSION['user_pseudo'],
    ''  // Email non nécessaire ici
);

// 📚 Récupération de l'historique
$historique = $user->getHistorique();

// 📚 CONCEPT : Calcul des statistiques globales
$totalParties = count($historique);
$totalPoints = 0;
$meilleurePerformance = 0;

foreach ($historique as $score) {
    $totalPoints += $score['score'];
    $pourcentage = ($score['score'] / $score['total_questions']) * 100;
    if ($pourcentage > $meilleurePerformance) {
        $meilleurePerformance = $pourcentage;
    }
}

// Calcul de la moyenne
$moyenne = $totalParties > 0 ? round($totalPoints / $totalParties, 1) : 0;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon historique - QuizMusic 🎵</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-6xl">

        <!-- Navigation -->
        <nav class="mb-8">
            <a href="index.php" class="text-purple-300 hover:text-white transition-colors">
                ← Retour à l'accueil
            </a>
        </nav>

        <!-- En-tête -->
        <header class="text-center mb-12">
            <h1 class="text-5xl font-bold text-white mb-4">
                📊 Mon historique
            </h1>
            <p class="text-xl text-purple-200">
                Toutes vos performances, <?php echo htmlspecialchars($_SESSION['user_pseudo']); ?> !
            </p>
        </header>

        <!-- Statistiques globales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total de parties -->
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center">
                <div class="text-5xl mb-2">🎮</div>
                <div class="text-4xl font-bold text-purple-600 mb-2"><?php echo $totalParties; ?></div>
                <div class="text-gray-600">Parties jouées</div>
            </div>

            <!-- Moyenne -->
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center">
                <div class="text-5xl mb-2">📈</div>
                <div class="text-4xl font-bold text-blue-600 mb-2"><?php echo $moyenne; ?>/5</div>
                <div class="text-gray-600">Score moyen</div>
            </div>

            <!-- Meilleure performance -->
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center">
                <div class="text-5xl mb-2">🏆</div>
                <div class="text-4xl font-bold text-yellow-600 mb-2"><?php echo round($meilleurePerformance); ?>%</div>
                <div class="text-gray-600">Meilleure performance</div>
            </div>
        </div>

        <!-- Tableau d'historique -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6">
                <h2 class="text-2xl font-bold text-white">
                    📜 Historique détaillé
                </h2>
            </div>

            <?php if (empty($historique)): ?>
                <!-- Message si aucune partie jouée -->
                <div class="p-12 text-center">
                    <div class="text-6xl mb-4">🎵</div>
                    <p class="text-xl text-gray-600 mb-6">
                        Vous n'avez pas encore joué de quiz !
                    </p>
                    <a href="index.php"
                       class="inline-block bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-200">
                        🎯 Commencer maintenant
                    </a>
                </div>
            <?php else: ?>
                <!-- Tableau responsive -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b-2 border-gray-200">
                            <tr>
                                <th class="p-4 text-left font-semibold text-gray-700">Thème</th>
                                <th class="p-4 text-center font-semibold text-gray-700">Score</th>
                                <th class="p-4 text-center font-semibold text-gray-700">Réussite</th>
                                <th class="p-4 text-center font-semibold text-gray-700">Temps</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historique as $index => $score): ?>
                                <?php
                                // 📚 Calcul du pourcentage
                                $pourcentage = ($score['score'] / $score['total_questions']) * 100;

                                // 📚 Détermination de la couleur selon le score
                                if ($pourcentage >= 80) {
                                    $couleur = 'text-green-600 bg-green-50';
                                } elseif ($pourcentage >= 60) {
                                    $couleur = 'text-blue-600 bg-blue-50';
                                } elseif ($pourcentage >= 40) {
                                    $couleur = 'text-orange-600 bg-orange-50';
                                } else {
                                    $couleur = 'text-red-600 bg-red-50';
                                }

                                // 📚 Formatage de la date
                                $date = new DateTime($score['date_jeu']);
                                $dateFormatee = $date->format('d/m/Y à H:i');

                                // 📚 Formatage du temps
                                $tempsAffiche = $score['temps_seconde'] ?
                                    floor($score['temps_seconde'] / 60) . 'min ' . ($score['temps_seconde'] % 60) . 's' :
                                    '-';
                                ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors <?php echo $index % 2 === 0 ? 'bg-white' : 'bg-gray-50'; ?>">
                                    <!-- Thème -->
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <span class="text-2xl"><?php echo $score['emoji']; ?></span>
                                            <span class="font-medium text-gray-800">
                                                <?php echo htmlspecialchars($score['theme_titre']); ?>
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Score -->
                                    <td class="p-4 text-center">
                                        <span class="text-xl font-bold text-gray-800">
                                            <?php echo $score['score']; ?> / <?php echo $score['total_questions']; ?>
                                        </span>
                                    </td>

                                    <!-- Pourcentage -->
                                    <td class="p-4 text-center">
                                        <span class="px-3 py-1 rounded-full font-semibold <?php echo $couleur; ?>">
                                            <?php echo round($pourcentage); ?>%
                                        </span>
                                    </td>

                                    <!-- Temps -->
                                    <td class="p-4 text-center text-gray-600">
                                        ⏱️ <?php echo $tempsAffiche; ?>
                                    </td>

                                    <!-- Date -->
                                    <td class="p-4 text-gray-600">
                                        <?php echo $dateFormatee; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Bouton de retour -->
        <div class="text-center mt-8">
            <a href="index.php"
               class="inline-block bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-bold py-4 px-8 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                🏠 Retour à l'accueil
            </a>
        </div>
    </div>
</body>

</html>
