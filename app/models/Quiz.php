<?php
namespace App\Models;

use PDO;

class Quiz
{
    private $conn;
    private $table_name = "quizzes";

    private $id;
    private $formation_id;
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

    public function setFormationId($formation_id)
    {
        $this->formation_id = $formation_id;
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

    public function getFormationId()
    {
        return $this->formation_id;
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
    public function createQuiz($quiz_name, $description, $formation_id = null)
    {
        $query = "INSERT INTO " . $this->table_name . " (quiz_name, description, formation_id) VALUES (:quiz_name, :description, :formation_id)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':quiz_name', $quiz_name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':formation_id', $formation_id);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    public function updateQuiz($id, $quiz_name, $description, $formation_id = null)
    {
        $query = "UPDATE " . $this->table_name . " SET quiz_name = :quiz_name, description = :description, formation_id = :formation_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':quiz_name', $quiz_name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':formation_id', $formation_id);
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
    public function saveUserScore($quizId, $userId, $score)
{
    // Vérifiez si un enregistrement existe déjà pour cet utilisateur et ce quiz
    $existingResult = $this->getUserQuizResult($userId, $quizId);

    if ($existingResult) {
        // Si un résultat existe, mettez à jour le score
        $query = "UPDATE quiz_results SET score = score + :score, completed_at = NOW() WHERE user_id = :user_id AND quiz_id = :quiz_id";
    } else {
        // Sinon, insérez un nouveau résultat
        $query = "INSERT INTO quiz_results (quiz_id, user_id, score, completed_at) VALUES (:quiz_id, :user_id, :score, NOW())";
    }

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':quiz_id', $quizId);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':score', $score);

    return $stmt->execute();
}

    
    public function getUserQuizResult($quizId, $userId) {
        $query = "SELECT * FROM quiz_results WHERE quiz_id = :quiz_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quiz_id', $quizId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getScoresByUser($userId) {
        $query = "SELECT q.quiz_name, qr.score FROM quiz_results qr JOIN quizzes q ON qr.quiz_id = q.id WHERE qr.user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPreviousResults($userId) {
        $query = "SELECT * FROM quiz_results WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[$row['quiz_id']] = $row;
        }
    
        return $results;
    }
    
}
