<?php

namespace App\Models;

use PDO;

class ExamSubmission {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function submitExam($examId, $studentId, $filePath) {
        $query = "INSERT INTO exam_submissions (exam_id, student_id, file_path, submitted_at) 
                  VALUES (:exam_id, :student_id, :file_path, NOW())";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':exam_id', $examId);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->bindParam(':file_path', $filePath);

        return $stmt->execute();
    }
    public function getSubmissionsCountByStudent($studentId) {
        $query = "SELECT COUNT(*) as count 
                  FROM exam_submissions 
                  WHERE student_id = :student_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'];
    }
    public function getSubmissionsByExam($examId) {
        $query = "SELECT es.*, u.username 
                  FROM exam_submissions es 
                  JOIN users u ON es.student_id = u.id 
                  WHERE es.exam_id = :exam_id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':exam_id', $examId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function deleteSubmission($submissionId) {
        $query = "DELETE FROM exam_submissions WHERE id = :submission_id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':submission_id', $submissionId);

        return $stmt->execute();
    }
    public function getSubmissionsByStudent($studentId) {
        $query = "SELECT es.*, e.title, e.description 
                  FROM exam_submissions es 
                  JOIN exams e ON es.exam_id = e.id 
                  WHERE es.student_id = :student_id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
