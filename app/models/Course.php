<?php
namespace App\Models;

use PDO;

class Course
{
    private $conn;
    private $table_name = 'courses';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function assignToStudent($studentId, $courseName)
    {
        $query = "INSERT INTO " . $this->table_name . " (student_id, course_name, assigned_at) VALUES (:student_id, :course_name, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->bindParam(':course_name', $courseName);

        return $stmt->execute();
    }
}
