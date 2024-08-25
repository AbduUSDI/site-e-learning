<?php

namespace App\Models;

use PDO;
class Exam
{
    private $conn;
    private $table_name = "exams";

    public $id;
    public $title;
    public $description;
    public $file_path;
    public $due_date;
    public $created_at;
    public $updated_at;
    public $formationId;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createExam($title, $description, $filePath, $dueDate, $formationId) {
        $query = "INSERT INTO exams (title, description, file_path, due_date, formation_id) 
                  VALUES (:title, :description, :file_path, :due_date, :formation_id)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':file_path', $filePath);
        $stmt->bindParam(':due_date', $dueDate);
        $stmt->bindParam(':formation_id', $formationId);

        return $stmt->execute();
    }

    public function getExams() {
        $query = "SELECT e.*, f.name as formation_name 
                  FROM exams e 
                  JOIN formations f ON e.formation_id = f.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getFormations() {
        $query = "SELECT * FROM formations";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET title = :title, description = :description, file_path = :file_path, due_date = :due_date
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->file_path = htmlspecialchars(strip_tags($this->file_path));
        $this->due_date = htmlspecialchars(strip_tags($this->due_date));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":file_path", $this->file_path);
        $stmt->bindParam(":due_date", $this->due_date);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function getFormationsByStudent($student_id) {
        $query = "SELECT f.* FROM formations f 
                  JOIN user_formations uf ON f.id = uf.formation_id 
                  WHERE uf.user_id = :student_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();
        
        return $stmt;
    }

    public function getAllExamsByFormation($formationId) {
        $query = "SELECT e.*, f.name as formation_name FROM exams e JOIN formations f ON e.formation_id = f.id WHERE e.formation_id = :formation_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':formation_id', $formationId);
        $stmt->execute();
        return $stmt;
    }
    public function getFeedbackBySubmissionId($submissionId)
    {
        $query = "SELECT * FROM exam_feedback WHERE submission_id = :submission_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':submission_id', $submissionId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getFeedbacksByStudentId($studentId) {
        $query = "SELECT es.id as submission_id, es.exam_id, e.title, ef.message, ef.audio_path
                  FROM exam_submissions es
                  JOIN exams e ON es.exam_id = e.id
                  LEFT JOIN exam_feedback ef ON es.id = ef.submission_id
                  WHERE es.student_id = :student_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function submitFeedback($submissionId, $message, $audioPath = null) {
        $query = "INSERT INTO exam_feedback (submission_id, message, audio_path) VALUES (:submission_id, :message, :audio_path)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':submission_id', $submissionId);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':audio_path', $audioPath);
        return $stmt->execute();
    }
    
}
