<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllUtilisateurs() {
        $stmt = $this->conn->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUtilisateurParEmail($email) {
        $query = 'SELECT * FROM users WHERE email = :email';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getEmail($email) {
        $query = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    public function addUser($email, $password, $role_id, $username) {
        // Hacher le mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        $stmt = $this->conn->prepare("INSERT INTO users (email, password, role_id, username) VALUES (:email, :password, :role_id, :username)");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
    public function updateUser($id, $email, $role_id, $username, $password = null) {
        $sql = "UPDATE users SET email = :email, role_id = :role_id, username = :username";
        $params = [
            ':id' => $id,
            ':email' => $email,
            ':role_id' => $role_id,
            ':username' => $username
        ];
    
        if (!empty($password)) {
            $sql .= ", password = :password";
            $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        $sql .= " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
    }
    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function updatePassword($userId, $hashedPassword) {
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $userId]);
    }
    function getUsernames($db, $userIds) {
        $placeholders = implode(',', array_fill(0, count($userIds), '?'));
        $query = "SELECT id, username FROM users WHERE id IN ($placeholders)";
        $stmt = $db->prepare($query);
        $stmt->execute($userIds);
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return $results;
    }
}

class User2 {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function updateUserProfile($userId, $username, $email, $newPassword = null) {
        if ($newPassword !== null) {
            $query = "UPDATE " . $this->table . " SET username = :username, email = :email, password = :password WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $hashedPassword);
        } else {
            $query = "UPDATE " . $this->table . " SET username = :username, email = :email WHERE id = :id";
            $stmt = $this->conn->prepare($query);
        }
        $stmt->bindParam(':id', $userId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }
    public function sendFriendRequest($sender_id, $receiver_id) {
        $query = "INSERT INTO friend_requests (sender_id, receiver_id) VALUES (:sender_id, :receiver_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sender_id', $sender_id);
        $stmt->bindParam(':receiver_id', $receiver_id);
        return $stmt->execute();
    }

    public function respondFriendRequest($request_id, $status) {
        $query = "UPDATE friend_requests SET status = :status WHERE id = :request_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':request_id', $request_id);
        return $stmt->execute();
    }

    public function getFriendRequests($user_id) {
        $query = "SELECT * FROM friend_requests WHERE receiver_id = :user_id AND status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFriends($user_id) {
        $query = "SELECT u.id, u.username, fr.id as request_id
                  FROM friend_requests fr
                  JOIN users u ON (fr.sender_id = u.id OR fr.receiver_id = u.id)
                  WHERE (fr.sender_id = :user_id OR fr.receiver_id = :user_id)
                  AND fr.status = 'accepted'
                  AND u.id != :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUserByUsername($username) {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function removeFriend($request_id) {
        $query = "DELETE FROM friend_requests WHERE id = :request_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':request_id', $request_id);
        return $stmt->execute();
    }
}