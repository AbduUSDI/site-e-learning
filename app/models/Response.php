<?php

namespace App\Models;

use PDO;

class Response
{
    private $conn;
    private $table_name = "forum_posts";  // Utilisation de la même table que les threads

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Créer une réponse à un thread
    public function createResponse($user_id, $body, $thread_id)
    {
        $query = "INSERT INTO " . $this->table_name . " (user_id, body, post_type, parent_id, created_at) VALUES (:user_id, :body, 'reply', :parent_id, NOW())";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':body', $body, PDO::PARAM_STR);
        $stmt->bindParam(':parent_id', $thread_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Obtenir les réponses d'un utilisateur spécifique
    public function getResponsesByUserId($user_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id AND post_type = 'reply' ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mettre à jour une réponse
    public function updateResponse($id, $body)
    {
        $query = "UPDATE " . $this->table_name . " SET body = :body, updated_at = NOW() WHERE id = :id AND post_type = 'reply'";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':body', $body, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Supprimer une réponse
    public function deleteResponse($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND post_type = 'reply'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
