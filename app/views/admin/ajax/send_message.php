<?php
session_start();

require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\MessageController;

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Accès non autorisé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senderId = $_SESSION['user']['id'];
    $receiverId = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : null;
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (!$receiverId || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Données invalides fournies.']);
        exit;
    }

    $database = new Database();
    $db = $database->getConnection();
    $messageController = new MessageController($db);

    if ($messageController->sendMessageToStudent($senderId, $receiverId, $message)) {
        echo json_encode(['status' => 'success', 'message' => 'Message envoyé avec succès.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'envoi du message.']);
    }
}
