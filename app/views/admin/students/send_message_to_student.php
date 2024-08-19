<?php
require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\MessageController;

$database = new Database();
$db = $database->getConnection();

$messageController = new MessageController($db);

$senderId = $_SESSION['user']['id'];
$receiverId = $_POST['student_id'];
$messageBody = $_POST['message'];

if ($messageController->sendMessageToStudent($senderId, $receiverId, $messageBody)) {
    echo json_encode(['status' => 'success', 'message' => 'Message envoyé avec succès.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'envoi du message.']);
}
