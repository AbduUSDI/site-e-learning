<?php
namespace App\Controllers;

use App\Models\Message;
use Exception;

class MessageController
{
    private $message;

    public function __construct($db)
    {
        $this->message = new Message($db);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function sendMessageToTeacher($teacher_id, $content)
    {
        if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            $sender_id = $_SESSION['user']['id'];

            $this->message->setSenderId($sender_id);
            $this->message->setReceiverId($teacher_id);
            $this->message->setContent($content);

            return $this->message->save();
        } else {
            throw new Exception('Utilisateur non connecté');
        }
    }
    public function sendMessageToStudent($senderId, $receiverId, $messageBody)
    {
        return $this->message->saveStudent($senderId, $receiverId, $messageBody);
    }
    public function getMessagesByUserId($userId)
{
    return $this->message->getMessagesByUserId($userId);
}
}
