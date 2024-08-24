<?php
namespace App\Config;

require '../../../vendor/autoload.php';

use MongoDB\Client;
use Exception;

class MongoDBForum {
    private $mongoClient;
    private $mongoCollection;

    public function __construct() {
        $uri = 'mongodb+srv://AbduUSDI:heroku123456@abdurahmanusdi.lc9y4uk.mongodb.net';
        $databaseName = 'e-learning_forum';

        try {
            $this->mongoClient = new Client($uri);
            $this->mongoCollection = $this->mongoClient->selectDatabase($databaseName)->views;
        } catch (Exception $erreur) {
            error_log("Erreur de connexion à MongoDB : " . $erreur->getMessage());
            throw new Exception("Impossible de se connecter à la base de données MongoDB");
        }
    }
    public function getCollection($collectionName) {
        return $this->mongoClient->selectDatabase('e-learning_forum')->selectCollection($collectionName);
    }
    public function deleteThread($threadId) {
        $collection = $this->getCollection('views');
        $result = $collection->deleteOne(['thread_id' => (string)$threadId]);
        return $result->isAcknowledged();
    }
}  