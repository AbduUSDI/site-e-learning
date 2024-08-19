<?php

namespace App\Controllers;

use App\Models\Response;
use PDO;

class ResponseController
{
    private $responseModel;

    public function __construct($db)
    {
        $this->responseModel = new Response($db);
    }

    // Créer une nouvelle réponse
    public function createResponse($user_id, $body, $thread_id)
    {
        return $this->responseModel->createResponse($user_id, $body, $thread_id);
    }

    // Obtenir les réponses d'un utilisateur
    public function getResponsesByUserId($user_id)
    {
        return $this->responseModel->getResponsesByUserId($user_id);
    }

    // Mettre à jour une réponse
    public function updateResponse($id, $body)
    {
        return $this->responseModel->updateResponse($id, $body);
    }

    // Supprimer une réponse
    public function deleteResponse($id)
    {
        return $this->responseModel->deleteResponse($id);
    }
}
