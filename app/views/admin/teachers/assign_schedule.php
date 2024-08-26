<?php
require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\ScheduleController;

$database = new Database();
$db = $database->getConnection();

$scheduleController = new ScheduleController($db);

$teacherId = (int)$_POST['teacher_id'];
$schedule = (string)$_POST['schedule'];

if ($scheduleController->assignScheduleToTeacher($teacherId, $schedule)) {
    echo json_encode(['status' => 'success', 'message' => 'Emploi du temps attribué avec succès.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'attribution de l\'emploi du temps.']);
}