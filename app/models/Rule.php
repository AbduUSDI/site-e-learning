<?php

namespace App\Models;

use PDO;

class Rule
{
    private $conn;
    private $table_name = "rules";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Créer une nouvelle règle
    public function create($title, $description)
    {
        $query = "INSERT INTO " . $this->table_name . " (title, description) VALUES (:title, :description)";
        $stmt = $this->conn->prepare($query);

        // Assainir les données
        $title = htmlspecialchars(strip_tags($title));
        $description = htmlspecialchars(strip_tags($description));

        // Liaison des données
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Lire toutes les règles
    public function read()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Mettre à jour une règle existante
    public function update($id, $title, $description)
    {
        $query = "UPDATE " . $this->table_name . " SET title = :title, description = :description WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Assainir les données
        $title = htmlspecialchars(strip_tags($title));
        $description = htmlspecialchars(strip_tags($description));
        $id = htmlspecialchars(strip_tags($id));

        // Liaison des données
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Supprimer une règle
    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Assainir les données
        $id = htmlspecialchars(strip_tags($id));

        // Liaison des données
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}