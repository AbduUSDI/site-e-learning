<?php
require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\MessageController;

$database = new Database();
$db = $database->getConnection();

$messageController = new MessageController($db);

$senderId = (int)$_SESSION['user']['id'];
$teacherId = (int)$_POST['teacher_id'];
$messageBody = (string)$_POST['body'];

if ($messageController->sendMessageToTeacher($senderId, $teacherId, $messageBody)) {
    echo json_encode(['status' => 'success', 'message' => 'Message envoyé avec succès.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'envoi du message.']);
}