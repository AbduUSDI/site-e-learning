<?php
namespace App\Models;

use PDO;

class Thread
{
    private $conn;
    private $table_name = "forum_posts";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Crée un nouveau post (thread ou reply)
    public function createPost($user_id, $title, $body, $post_type = 'thread', $parent_id = null)
    {
        // Requête d'insertion
        $query = "INSERT INTO " . $this->table_name . " 
                (user_id, title, body, post_type, parent_id, created_at, updated_at) 
                VALUES (:user_id, :title, :body, :post_type, :parent_id, NOW(), NOW())";

        $stmt = $this->conn->prepare($query);

        // Bind des paramètres
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':body', $body, PDO::PARAM_STR);
        $stmt->bindParam(':post_type', $post_type, PDO::PARAM_STR);
        $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);

        // Exécution de la requête
        return $stmt->execute();
    }
    public function createResponse($threadId, $userId, $body)
    {
        // Le type de post est fixé à 'reply'
        $post_type = 'reply';

        // Requête d'insertion
        $query = "INSERT INTO " . $this->table_name . " (user_id, title, body, post_type, parent_id, created_at) 
                VALUES (:user_id, NULL, :body, :post_type, :parent_id, NOW())";
        
        $stmt = $this->conn->prepare($query);

        // Bind des paramètres
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':body', $body, PDO::PARAM_STR);
        $stmt->bindParam(':post_type', $post_type, PDO::PARAM_STR);
        $stmt->bindParam(':parent_id', $threadId, PDO::PARAM_INT);

        // Exécution de la requête
        return $stmt->execute();
    }

    // Récupère tous les threads
    public function getThreads()
{
    $query = "SELECT * FROM " . $this->table_name . " WHERE post_type = 'thread' ORDER BY created_at DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // Récupère un thread par son ID
    public function getThreadById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupère les réponses associées à un thread
    public function getResponses($thread_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE parent_id = :parent_id AND post_type = 'reply' ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':parent_id', $thread_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getResponsesByThreadId($threadId)
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE parent_id = :threadId AND post_type = 'reply' 
                ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':threadId', $threadId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Met à jour un post existant (titre et contenu)
    public function updatePost($id, $title, $body)
    {
        $query = "UPDATE " . $this->table_name . " SET title = :title, body = :body, updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Bind des paramètres
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':body', $body, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Supprime un post par son ID
    public function deletePost($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Récupère tous les threads créés par un utilisateur donné
    public function getThreadsByUserId($user_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id AND post_type = 'thread' ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
