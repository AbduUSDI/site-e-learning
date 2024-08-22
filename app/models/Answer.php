<?php

namespace App\Models;

use PDO;

class Answer
{
    private $conn;
    private $table_name = "answers";

    private $id;
    private $question_id;
    private $answer_text;
    private $is_correct;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setQuestionId($question_id)
    {
        $this->question_id = $question_id;
    }

    public function setAnswerText($answer_text)
    {
        $this->answer_text = $answer_text;
    }

    public function setIsCorrect($is_correct)
    {
        $this->is_correct = $is_correct;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getQuestionId()
    {
        return $this->question_id;
    }

    public function getAnswerText()
    {
        return $this->answer_text;
    }

    public function getIsCorrect()
    {
        return $this->is_correct;
    }
    public function getLastInsertId()
    {
        return $this->conn->lastInsertId();
    }

    // CRUD methods
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (question_id, answer_text, is_correct) VALUES (:question_id, :answer_text, :is_correct)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':question_id', $this->question_id);
        $stmt->bindParam(':answer_text', $this->answer_text);
        $stmt->bindParam(':is_correct', $this->is_correct);

        return $stmt->execute();
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " SET answer_text = :answer_text, is_correct = :is_correct WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':answer_text', $this->answer_text);
        $stmt->bindParam(':is_correct', $this->is_correct);

        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function getByQuestion($question_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE question_id = :question_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':question_id', $question_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function deleteMissingAnswers($question_id, $existingAnswerIds)
    {
        // Vérifie si des réponses existent pour la question
        if (empty($existingAnswerIds)) {
            $query = "DELETE FROM " . $this->table_name . " WHERE question_id = :question_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':question_id', $question_id);
        } else {
            // Supprime les réponses qui ne sont pas dans $existingAnswerIds
            $placeholders = implode(',', array_fill(0, count($existingAnswerIds), '?'));
            $query = "DELETE FROM " . $this->table_name . " WHERE question_id = ? AND id NOT IN ($placeholders)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $question_id);
            foreach ($existingAnswerIds as $k => $id) {
                $stmt->bindValue($k + 2, $id); // $k+2 car l'index 1 est occupé par question_id
            }
    }

    return $stmt->execute();
    }
    public function createAnswer($questionId, $answerText, $isCorrect)
        {
            $query = "INSERT INTO answers (question_id, answer_text, is_correct) VALUES (:question_id, :answer_text, :is_correct)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':question_id', $questionId);
            $stmt->bindParam(':answer_text', $answerText);
            $stmt->bindParam(':is_correct', $isCorrect);

            return $stmt->execute();
        }

        public function getAnswersByQuestionId($question_id)
        {
            $query = "SELECT * FROM " . $this->table_name . " WHERE question_id = :question_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':question_id', $question_id);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

}
