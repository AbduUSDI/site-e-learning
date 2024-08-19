<?php
namespace App\Controllers;

use App\Models\ForumPost;
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
    public function createThread($userId, $title, $body)
    {
        return $this->forumPost->createPost($userId, $body, 'thread', $title);
    }

    // Répondre à un thread ou à une réponse
    public function replyToPost($userId, $body, $parentId)
    {
        return $this->forumPost->createPost($userId, $body, 'reply', null, $parentId);
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
}
