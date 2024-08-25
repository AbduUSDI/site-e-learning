<?php
namespace App\Controllers;

use App\Models\FriendRequest;

class FriendController
{
    private $friendRequestModel;

    public function __construct($db)
    {
        $this->friendRequestModel = new FriendRequest($db);
    }

    public function sendFriendRequest($senderId, $receiverId)
    {
        if ($this->friendRequestModel->requestExists($senderId, $receiverId)) {
            return ['status' => 'error', 'message' => 'Demande déjà envoyée ou déjà amis.'];
        }

        if ($this->friendRequestModel->sendRequest($senderId, $receiverId)) {
            return ['status' => 'success', 'message' => 'Demande envoyée avec succès.'];
        }

        return ['status' => 'error', 'message' => 'Erreur lors de l\'envoi de la demande.'];
    }

    public function respondToFriendRequest($requestId, $response)
    {
        if ($this->friendRequestModel->respondToRequest($requestId, $response)) {
            if ($response == 'accepted') {
                $request = $this->friendRequestModel->getRequestById($requestId);
                $this->friendRequestModel->addFriend($request['sender_id'], $request['receiver_id']);
            }
            return ['status' => 'success', 'message' => 'Réponse envoyée avec succès.'];
        }
        return ['status' => 'error', 'message' => 'Erreur lors de l\'envoi de la réponse.'];
    }

    public function getFriends($userId)
    {
        return $this->friendRequestModel->getFriends($userId);
    }
    public function removeFriend($userId, $friendId) {
        return $this->friendRequestModel->removeFriend($userId, $friendId);
    }
    public function getPendingRequests($userId)
    {
        return $this->friendRequestModel->getPendingRequests($userId);
    }
    public function friendRequestExists($senderId, $receiverId)
    {
        return $this->friendRequestModel->requestExists($senderId, $receiverId);
    }
    public function getFriendRequests($user_id)
    {
        return $this->friendRequestModel->getFriendRequests($user_id);
    }
    // Méthode pour obtenir un utilisateur par ID
    public function getUserById($userId)
    {
        return $this->friendRequestModel->getUserById($userId);
    }
}
