<?php

namespace App\Models;

use PDO;

class Page
{
    private $conn;
    private $table_name = "pages";

    private $id;
    private $subcategory_id;
    private $content;
    private $video_url;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setSubCategoryId($subcategory_id)
    {
        $this->subcategory_id = $subcategory_id;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setVideoUrl($video_url)
    {
        $this->video_url = $video_url;
    }

    public function getPagesBySubCategory($subcategory_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE subcategory_id = :subcategory_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':subcategory_id', $subcategory_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPageById($id) {
        $query = "SELECT id, title, content, subcategory_id, video_url 
                  FROM pages 
                  WHERE id = :id";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    public function createPage($title, $content, $video_url, $subcategory_id) {
        error_log("Données pour createPage: Title = $title, Content = $content, Video_URL = $video_url, SubCategory_ID = $subcategory_id");
    
        // Préparer la requête
        $query = "INSERT INTO pages (title, content, video_url, subcategory_id) VALUES (:title, :content, :video_url, :subcategory_id)";
        $stmt = $this->conn->prepare($query);
    
        // Liaison des paramètres
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':content', $content);
        $stmt->bindValue(':video_url', $video_url);
        $stmt->bindValue(':subcategory_id', $subcategory_id, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            error_log("Insertion réussie dans la table 'pages'");
            return true;
        } else {
            error_log("Erreur d'insertion dans la table 'pages': " . implode(", ", $stmt->errorInfo()));
            return false;
        }
    }
    
    

    public function updatePage($id, $title, $content, $video_url, $subcategory_id) {
        $query = "UPDATE " . $this->table_name . " SET title = :title, content = :content, video_url = :video_url, subcategory_id = :subcategory_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":content", $content);
        $stmt->bindParam(":video_url", $video_url);
        $stmt->bindParam(":subcategory_id", $subcategory_id);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function deletePage()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
}
