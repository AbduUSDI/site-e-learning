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

    public function createPost($user_id, $body, $post_type, $title = null, $parent_id = null)
    {
        $query = "INSERT INTO " . $this->table_name . " (user_id, title, body, post_type, parent_id, created_at) VALUES (:user_id, :title, :body, :post_type, :parent_id, NOW())";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':body', $body);
        $stmt->bindParam(':post_type', $post_type);
        $stmt->bindParam(':parent_id', $parent_id);

        return $stmt->execute();
    }

    public function getThreads()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE post_type = 'thread' ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResponses($thread_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE parent_id = :parent_id AND post_type = 'reply' ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':parent_id', $thread_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updatePost($id, $title, $body)
    {
        $query = "UPDATE " . $this->table_name . " SET title = :title, body = :body, updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':body', $body);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function deletePost($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
    public function getThreadsByUserId($user_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id AND post_type = 'thread' ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
