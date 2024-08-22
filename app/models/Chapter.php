<?php

namespace App\Models;

use PDO;

class Chapter
{
    private $conn;
    private $table_name = "chapters";

    private $id;
    private $course_id;
    private $chapter_name;
    private $content;
    private $created_at;
    private $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCourseId($course_id)
    {
        $this->course_id = $course_id;
    }

    public function setChapterName($chapter_name)
    {
        $this->chapter_name = $chapter_name;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getCourseId()
    {
        return $this->course_id;
    }

    public function getChapterName()
    {
        return $this->chapter_name;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
    public function getLastInsertId()
    {
        return $this->conn->lastInsertId();
    }

    // CRUD Methods
    public function getAllChapters()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChapterById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getChaptersByCourseId($course_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE course_id = :course_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createChapter()
    {
        $query = "INSERT INTO " . $this->table_name . " (course_id, chapter_name, content) VALUES (:course_id, :chapter_name, :content)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':course_id', $this->course_id);
        $stmt->bindParam(':chapter_name', $this->chapter_name);
        $stmt->bindParam(':content', $this->content);
        
        return $stmt->execute();
    }

    public function updateChapter()
    {
        $query = "UPDATE " . $this->table_name . " SET chapter_name = :chapter_name, content = :content WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':chapter_name', $this->chapter_name);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function deleteChapter()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
}
