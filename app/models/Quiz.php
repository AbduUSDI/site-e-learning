<?php
namespace App\Models;

use PDO;

class Quiz
{
    private $conn;
    private $table_name = "quizzes";

    private $id;
    private $course_id;
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


    // CRUD Methods
    public function getAllQuizzes()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuizById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createQuiz()
    {
        $query = "INSERT INTO " . $this->table_name . " (quiz_name, description, course_id) VALUES (:quiz_name, :description, :course_id)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':quiz_name', $this->quiz_name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':course_id', $this->course_id);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    public function updateQuiz()
    {
        $query = "UPDATE " . $this->table_name . " SET quiz_name = :quiz_name, description = :description WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quiz_name', $this->quiz_name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function deleteQuiz()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
    public function deleteByQuizId($quiz_id)
{
    $query = "DELETE FROM " . $this->table_name . " WHERE quiz_id = :quiz_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':quiz_id', $quiz_id);
    $stmt->execute();
}

    public function getAllCourses()
    {
        $query = "SELECT * FROM courses";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
