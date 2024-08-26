<?php
namespace App\Models;

use PDO;

class FriendRequest
{
    private $conn;
    private $tableRequests = 'friend_requests';
    private $tableFriends = 'friends';
    private $tableUsers = 'users';
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function sendRequest($senderId, $receiverId)
    {
        $query = "INSERT INTO " . $this->tableRequests . " (sender_id, receiver_id) VALUES (:sender_id, :receiver_id)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':sender_id', $senderId);
        $stmt->bindParam(':receiver_id', $receiverId);

        return $stmt->execute();
    }

    public function respondToRequest($requestId, $response)
    {
        $query = "UPDATE " . $this->tableRequests . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':status', $response);
        $stmt->bindParam(':id', $requestId);

        return $stmt->execute();
    }

    public function requestExists($senderId, $receiverId)
    {
        $query = "SELECT COUNT(*) FROM " . $this->tableRequests . " WHERE sender_id = :sender_id AND receiver_id = :receiver_id AND status = 'pending'";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':sender_id', $senderId);
        $stmt->bindParam(':receiver_id', $receiverId);

        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public function getRequestById($requestId)
    {
        $query = "SELECT * FROM " . $this->tableRequests . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $requestId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addFriend($userId, $friendId)
    {
        $query = "INSERT INTO " . $this->tableFriends . " (user_id, friend_id) VALUES (:user_id, :friend_id)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':friend_id', $friendId);

        return $stmt->execute();
    }

    public function getFriends($userId)
    {
        $query = "
            SELECT DISTINCT u.username, u.id as friend_id
            FROM " . $this->tableFriends . " f
            JOIN " . $this->tableUsers . " u ON u.id = CASE 
                WHEN f.user_id = :user_id THEN f.friend_id 
                ELSE f.user_id 
            END
            WHERE (f.user_id = :user_id OR f.friend_id = :user_id)
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function removeFriend($userId, $friendId)
    {
        $query = "
            DELETE FROM " . $this->tableFriends . "
            WHERE (user_id = :user_id AND friend_id = :friend_id)
            OR (user_id = :friend_id AND friend_id = :user_id)
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':friend_id', $friendId);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
    public function getPendingRequests($userId)
    {
        $query = "SELECT * FROM " . $this->tableRequests . " WHERE receiver_id = :user_id AND status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getFriendRequests($userId)
    {
        $query = "
            SELECT fr.id, fr.sender_id, fr.receiver_id, fr.status, u.username AS sender_username
            FROM " . $this->tableRequests . " fr
            JOIN " . $this->tableUsers . " u ON fr.sender_id = u.id
            WHERE fr.receiver_id = :user_id AND fr.status = 'pending'
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Méthode pour obtenir un utilisateur par ID
    public function getUserById($userId)
    {
        $query = "SELECT * FROM " . $this->tableUsers . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}