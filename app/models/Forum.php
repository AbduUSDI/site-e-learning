<?php

namespace App\Models;

use PDO;

class Forum {
    private $conn;
    private $table = 'threads';

    private $id;
    private $title;
    private $body;
    private $user_id;
    private $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Getters and Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getBody() {
        return $this->body;
    }

    public function setBody($body) {
        $this->body = $body;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    // Méthode pour ajouter un thread dans la base de données
    public function addThread() {
        $query = "INSERT INTO " . $this->table . " (title, body, user_id, created_at) VALUES (:title, :body, :user_id, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $this->getTitle());
        $stmt->bindParam(':body', $this->getBody());
        $stmt->bindParam(':user_id', $this->getUserId());
        
        return $stmt->execute();
    }

    // Méthode pour récupérer tous les threads
    public function getThreads() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour récupérer un thread par ID
    public function getThreadById($id) {
        $this->setId($id);

        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->getId());
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Méthode pour supprimer un thread
    public function deleteThread($id) {
        $this->setId($id);

        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->getId());
        return $stmt->execute();
    }
}
