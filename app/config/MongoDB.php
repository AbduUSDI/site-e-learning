<?php
namespace App\Config;

use MongoDB\Client;
use Exception;

class MongoDB {
    private $mongoClient;
    private $mongoCollection;

    public function __construct() {
        $uri = 'mongodb+srv://AbduUSDI:heroku123456@abdurahmanusdi.lc9y4uk.mongodb.net';
        $databaseName = 'e-learning';

        try {
            $this->mongoClient = new Client($uri);
            $this->mongoCollection = $this->mongoClient->selectDatabase($databaseName)->progression;
        } catch (Exception $erreur) {
            error_log("Erreur de connexion à MongoDB : " . $erreur->getMessage());
            throw new Exception("Impossible de se connecter à la base de données MongoDB");
        }
    }

    public function getCollection($collectionName) {
        return $this->mongoClient->selectDatabase('e-learning')->selectCollection($collectionName);
    }

    // Méthode pour créer une progression d'élève
    public function createProgression($userId, $formationId, $progress, $quizPoints, $isCompleted) {
        $data = [
            'user_id' => $userId,
            'formation_id' => $formationId,
            'progress' => $progress,
            'quiz_points' => $quizPoints,
            'is_completed' => $isCompleted
        ];

        $result = $this->mongoCollection->insertOne($data);
        return $result->getInsertedId();
    }

    // Méthode pour mettre à jour la progression d'un élève
    public function updateProgression($userId, $formationId, $progress, $quizPoints, $isCompleted) {
        $filter = ['user_id' => $userId, 'formation_id' => $formationId];
        $update = [
            '$set' => [
                'progress' => $progress,
                'quiz_points' => $quizPoints,
                'is_completed' => $isCompleted
            ]
        ];

        $result = $this->mongoCollection->updateOne($filter, $update);
        return $result->isAcknowledged();
    }

    // Méthode pour récupérer la progression d'un élève
    public function getProgression($userId, $formationId) {
        $filter = ['user_id' => $userId, 'formation_id' => $formationId];
        return $this->mongoCollection->findOne($filter);
    }

    // Méthode pour récupérer toutes les progressions d'un élève
    public function getAllProgressionsByUser($userId) {
        $filter = ['user_id' => $userId];
        return $this->mongoCollection->find($filter)->toArray();
    }

    // Méthode pour supprimer une progression
    public function deleteProgression($userId, $formationId) {
        $filter = ['user_id' => $userId, 'formation_id' => $formationId];
        $result = $this->mongoCollection->deleteOne($filter);
        return $result->isAcknowledged();
    }
}
