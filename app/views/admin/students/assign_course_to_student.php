<?php
require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\CourseController;

$database = new Database();
$db = $database->getConnection();

$courseController = new CourseController($db);

$studentId = $_POST['student_id'];
$courseName = $_POST['course_name'];

if ($courseController->assignCourseToStudent($studentId, $courseName)) {
    echo json_encode(['status' => 'success', 'message' => 'Formation ajoutée avec succès.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'ajout de la formation.']);
}
