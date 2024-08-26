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

    public function sendMessageToTeacher($senderId, $receiverId, $body)
    {
            return $this->message->save($senderId, $receiverId, $body);
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
