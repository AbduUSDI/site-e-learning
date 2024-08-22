<?php

namespace App\Models;

use PDO;
use Exception;

class Course
{
    private $conn;
    private $table = "courses";
    private $studentCoursesTable = "student_courses";

    private $id;
    private $formation_id;
    private $course_name;
    private $description;
    private $created_at;
    private $chapter_id;
    private $text;
    private $image;
    private $video;
    private $quiz_id;
    private $test_id;

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

    public function setCourseName($course_name)
    {
        $this->course_name = $course_name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setChapterId($chapter_id)
    {
        $this->chapter_id = $chapter_id;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function setVideo($video)
    {
        $this->video = $video;
    }

    public function setQuizId($quiz_id)
    {
        $this->quiz_id = $quiz_id;
    }

    public function setTestId($test_id)
    {
        $this->test_id = $test_id;
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

    public function getCourseName()
    {
        return $this->course_name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getChapterId()
    {
        return $this->chapter_id;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getVideo()
    {
        return $this->video;
    }

    public function getQuizId()
    {
        return $this->quiz_id;
    }

    public function getTestId()
    {
        return $this->test_id;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getLastInsertId()
    {
        return $this->conn->lastInsertId();
    }

    // Assign a course to a student
    public function assignToStudent($studentId, $courseId)
    {
        // Check if the course exists
        $query = "SELECT id FROM " . $this->table . " WHERE id = :course_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId);
        $stmt->execute();

        if ($stmt->fetchColumn()) {
            // Insert the student with the course_id
            $insertQuery = "INSERT INTO " . $this->studentCoursesTable . " (student_id, course_id, assigned_at)
                            VALUES (:student_id, :course_id, NOW())";
            $insertStmt = $this->conn->prepare($insertQuery);
            $insertStmt->bindParam(':student_id', $studentId);
            $insertStmt->bindParam(':course_id', $courseId);

            return $insertStmt->execute();
        } else {
            throw new Exception('Course not found for the given course_id');
        }
    }

    // Get all courses
    public function getAllCourses()
    {
        $query = "SELECT id, course_name FROM " . $this->table . " ORDER BY course_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get courses by student ID
    public function getCoursesByStudent($studentId)
    {
        $query = "SELECT c.course_name 
                  FROM " . $this->studentCoursesTable . " sc
                  JOIN " . $this->table . " c ON sc.course_id = c.id 
                  WHERE sc.student_id = :student_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get courses by formation ID
    public function getCoursesByFormationId($formationId)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE formation_id = :formation_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':formation_id', $formationId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a course by ID
    public function getCourseById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create a new course
    public function createCourse()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (formation_id, course_name, description, chapter_id, text, image, video, quiz_id, test_id, created_at) 
                  VALUES (:formation_id, :course_name, :description, :chapter_id, :text, :image, :video, :quiz_id, :test_id, NOW())";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':formation_id', $this->formation_id);
        $stmt->bindParam(':course_name', $this->course_name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':chapter_id', $this->chapter_id);
        $stmt->bindParam(':text', $this->text);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':video', $this->video);
        $stmt->bindParam(':quiz_id', $this->quiz_id);
        $stmt->bindParam(':test_id', $this->test_id);

        return $stmt->execute();
    }

    // Update an existing course
    public function updateCourse()
    {
        $query = "UPDATE " . $this->table . " 
                  SET formation_id = :formation_id, course_name = :course_name, description = :description, 
                      chapter_id = :chapter_id, text = :text, image = :image, video = :video, quiz_id = :quiz_id, test_id = :test_id 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':formation_id', $this->formation_id);
        $stmt->bindParam(':course_name', $this->course_name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':chapter_id', $this->chapter_id);
        $stmt->bindParam(':text', $this->text);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':video', $this->video);
        $stmt->bindParam(':quiz_id', $this->quiz_id);
        $stmt->bindParam(':test_id', $this->test_id);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Delete a course
    public function deleteCourse()
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Get the last inserted course
    public function getLastInsertedCourse()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
