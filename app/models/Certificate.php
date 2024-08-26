<?php

namespace App\Models;

use PDO;

class Certificate
{
    private $conn;
    private $table = 'certificates';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getCertificateById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCertificatesByUserId($userId)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createCertificate($data)
    {
        $query = "INSERT INTO " . $this->table . " (user_id, formation_id, certificate_number, issue_date, valid_until) VALUES (:user_id, :formation_id, :certificate_number, :issue_date, :valid_until)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':formation_id', $data['formation_id'], PDO::PARAM_INT);
        $stmt->bindParam(':certificate_number', $data['certificate_number']);
        $stmt->bindParam(':issue_date', $data['issue_date']);
        $stmt->bindParam(':valid_until', $data['valid_until']);
        return $stmt->execute();
    }
}
