<?php

namespace App\Controllers;

use PDO;

class MediatequeController {
    private $conn;
    private $table_name = "courses";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Récupère tous les éléments de la médiathèque
    public function getAllMedia() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère un élément de la médiathèque par son ID
    public function getMediaById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crée un nouvel élément dans la médiathèque
    public function createMedia($title, $content) {
        $query = "INSERT INTO " . $this->table_name . " (title, content) VALUES (:title, :content)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);

        return $stmt->execute();
    }

    // Met à jour un élément de la médiathèque
    public function updateMedia($id, $title, $content) {
        $query = "UPDATE " . $this->table_name . " SET title = :title, content = :content WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // Supprime un élément de la médiathèque
    public function deleteMedia($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}
