<?php

require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\MessageController;

$database = new Database();
$db = $database->getConnection();

$messageController = new MessageController($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacher_id = $_POST['teacher_id'];
    $message = $_POST['message'];

    // Envoyer le message (à implémenter dans MessageController)
    $result = $messageController->sendMessageToTeacher($teacher_id, $message);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Message envoyé avec succès.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'envoi du message.']);
    }
}