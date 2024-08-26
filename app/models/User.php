<?php

namespace App\Models;

use PDO;

class User {
    private $conn;
    private $table_name = "users";
    private $id;
    private $username;
    private $email;
    private $password;
    private $created_at;
    private $role_id;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    // Getters and Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->username;
    }

    public function setName($username) {
        $this->username = $username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }
    public function getRoleId() {
        return $this->role_id;
    }

    public function setRoleId($role_id) {
        $this->role_id = $role_id;
    }
    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }
    public function findById($id) {
        $query = 'SELECT * FROM users WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Méthode pour récupérer tous les utilisateurs
    public function getAllUsers() {
        $stmt = $this->conn->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour récupérer un utilisateur par email
    public function getUserByEmail($email) {
        $this->setEmail($email);

        $query = 'SELECT * FROM users WHERE email = :email';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email); // Changement ici
        $email = $this->getEmail(); // Ajout de cette ligne
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getUserByUsername($username)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Méthode pour ajouter un utilisateur dans la base de données
    public function addUser() {
        $query = "INSERT INTO users (username, email, password, role_id, created_at) 
                  VALUES (:username, :email, :password, :role_id, NOW())";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':role_id', $this->role_id);

        return $stmt->execute();
    }

    // Méthode pour mettre à jour un utilisateur
    public function updateUser()
    {
        // Si le mot de passe est défini, on le met à jour, sinon on le laisse tel quel
        $query = "UPDATE " . $this->table_name . " SET username = :username, email = :email, role_id = :role_id";
        
        if (!empty($this->password)) {
            $query .= ", password = :password";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        // Liaison des paramètres
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':role_id', $this->role_id);
        $stmt->bindParam(':id', $this->id);

        if (!empty($this->password)) {
            $stmt->bindParam(':password', $this->password);
        }

        return $stmt->execute();
    }
    public function updateUserProfile($userId, $username, $email, $newPassword = null)
{
    if (!empty($newPassword)) {
        $query = "UPDATE " . $this->table_name . " SET username = :username, email = :email, password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $hashedPassword);
    } else {
        $query = "UPDATE " . $this->table_name . " SET username = :username, email = :email WHERE id = :id";
        $stmt = $this->conn->prepare($query);
    }
    
    $stmt->bindParam(':id', $userId);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    
    return $stmt->execute();
}



    // Méthode pour supprimer un utilisateur
    public function deleteUser($id) {
        $this->setId($id);

        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $id = $this->getId();
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
    // Méthode pour obtenir les utilisateurs par rôle
    public function getUsersByRole($role_id) {
        $query = "SELECT * FROM users WHERE role_id = :role_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function validateCursus($studentId, $validationStatus)
    {
        $query = "UPDATE " . $this->table_name . " SET cursus_valide = :validation_status WHERE id = :student_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':validation_status', $validationStatus);
        $stmt->bindParam(':student_id', $studentId);

        return $stmt->execute();
    }
    public function getCursusValidationStats() {
        $query = "SELECT COUNT(*) as total, 
                  SUM(cursus_valide = 1) as cursus_valide, 
                  SUM(cursus_valide = 0) as cursus_non_valide 
                  FROM users 
                  WHERE role_id = 3"; // Assurez-vous que le rôle 3 correspond bien aux étudiants
    
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function updateCertificateIssued($userId)
    {
        $query = "UPDATE " . $this->table_name . " SET certificate_issued = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }
}