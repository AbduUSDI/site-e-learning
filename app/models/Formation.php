<?php
namespace App\Models;

use PDO;

class Formation
{
    private $conn;
    private $table_name = "formations";

    private $id;
    private $name;
    private $description;
    private $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    // CRUD Methods
    public function getAllFormations() : array
{
    $query = "SELECT * FROM formations";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    $formations = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $formation = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'categories' => $this->getCategoriesByFormationId($row['id']) // Ajouter les catégories ici
        ];
        $formations[] = $formation;
    }

    return $formations;
}

public function getFormationsByUser($userId)
{
    $query = "
        SELECT f.id, f.name, f.description 
        FROM formations f
        INNER JOIN user_formations uf ON f.id = uf.formation_id
        WHERE uf.user_id = :user_id
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Doit retourner un tableau
}

private function getCategoriesByFormationId($formationId)
{
    $query = "SELECT * FROM categories WHERE formation_id = :formation_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':formation_id', $formationId);
    $stmt->execute();

    $categories = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $category = [
            'id' => $row['id'],
            'title' => $row['title'],
            'subcategories' => $this->getSubCategoriesByCategoryId($row['id']) // Ajouter les sous-catégories ici
        ];
        $categories[] = $category;
    }

    return $categories;
}

private function getSubCategoriesByCategoryId($categoryId)
{
    $query = "SELECT * FROM subcategories WHERE category_id = :category_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':category_id', $categoryId);
    $stmt->execute();

    $subcategories = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $subcategory = [
            'id' => $row['id'],
            'title' => $row['title'],
            'pages' => $this->getPagesBySubCategoryId($row['id']) // Ajouter les pages ici
        ];
        $subcategories[] = $subcategory;
    }

    return $subcategories;
}

private function getPagesBySubCategoryId($subcategoryId)
{
    $query = "SELECT * FROM pages WHERE subcategory_id = :subcategory_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':subcategory_id', $subcategoryId);
    $stmt->execute();

    $pages = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $page = [
            'id' => $row['id'],
            'title' => $row['title'],
            'content' => $row['content'],
            'video_url' => $row['video_url']
        ];
        $pages[] = $page;
    }

    return $pages;
}
    public function getFormationById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createFormation($name, $description) {
        $query = "INSERT INTO " . $this->table_name . " (name, description) VALUES (:name, :description)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateFormation($id, $name, $description) {
        error_log("Tentative de mise à jour de la formation ID: $id, Name: $name, Description: $description");
        
        $query = "UPDATE formations SET name = :name, description = :description WHERE id = :id";
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            error_log("Mise à jour réussie pour la formation ID: $id");
            return true;
        } else {
            error_log("Erreur lors de la mise à jour de la formation ID: $id, Erreur: " . implode(", ", $stmt->errorInfo()));
            return false;
        }
    }
    

    public function deleteFormation()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
    // Méthode pour assigner une formation à un étudiant
    public function assignFormationToStudent($userId, $formationId)
    {
        // Vérifier si l'utilisateur est déjà inscrit à cette formation
        $query = "SELECT * FROM user_formations WHERE user_id = :user_id AND formation_id = :formation_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':formation_id', $formationId);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return false; // L'utilisateur est déjà inscrit à cette formation
        }

        // Ajouter l'inscription à la formation
        $query = "INSERT INTO user_formations (user_id, formation_id) VALUES (:user_id, :formation_id)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':formation_id', $formationId);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
