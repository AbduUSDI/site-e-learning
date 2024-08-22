<?php
namespace App\Models;

use PDO;

class Quiz
{
    private $conn;
    private $table_name = "quizzes";

    private $id;
    private $course_id;
    private $chapter_id;
    private $quiz_name;
    private $description;
    private $created_at;

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

    public function setChapterId($chapter_id)
    {
        $this->chapter_id = $chapter_id;
    }

    public function setQuizName($quiz_name)
    {
        $this->quiz_name = $quiz_name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
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

    public function getChapterId()
    {
        return $this->chapter_id;
    }

    public function getQuizName()
    {
        return $this->quiz_name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }
    public function getLastInsertId()
    {
        return $this->conn->lastInsertId();
    }
    public function createQuiz($quiz_name, $description, $course_id = null)
    {
        $query = "INSERT INTO " . $this->table_name . " (quiz_name, description, course_id) VALUES (:quiz_name, :description, :course_id)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':quiz_name', $quiz_name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':course_id', $course_id);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    public function updateQuiz($id, $quiz_name, $description, $course_id = null)
    {
        $query = "UPDATE " . $this->table_name . " SET quiz_name = :quiz_name, description = :description, course_id = :course_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':quiz_name', $quiz_name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function deleteQuiz($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function getAllQuizzes()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuizById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
