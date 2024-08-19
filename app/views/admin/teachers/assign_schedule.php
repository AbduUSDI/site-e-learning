<?php

require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\ScheduleController;

$database = new Database();
$db = $database->getConnection();

$scheduleController = new ScheduleController($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacher_id = $_POST['teacher_id'];
    $schedule = $_POST['schedule'];

    // Attribuer l'emploi du temps (à implémenter dans ScheduleController)
    $result = $scheduleController->assignScheduleToTeacher($teacher_id, $schedule);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Emploi du temps attribué avec succès.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'attribution de l\'emploi du temps.']);
    }
}
