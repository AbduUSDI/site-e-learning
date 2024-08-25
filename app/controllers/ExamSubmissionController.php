<?php

namespace App\Controllers;

use App\Models\ExamSubmission;
use PDO;

class ExamSubmissionController {
    private $db;
    private $examSubmissionModel;

    public function __construct($db) {
        $this->db = $db;
        $this->examSubmissionModel = new ExamSubmission($db);
    }

    public function submitExam($examId, $studentId, $filePath) {
        return $this->examSubmissionModel->submitExam($examId, $studentId, $filePath);
    }
    public function getSubmissionsByStudent($studentId) {
        return $this->examSubmissionModel->getSubmissionsByStudent($studentId);
    }
    public function getSubmissionsByExam($examId) {
        return $this->examSubmissionModel->getSubmissionsByExam($examId);
    }
    public function deleteSubmission($submissionId) {
        return $this->examSubmissionModel->deleteSubmission($submissionId);
    }
    public function getSubmissionsCountByStudent($studentId) {
        return $this->examSubmissionModel->getSubmissionsCountByStudent($studentId);
    }
}