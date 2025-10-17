<?php
require_once 'Database.php';

class Badge {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnexion();
    }

    // Récupérer tous les badges
    public function getAllBadges() {
        $stmt = $this->pdo->query("SELECT * FROM badges");
        return $stmt->fetchAll();
    }

    // Récupérer les badges d'un utilisateur
    public function getUserBadges($userId) {
        $stmt = $this->pdo->prepare("
            SELECT b.*
            FROM badges b
            JOIN user_badges ub ON b.id = ub.badge_id
            WHERE ub.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Attribuer un badge à un utilisateur
    public function attribuerBadge($userId, $badgeCode) {
        // Vérifier si badge déjà obtenu
        $stmt = $this->pdo->prepare("
            SELECT id FROM badges WHERE code = ?
        ");
        $stmt->execute([$badgeCode]);
        $badge = $stmt->fetch();

        if (!$badge) return false;

        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (?, ?)
        ");
        return $stmt->execute([$userId, $badge['id']]);
    }
}
