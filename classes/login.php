<?php
/**
 * Page de connexion
 * Permet à un utilisateur existant de se connecter
 */

// 📚 CONCEPT : Démarrage de session
// TOUJOURS en premier, avant tout HTML
session_start();

// 📚 Chargement des classes nécessaires
require_once 'classes/Database.php';
require_once 'classes/User.php';

// Variable pour stocker les erreurs
$erreur = '';

// 📚 CONCEPT : Traitement du formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // 📚 Tentative de connexion
    $user = User::login($email, $password);
    
    if ($user) {
        // ✅ Connexion réussie
        // 📚 CONCEPT : Stockage en session
        // Les sessions permettent de garder l'utilisateur connecté
        // entre les pages
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_pseudo'] = $user->getPseudo();
        
        // 📚 Redirection vers l'accueil
        header('Location: index.php');
        exit;
        
    } else {
        // ❌ Échec de connexion
        $erreur = 'Email ou mot de passe incorrect';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - QuizMusic 🎵</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body
    class="bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">🎵 QuizMusic</h1>
            <p class="text-gray-600">Connectez-vous pour jouer</p>
        </div>

        <?php if ($erreur): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4">
            <?php echo htmlspecialchars($erreur); ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" name="email" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">Mot de passe</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-bold py-3 rounded-xl transition-all duration-200">
                Se connecter
            </button>
        </form>

        <p class="text-center text-gray-600 mt-6">
            Pas encore de compte ?
            <a href="register.php" class="text-purple-600 hover:text-purple-700 font-medium">Inscrivez-vous</a>
        </p>
    </div>
</body>

</html>