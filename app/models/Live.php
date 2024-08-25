<?php

namespace App\Models;

use PDO;

class Live
{
    private $conn;
    private $table_name = "lives";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createLive($title, $description, $date, $link, $utilisateur_id)
    {
        $query = "INSERT INTO " . $this->table_name . " (title, description, date, link, utilisateur_id) 
                VALUES (:title, :description, :date, :link, :utilisateur_id)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':link', $link);
        $stmt->bindParam(':utilisateur_id', $utilisateur_id);

        return $stmt->execute();
    }

    public function updateLive($id, $title, $description, $date, $link, $utilisateur_id)
    {
        $query = "UPDATE " . $this->table_name . " SET title = :title, description = :description, date = :date, link = :link, utilisateur_id = :utilisateur_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':link', $link);
        $stmt->bindParam(':utilisateur_id', $utilisateur_id);

        return $stmt->execute();
    }
    public function deleteLive($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function getAllLives()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllLivesStudent()
{
    $query = "
        SELECT l.id, l.title, l.description, l.date as live_date, l.link, p.prenom, p.nom, p.photo_profil
        FROM lives l
        JOIN profils p ON l.utilisateur_id = p.utilisateur_id
        ORDER BY l.date DESC
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
