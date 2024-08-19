<?php
namespace App\Models;

use PDO;

class FriendRequest
{
    private $conn;
    private $tableRequests = 'friend_requests';
    private $tableFriends = 'friends';

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
        $query = "SELECT * FROM " . $this->tableFriends . " WHERE user_id = :user_id OR friend_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingRequests($userId)
    {
        $query = "SELECT * FROM " . $this->tableRequests . " WHERE receiver_id = :user_id AND status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getFriendRequests($user_id)
{
    $query = "SELECT * FROM " . $this->tableRequests . " WHERE receiver_id = :user_id AND status = 'pending'";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
