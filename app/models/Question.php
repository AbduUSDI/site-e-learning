<?php

namespace App\Models;

use PDO;

class Question
{
    private $conn;
    private $table_name = "questions";

    private $id;
    private $quiz_id;
    private $question_text;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setQuizId($quiz_id)
    {
        $this->quiz_id = $quiz_id;
    }

    public function setQuestionText($question_text)
    {
        $this->question_text = $question_text;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getQuizId()
    {
        return $this->quiz_id;
    }

    public function getQuestionText()
    {
        return $this->question_text;
    }
    public function getLastInsertId()
    {
        return $this->conn->lastInsertId();
    }
    // CRUD methods
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (quiz_id, question_text) VALUES (:quiz_id, :question_text)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quiz_id', $this->quiz_id);
        $stmt->bindParam(':question_text', $this->question_text);

        return $stmt->execute();
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " SET question_text = :question_text WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':question_text', $this->question_text);

        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function getByQuiz($quiz_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE quiz_id = :quiz_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quiz_id', $quiz_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function deleteMissingQuestions($quizId, $existingQuestionIds)
    {
        $placeholders = implode(',', array_fill(0, count($existingQuestionIds), '?'));
        $query = "DELETE FROM " . $this->table_name . " WHERE quiz_id = ? AND id NOT IN ($placeholders)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(array_merge([$quizId], $existingQuestionIds));
    }
    public function createQuestion($quiz_id, $question_text)
    {
        $query = "INSERT INTO " . $this->table_name . " (quiz_id, question_text) VALUES (:quiz_id, :question_text)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quiz_id', $quiz_id);
        $stmt->bindParam(':question_text', $question_text);

        return $stmt->execute();
    }

    public function deleteQuestionsByQuizId($quiz_id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE quiz_id = :quiz_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quiz_id', $quiz_id);

        return $stmt->execute();
    }
    public function getQuestionsByQuiz($quiz_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE quiz_id = :quiz_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quiz_id', $quiz_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
