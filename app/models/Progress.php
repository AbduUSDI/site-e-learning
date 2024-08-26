<?php
namespace App\Models;

use PDO;

class Progress {
    private $conn;
    private $table_name = "user_progress";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function markPageAsComplete($userId, $formationId, $pageId) {
        $query = "INSERT INTO " . $this->table_name . " (user_id, formation_id, page_id) VALUES (:user_id, :formation_id, :page_id)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':formation_id', $formationId);
        $stmt->bindParam(':page_id', $pageId);

        return $stmt->execute();
    }

    public function getCompletedPagesCount($userId, $formationId) {
        $query = "SELECT COUNT(*) as completed_count FROM " . $this->table_name . " WHERE user_id = :user_id AND formation_id = :formation_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':formation_id', $formationId);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['completed_count'];
    }
}
