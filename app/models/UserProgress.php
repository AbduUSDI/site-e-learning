<?php
namespace App\Models;

use PDO;

class UserProgress
{
    private $conn;
    private $table_name = "user_progress";

    private $id;
    private $user_id;
    private $chapter_id;
    private $course_id;
    private $formation_id;
    private $progress;
    private $quiz_score;
    private $test_score;
    private $completed;
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

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function setChapterId($chapter_id)
    {
        $this->chapter_id = $chapter_id;
    }

    public function setCourseId($course_id)
    {
        $this->course_id = $course_id;
    }

    public function setFormationId($formation_id)
    {
        $this->formation_id = $formation_id;
    }

    public function setProgress($progress)
    {
        $this->progress = $progress;
    }

    public function setQuizScore($quiz_score)
    {
        $this->quiz_score = $quiz_score;
    }

    public function setTestScore($test_score)
    {
        $this->test_score = $test_score;
    }

    public function setCompleted($completed)
    {
        $this->completed = $completed;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getChapterId()
    {
        return $this->chapter_id;
    }

    public function getCourseId()
    {
        return $this->course_id;
    }

    public function getFormationId()
    {
        return $this->formation_id;
    }

    public function getProgress()
    {
        return $this->progress;
    }

    public function getQuizScore()
    {
        return $this->quiz_score;
    }

    public function getTestScore()
    {
        return $this->test_score;
    }

    public function getCompleted()
    {
        return $this->completed;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    // CRUD Methods
    public function getUserProgressByUserId($user_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUserProgress()
    {
        $query = "INSERT INTO " . $this->table_name . " (user_id, chapter_id, course_id, formation_id, progress, quiz_score, test_score, completed) 
                  VALUES (:user_id, :chapter_id, :course_id, :formation_id, :progress, :quiz_score, :test_score, :completed)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':chapter_id', $this->chapter_id);
        $stmt->bindParam(':course_id', $this->course_id);
        $stmt->bindParam(':formation_id', $this->formation_id);
        $stmt->bindParam(':progress', $this->progress);
        $stmt->bindParam(':quiz_score', $this->quiz_score);
        $stmt->bindParam(':test_score', $this->test_score);
        $stmt->bindParam(':completed', $this->completed);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function updateUserProgress()
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET progress = :progress, quiz_score = :quiz_score, test_score = :test_score, completed = :completed 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':progress', $this->progress);
        $stmt->bindParam(':quiz_score', $this->quiz_score);
        $stmt->bindParam(':test_score', $this->test_score);
        $stmt->bindParam(':completed', $this->completed);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function deleteUserProgress()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
