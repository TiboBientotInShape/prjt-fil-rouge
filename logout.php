<?php
/**
 * QuizMusic - Déconnexion
 * Jour 4 : Destruction de la session et redirection
 * 
 * 📚 CONCEPT : DÉCONNEXION SÉCURISÉE
 * Il ne suffit pas de faire unset($_SESSION) !
 * Il faut suivre les bonnes pratiques de sécurité
 */

// 📚 ÉTAPE 1 : Démarrer la session
// Obligatoire pour pouvoir la détruire
session_start();

// 📚 ÉTAPE 2 : Supprimer toutes les variables de session
// $_SESSION = [] vide le tableau mais ne détruit pas la session
$_SESSION = [];

// 📚 ÉTAPE 3 : Détruire le cookie de session
// Si on utilise des cookies pour la session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 📚 ÉTAPE 4 : Détruire la session côté serveur
session_destroy();

// 📚 ÉTAPE 5 : Rediriger vers la page de connexion
header('Location: login.php');
exit;