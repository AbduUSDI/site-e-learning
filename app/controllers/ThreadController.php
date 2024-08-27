<?php
namespace App\Controllers;

use App\Models\Thread;
use PDO;

class ThreadController
{
    private $forumPost;

    public function __construct(PDO $db)
    {
        $this->forumPost = new Thread($db);
    }

    // Créer un nouveau thread
    public function createThread($userId, $title, $body, $type)
{
    return $this->forumPost->createPost($userId, $title, $body, $type);
}


    // Méthode pour créer une réponse à un thread
    public function createResponse($threadId, $userId, $body)
    {
        // Appelle la méthode createResponse du modèle Thread
        return $this->forumPost->createResponse($threadId, $userId, $body);
    }

    // Récupérer tous les threads
    public function getAllThreads()
    {
        return $this->forumPost->getThreads();
    }

    // Récupérer les réponses d'un thread
    public function getThreadResponses($threadId)
    {
        return $this->forumPost->getResponses($threadId);
    }
    public function getResponsesByThreadId($threadId)
    {
        return $this->forumPost->getResponsesByThreadId($threadId);
    }
    
    // Mettre à jour un post (thread ou réponse)
    public function updatePost($postId, $title, $body)
    {
        return $this->forumPost->updatePost($postId, $title, $body);
    }

    // Supprimer un post (thread ou réponse)
    public function deletePost($postId)
    {
        return $this->forumPost->deletePost($postId);
    }
    public function getThreadsByUserId($user_id)
    {
        return $this->forumPost->getThreadsByUserId($user_id);
    }
    public function getThreadById($id) 
    {
        return $this->forumPost->getThreadById($id);
    }
}