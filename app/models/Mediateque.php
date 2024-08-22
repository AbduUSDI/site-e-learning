<?php
namespace App\Models;
use PDO;

class Mediateque extends Model {

    protected $table = 'media';

    public function getAllMedia() {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMediaById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE media_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function uploadMedia($title, $description, $file) {
        // Logic for file upload and saving details in the database
        $filePath = '/public/uploads/' . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $sql = "INSERT INTO {$this->table} (title, description, url, media_type) VALUES (:title, :description, :url, :media_type)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':url', $filePath);
            $stmt->bindParam(':media_type', $file['type']);
            return $stmt->execute();
        }
        return false;
    }
}
