<?php
namespace App\Models;

use PDO;

class Schedule
{
    private $conn;
    private $table_name = "schedules";

    private $id;
    private $teacher_id;
    private $schedule;
    private $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function setTeacherId($teacher_id)
    {
        $this->teacher_id = $teacher_id;
    }

    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
    }

    public function save()
    {
        $query = "INSERT INTO " . $this->table_name . " (teacher_id, schedule, assigned_at) VALUES (:teacher_id, :schedule, NOW())";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':teacher_id', $this->teacher_id, PDO::PARAM_INT);
        $stmt->bindParam(':schedule', $this->schedule, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function getScheduleByTeacherId($teacher_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE teacher_id = :teacher_id ORDER BY assigned_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
