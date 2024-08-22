<?php
namespace App\Models;

use PDO;

class Test
{
    private $conn;
    private $table_name = "tests";

    private $id;
    private $chapter_id;
    private $order;
    private $question;
    private $answer;
    private $is_correct;
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

    public function setChapterId($chapter_id)
    {
        $this->chapter_id = $chapter_id;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function setQuestion($question)
    {
        $this->question = $question;
    }

    public function setAnswer($answer)
    {
        $this->answer = $answer;
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

    public function getChapterId()
    {
        return $this->chapter_id;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function getAnswer()
    {
        return $this->answer;
    }

    public function getIsCorrect()
    {
        return $this->is_correct;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    // CRUD Methods
    public function getAllTests()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTestById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createTest()
    {
        $query = "INSERT INTO " . $this->table_name . " (chapter_id, `order`, question, answer, is_correct) 
                  VALUES (:chapter_id, :order, :question, :answer, :is_correct)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':chapter_id', $this->chapter_id);
        $stmt->bindParam(':order', $this->order);
        $stmt->bindParam(':question', $this->question);
        $stmt->bindParam(':answer', $this->answer);
        $stmt->bindParam(':is_correct', $this->is_correct);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function updateTest()
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET question = :question, answer = :answer, is_correct = :is_correct, `order` = :order 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':question', $this->question);
        $stmt->bindParam(':answer', $this->answer);
        $stmt->bindParam(':is_correct', $this->is_correct);
        $stmt->bindParam(':order', $this->order);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function deleteTest()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
