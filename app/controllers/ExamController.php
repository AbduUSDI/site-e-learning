<?php

namespace App\Controllers;

use App\Models\Exam;

class ExamController
{
    private $conn;
    private $exam;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->exam = new Exam($db);
    }

    public function createExam($title, $description, $file_path, $due_date, $formationId)
    {
        $this->exam->title = $title;
        $this->exam->description = $description;
        $this->exam->file_path = $file_path;
        $this->exam->due_date = $due_date;
        $this->exam->formationId = $formationId;

        if ($this->exam->createExam($title, $description, $file_path, $due_date, $formationId)) {
            return true;
        }
        return false;
    }
    public function getFormations() {
        return $this->exam->getFormations();
    }
    public function getExams()
    {
        return $this->exam->getExams();
    }

    public function getExamById($id)
    {
        $this->exam->id = $id;
        return $this->exam->readOne();
    }

    public function updateExam($id, $title, $description, $file_path, $due_date)
    {
        $this->exam->id = $id;
        $this->exam->title = $title;
        $this->exam->description = $description;
        $this->exam->file_path = $file_path;
        $this->exam->due_date = $due_date;

        if ($this->exam->update()) {
            return true;
        }
        return false;
    }

    public function deleteExam($id)
    {
        $this->exam->id = $id;
        if ($this->exam->delete()) {
            return true;
        }
        return false;
    }
    public function getFormationsByStudent($student_id) {
        return $this->exam->getFormationsByStudent($student_id);
    }
    public function getAllExamsByFormation($formation_id) {
        return $this->exam->getAllExamsByFormation($formation_id);
    }
    public function getFeedbackBySubmissionId($submissionId) {
        return $this->exam->getFeedbackBySubmissionId($submissionId);
    }
    public function submitFeedback($submissionId, $message, $audioPath = null) {
        return $this->exam->submitFeedback($submissionId, $message, $audioPath);
    }
    public function getFeedbacksByStudentId($studentId) {
        return $this->exam->getFeedbacksByStudentId($studentId);
    }
}
