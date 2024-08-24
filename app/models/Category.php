<?php

namespace App\Models;

use PDO;

class Category
{
    private $conn;
    private $table_name = "categories";

    private $id;
    private $formation_id;
    private $title;
    private $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setFormationId($formation_id)
    {
        $this->formation_id = $formation_id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }
    public function getCategoriesByFormation($formation_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE formation_id = :formation_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':formation_id', $formation_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function createCategory($title, $formation_id) {
        $query = "INSERT INTO " . $this->table_name . " (title, formation_id) VALUES (:title, :formation_id)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":formation_id", $formation_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function updateCategory($id, $title, $formation_id) {
        $query = "UPDATE " . $this->table_name . " SET title = :title, formation_id = :formation_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":formation_id", $formation_id);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function deleteCategory($id) {
        $query = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function getCategoryById($id) {
        $query = 'SELECT * FROM categories WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getCategoryProgress($userId, $categoryId) 
    {
        $query = "SELECT status FROM category_progress WHERE user_id = :user_id AND category_id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateCategoryProgress($userId, $categoryId, $status) {

        $query = "SELECT * FROM category_progress WHERE user_id = :user_id AND category_id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {
            // Si l'enregistrement existe, le mettre à jour
            $query = "UPDATE category_progress SET status = :status WHERE user_id = :user_id AND category_id = :category_id";
        } else {
            // Sinon, insérer un nouvel enregistrement
            $query = "INSERT INTO category_progress (user_id, category_id, status) VALUES (:user_id, :category_id, :status)";
        }
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->bindParam(':status', $status);
    
        return $stmt->execute();
    }
    
}