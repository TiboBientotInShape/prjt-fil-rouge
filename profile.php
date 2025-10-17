<?php
session_start();
require_once 'classes/Database.php';

// ✅ Vérification de connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = Database::getConnexion();
$userId = $_SESSION['user_id'];

// ✅ Récupération des infos utilisateur
$stmt = $pdo->prepare("SELECT pseudo, email, date_inscription FROM utilisateurs WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    die("Utilisateur introuvable.");
}

// ✅ Récupération des statistiques de jeu
// Nombre total de parties jouées
$stmt = $pdo->prepare("SELECT COUNT(*) FROM historique WHERE utilisateur_id = ?");
$stmt->execute([$userId]);
$totalParties = $stmt->fetchColumn();

// Thème préféré (le plus joué)
$stmt = $pdo->prepare("
    SELECT q.titre, COUNT(h.id) AS nb
    FROM historique h
    JOIN questionnaires q ON h.questionnaire_id = q.id
    WHERE h.utilisateur_id = ?
    GROUP BY q.id
    ORDER BY nb DESC
    LIMIT 1
");
$stmt->execute([$userId]);
$themePref = $stmt->fetch();
$themePrefere = $themePref ? $themePref['titre'] : "Aucun encore";

// ✅ Gestion de la modification du pseudo
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['new_pseudo'])) {
    $newPseudo = trim($_POST['new_pseudo']);

    if (strlen($newPseudo) < 3) {
        $message = "❌ Le pseudo doit contenir au moins 3 caractères.";
    } else {
        // Vérifier que le pseudo n'existe pas déjà
        $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE pseudo = ? AND id != ?");
        $check->execute([$newPseudo, $userId]);
        if ($check->fetch()) {
            $message = "⚠️ Ce pseudo est déjà utilisé par un autre joueur.";
        } else {
            // Mise à jour du pseudo
            $stmt = $pdo->prepare("UPDATE utilisateurs SET pseudo = ? WHERE id = ?");
            $stmt->execute([$newPseudo, $userId]);
            $_SESSION['user_pseudo'] = $newPseudo;
            $user['pseudo'] = $newPseudo;
            $message = "✅ Pseudo modifié avec succès !";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil - QuizMusic</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900 text-white min-h-screen">
    <div class="container mx-auto p-8 max-w-3xl">

        <!-- Titre principal -->
        <h1 class="text-4xl font-bold mb-8 text-center">👤 Mon profil</h1>

        <!-- Message de confirmation ou d’erreur -->
        <?php if ($message): ?>
            <div class="bg-white/20 text-center p-3 mb-6 rounded-lg">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Informations utilisateur -->
        <div class="bg-white/10 p-6 rounded-xl mb-6 shadow-lg">
            <p><strong>Pseudo :</strong> <?= htmlspecialchars($user['pseudo']) ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Date d’inscription :</strong> <?= htmlspecialchars($user['date_inscription']) ?></p>
            <p><strong>Parties jouées :</strong> <?= $totalParties ?></p>
            <p><strong>Thème préféré :</strong> <?= htmlspecialchars($themePrefere) ?></p>
        </div>

        <!-- Formulaire de modification du pseudo -->
        <form method="POST" class="bg-white/10 p-6 rounded-xl shadow-lg">
            <h2 class="text-2xl font-semibold mb-4">✏️ Modifier mon pseudo</h2>
            <input type="text" name="new_pseudo" placeholder="Nouveau pseudo"
                   class="w-full p-3 rounded-lg text-black mb-4" required>
            <button type="submit"
                    class="bg-purple-600 hover:bg-purple-700 px-5 py-3 rounded-lg font-semibold transition-all duration-200">
                Mettre à jour
            </button>
        </form>

        <!-- Lien de retour -->
        <div class="mt-8 text-center">
            <a href="index.php"
               class="text-purple-300 hover:underline text-lg">
               ⬅ Retour à l'accueil
            </a>
        </div>

    </div>
</body>
</html>
