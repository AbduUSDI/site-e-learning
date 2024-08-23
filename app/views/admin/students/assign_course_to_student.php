<?php
require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\FormationController;

$database = new Database();
$db = $database->getConnection();

$formationController = new FormationController($db);

$userId = $_POST['user_id'];
$formationId = $_POST['formation_id'];

if ($formationController->assignFormationToStudent($userId, $formationId)) {
    echo json_encode(['status' => 'success', 'message' => 'Formation ajoutée avec succès.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'ajout de la formation.']);
}
