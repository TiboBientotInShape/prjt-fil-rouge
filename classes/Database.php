<?php
/**
 * Classe Database - Gestion de la connexion PDO
 *
 * 📚 CONCEPT : PATTERN SINGLETON
 * Le pattern Singleton garantit qu'il n'existe qu'UNE SEULE instance
 * de connexion à la base de données dans toute l'application
 *
 * POURQUOI ?
 * - Éviter d'ouvrir plusieurs connexions inutiles (économie de ressources)
 * - Centraliser la configuration de la BDD en un seul endroit
 * - Faciliter la maintenance (un seul point de modification)
 *
 * COMMENT ÇA MARCHE ?
 * 1. La propriété statique $connexion stocke l'unique instance PDO
 * 2. La première fois qu'on appelle getConnexion(), on crée la connexion
 * 3. Les fois suivantes, on retourne la connexion déjà créée
 */

class Database {
    // 📚 CONCEPT : Propriété STATIC
    // static = partagée par toutes les instances de la classe
    // Il n'y a qu'UNE SEULE variable $connexion pour toute l'application
    // ?PDO signifie "peut contenir un objet PDO ou null"
    private static ?PDO $connexion = null;

    /**
     * Récupère ou crée la connexion PDO (pattern Singleton)
     *
     * 📚 CONCEPT : Méthode STATIC
     * static = on peut l'appeler sans créer d'instance de la classe
     * Database::getConnexion() au lieu de $db->getConnexion()
     *
     * @return PDO L'instance de connexion PDO
     * @throws PDOException Si la connexion échoue
     */
    public static function getConnexion(): PDO {
        // 📚 CONCEPT : self::
        // self fait référence à la classe elle-même (pas à une instance)
        // self::$connexion accède à la propriété statique $connexion

        // Si la connexion n'existe pas encore (première utilisation)
        if (self::$connexion === null) {
            try {
                // 📚 CONCEPT : DSN (Data Source Name)
                // Chaîne de connexion contenant les paramètres de BDD
                // Format : "sgbd:host=serveur;dbname=base;charset=encodage"
                $dsn = "mysql:host=localhost;dbname=quizmusic;charset=utf8mb4";

                // 📚 PARAMÈTRES DE CONNEXION
                // En production, ces valeurs devraient être dans un fichier .env
                $username = "root";      // Utilisateur MySQL (par défaut "root" sur XAMPP)
                $password = "";          // Mot de passe vide par défaut sur XAMPP

                // 📚 CONCEPT : OPTIONS PDO pour la sécurité et le debug
                $options = [
                    // 📚 ERRMODE_EXCEPTION : Lance des exceptions en cas d'erreur
                    // Permet d'attraper les erreurs avec try/catch
                    // Alternative : ERRMODE_SILENT (ne fait rien), ERRMODE_WARNING (affiche un warning)
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                    // 📚 FETCH_ASSOC : Retourne des tableaux associatifs par défaut
                    // $row['nom'] au lieu de $row[0]
                    // Plus lisible et maintenable
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                    // 📚 EMULATE_PREPARES false : Utilise les vraies requêtes préparées
                    // Plus sûr contre les injections SQL
                    // MySQL prépare réellement la requête côté serveur
                    PDO::ATTR_EMULATE_PREPARES => false,

                    // 📚 STRINGIFY_FETCHES false : Conserve les types natifs
                    // Les INT restent des INT, pas des strings
                    PDO::ATTR_STRINGIFY_FETCHES => false
                ];

                // 📚 CONCEPT : Création de l'objet PDO
                // new PDO() établit la connexion à la base de données
                self::$connexion = new PDO($dsn, $username, $password, $options);

            } catch (PDOException $e) {
                // 📚 CONCEPT : Gestion des erreurs de connexion
                // En PRODUCTION, ne jamais afficher le message d'erreur détaillé !
                // Il pourrait révéler des informations sensibles (structure BDD, etc.)

                // VERSION DÉVELOPPEMENT (détaillée) :
                die("❌ Erreur de connexion à la base de données : " . $e->getMessage());

                // VERSION PRODUCTION (générique) :
                // error_log($e->getMessage()); // Log dans un fichier
                // die("Une erreur est survenue. Veuillez réessayer plus tard.");
            }
        }

        // Retourne la connexion (créée maintenant ou déjà existante)
        return self::$connexion;
    }

    /**
     * Ferme explicitement la connexion (optionnel)
     *
     * 📚 NOTE : PHP ferme automatiquement les connexions à la fin du script
     * Cette méthode est utile pour les scripts long-running
     */
    public static function fermerConnexion(): void {
        self::$connexion = null;
    }
}
