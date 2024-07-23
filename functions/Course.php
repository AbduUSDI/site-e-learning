<?php
class Course {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function getDernierCoursConsulté($userId) {
        $query = "SELECT * FROM courses WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}