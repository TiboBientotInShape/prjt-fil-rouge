<?php
session_start();
require_once 'classes/Database.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = Database::getConnexion();
$user_id = $_SESSION['user_id'];

// 🧭 1. Récupération des infos de l'utilisateur
$stmt = $pdo->prepare("SELECT pseudo, email, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// 🧭 2. Si le formulaire de changement de pseudo est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nouveau_pseudo'])) {
    $nouveau_pseudo = trim($_POST['nouveau_pseudo']);

    if (!empty($nouveau_pseudo)) {
        // Met à jour le pseudo dans la BDD
        $update = $pdo->prepare("UPDATE users SET pseudo = ? WHERE id = ?");
        $update->execute([$nouveau_pseudo, $user_id]);

        // Met à jour la session
        $_SESSION['user_pseudo'] = $nouveau_pseudo;

        // Recharge la page
        header('Location: profile.php');
        exit;
    }
}

// 🧮 3. Nombre total de parties jouées
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_parties FROM scores WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_parties = $stmt->fetchColumn();

// 🧭 4. Thème préféré (le plus joué)
$stmt = $pdo->prepare("
    SELECT q.titre, COUNT(*) AS nb_parties
    FROM scores s
    JOIN questionnaires q ON s.questionnaire_id = q.id
    WHERE s.user_id = ?
    GROUP BY s.questionnaire_id
    ORDER BY nb_parties DESC
    LIMIT 1
");
$stmt->execute([$user_id]);
$theme_pref = $stmt->fetchColumn() ?: 'Aucun thème joué pour l’instant 😅';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil de <?php echo htmlspecialchars($user['pseudo']); ?> - QuizMusic 🎵</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 min-h-screen text-white">

<div class="max-w-3xl mx-auto py-10 px-6">
    <a href="index.php" class="text-purple-300 hover:text-white mb-6 inline-block">← Retour à l'accueil</a>

    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 shadow-lg">
        <h1 class="text-3xl font-bold mb-6 text-center">👤 Profil de <?php echo htmlspecialchars($user['pseudo']); ?></h1>

        <div class="space-y-4">
            <p><strong>Pseudo :</strong> <?php echo htmlspecialchars($user['pseudo']); ?></p>
            <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Date d’inscription :</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
            <p><strong>Nombre total de parties jouées :</strong> <?php echo $total_parties; ?></p>
            <p><strong>Thème préféré :</strong> <?php echo htmlspecialchars($theme_pref); ?></p>
        </div>

        <hr class="my-6 border-white/20">

        <h2 class="text-2xl font-semibold mb-4">✏️ Modifier votre pseudo</h2>
        <form method="POST" class="flex items-center gap-3">
            <input type="text" name="nouveau_pseudo" placeholder="Nouveau pseudo"
                   class="flex-grow px-4 py-2 rounded-xl text-black" required>
            <button type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-2 rounded-xl">
                Mettre à jour
            </button>
        </form>
    </div>
</div>

</body>
</html>
