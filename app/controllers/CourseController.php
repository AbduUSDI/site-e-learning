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

    public function assignCourseToStudent($studentId, $courseName)
    {
        return $this->courseModel->assignToStudent($studentId, $courseName);
    }
}
