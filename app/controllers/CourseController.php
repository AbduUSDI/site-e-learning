<?php
namespace App\Controllers;

use App\Models\Course;

class CourseController
{
    private $courseModel;

    public function __construct($db)
    {
        $this->courseModel = new Course($db);
    }

    public function getAllCourses()
    {
        return $this->courseModel->getAllCourses();
    }

    public function getCourseById($id)
    {
        return $this->courseModel->getCourseById($id);
    }

    public function createCourse($data)
    {
        $this->courseModel->setFormationId($data['formation_id']);
        $this->courseModel->setCourseName($data['course_name']);
        $this->courseModel->setDescription($data['description']);
        $this->courseModel->setImage($data['image']);
        $this->courseModel->setVideo($data['video']);
        return $this->courseModel->createCourse();
    }

    public function updateCourse($id, $data)
    {
        $this->courseModel->setId($id);
        $this->courseModel->setCourseName($data['course_name']);
        $this->courseModel->setDescription($data['description']);
        $this->courseModel->setImage($data['image']);
        $this->courseModel->setVideo($data['video']);
        return $this->courseModel->updateCourse();
    }

    public function deleteCourse($id)
    {
        $this->courseModel->setId($id);
        return $this->courseModel->deleteCourse();
    }
    public function getCoursesByStudent($studentId):void {
        $this->courseModel->getCoursesByStudent($studentId);
    }
    public function assignCourseToStudent($studentId) {
        $this->courseModel->getCoursesByStudent($studentId);
    }
}
