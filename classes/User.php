<?php
/**
 * Classe User
 * Gère l'authentification et les données utilisateur
 *
 * 📚 RESPONSABILITÉS :
 * - Connexion (login)
 * - Inscription (register)
 * - Récupération de l'historique des scores
 */

class User {
    // 📚 CONCEPT : Propriétés de l'utilisateur
    private ?int $id = null;         // ? signifie "peut être null" (pour les users non enregistrés)
    private string $pseudo;
    private string $email;

    /**
     * Constructeur
     */
    public function __construct(?int $id, string $pseudo, string $email) {
        $this->id = $id;
        $this->pseudo = $pseudo;
        $this->email = $email;
    }

    /**
     * Authentification d'un utilisateur
     *
     * 📚 CONCEPT : Méthode STATIC
     * static = méthode de classe, pas d'instance
     * On peut appeler User::login() sans créer d'objet User
     *
     * POURQUOI STATIC ?
     * Parce qu'on ne connaît pas encore l'utilisateur
     * On cherche à le trouver dans la BDD
     *
     * @param string $email Email saisi
     * @param string $password Mot de passe saisi
     * @return User|null Objet User si succès, null si échec
     */
    public static function login(string $email, string $password): ?User {
        $pdo = Database::getConnexion();

        // 📚 Recherche de l'utilisateur par email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $ligne = $stmt->fetch();

        // 📚 CONCEPT SÉCURITÉ : Vérification du mot de passe hashé
        // password_verify() compare le mot de passe en clair avec le hash
        // JAMAIS de comparaison directe (==) !
        // password_verify() gère automatiquement le sel et l'algorithme

        if ($ligne && password_verify($password, $ligne['password_hash'])) {
            // ✅ Authentification réussie
            // On crée et retourne un objet User
            return new User(
                $ligne['id'],
                $ligne['pseudo'],
                $ligne['email']
            );
        }

        // ❌ Email inexistant ou mot de passe incorrect
        return null;
    }

    /**
     * Inscription d'un nouvel utilisateur
     *
     * 📚 CONCEPT : Création de compte sécurisée
     * - Hash du mot de passe AVANT stockage
     * - Gestion des doublons (pseudo/email déjà utilisé)
     *
     * @param string $pseudo Pseudo choisi
     * @param string $email Email
     * @param string $password Mot de passe en clair
     * @return User|null Objet User si succès, null si doublon
     */
    public static function register(string $pseudo, string $email, string $password): ?User {
        $pdo = Database::getConnexion();

        try {
            // 📚 CONCEPT SÉCURITÉ : Hachage du mot de passe
            // password_hash() utilise un algorithme fort (bcrypt par défaut)
            // CARACTÉRISTIQUES :
            // - Génère automatiquement un "sel" aléatoire
            // - Coûteux en calcul (ralentit les attaques par force brute)
            // - Le hash contient : algorithme + coût + sel + hash
            // - Taille du hash : 60 caractères (on prévoit 255 en BDD)
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // 📚 Insertion dans la BDD
            $sql = "INSERT INTO users (pseudo, email, password_hash) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$pseudo, $email, $passwordHash]);

            // 📚 CONCEPT : Récupération de l'ID auto-incrémenté
            // lastInsertId() retourne l'ID de la dernière ligne insérée
            $id = (int)$pdo->lastInsertId();

            // ✅ Inscription réussie
            return new User($id, $pseudo, $email);

        } catch (PDOException $e) {
            // 📚 CONCEPT : Gestion des contraintes UNIQUE
            // Si le pseudo ou l'email existe déjà, MySQL lève une exception
            // Code d'erreur 23000 = violation de contrainte UNIQUE

            // ❌ Doublon détecté
            return null;
        }
    }

    /**
     * Récupère l'historique des scores de l'utilisateur
     *
     * 📚 CONCEPT : Jointure SQL (JOIN)
     * On lie la table scores avec la table questionnaires
     * pour afficher le titre du thème, pas juste son ID
     *
     * @return array Tableau des scores avec infos des questionnaires
     */
    public function getHistorique(): array {
        $pdo = Database::getConnexion();

        // 📚 CONCEPT : INNER JOIN
        // Relie deux tables sur une colonne commune
        // Ici : scores.questionnaire_id = questionnaires.id
        //
        // Résultat : on a accès aux colonnes des deux tables
        // s.* = toutes les colonnes de scores
        // q.titre, q.emoji = colonnes spécifiques de questionnaires
        $sql = "
            SELECT
                s.*,                          -- Toutes les colonnes de scores
                q.titre as theme_titre,       -- Titre du questionnaire
                q.emoji                       -- Emoji du questionnaire
            FROM scores s
            INNER JOIN questionnaires q ON s.questionnaire_id = q.id
            WHERE s.user_id = ?
            ORDER BY s.date_jeu DESC          -- Plus récent en premier
            LIMIT 20                          -- Seulement les 20 derniers
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$this->id]);

        return $stmt->fetchAll();
    }

    // ====== GETTERS ======

    public function getId(): ?int {
        return $this->id;
    }

    public function getPseudo(): string {
        return $this->pseudo;
    }

    public function getEmail(): string {
        return $this->email;
    }
}
