<?php

namespace App\Models;

use PDO;

class SubCategory
{
    private $conn;
    private $table_name = "subcategories";

    private $id;
    private $category_id;
    private $title;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getSubCategoriesByCategory($category_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE category_id = :category_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubCategoryById($id) {
        $query = "SELECT * FROM subcategories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    public function createSubCategory($title, $category_id) {
        $query = "INSERT INTO " . $this->table_name . " (title, category_id) VALUES (:title, :category_id)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":category_id", $category_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateSubCategory($id, $title, $category_id) {
        $query = "UPDATE " . $this->table_name . " SET title = :title, category_id = :category_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":category_id", $category_id);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function deleteSubCategory()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
}
