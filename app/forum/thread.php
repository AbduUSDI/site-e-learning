<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérification que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../../login.php');
    exit;
}

// Gestion de la durée de la session
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: ../../../auth/login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

require_once '../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\UserController;
use App\Controllers\ThreadController;
use App\Config\MongoDBForum;

$database = new Database();
$db = $database->getConnection();

$userController = new UserController($db);
$threadController = new ThreadController($db);
$mongoClient = new MongoDBForum();

$threadId = $_GET['id'];
$currentThread = $threadController->getThreadById($threadId);
$responses = $threadController->getResponsesByThreadId($threadId);

// Mise à jour des vues dans MongoDB
$viewsCollection = $mongoClient->getCollection('views');
$viewsCollection->updateOne(
    ['thread_id' => $threadId],
    ['$inc' => ['views' => 1]],
    ['upsert' => true]
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body = $_POST['body'];
    $userId = $_SESSION['user']['id'];
    // Utilisation de la méthode createResponse
    if ($threadController->createResponse($threadId, $userId, $body)) {
        header("Location: thread.php?id=$threadId");
        exit;
    } else {
        $error = "Erreur lors de l'ajout de la réponse. Veuillez réessayer.";
    }
}

include_once '../../public//templates/header.php';
include_once 'templates/navbar_forum.php';
?>

<style>
h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../image/backgroundwebsite.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
h1, .mt-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-4">
    <h1 class="my-4"><?php echo htmlspecialchars($currentThread['title']); ?></h1>
    <p><?php echo htmlspecialchars($currentThread['body']); ?></p>
    <small class="text-muted">Par <?php echo htmlspecialchars($currentThread['user_id']); ?> le <?php echo $currentThread['created_at']; ?></small>

    <h2 class="my-4">Réponses</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <ul class="list-group mb-4">
        <?php foreach ($responses as $response): ?>
            <li class="list-group-item">
                <p><?php echo htmlspecialchars($response['body'], ENT_QUOTES, 'UTF-8'); ?></p>
                <small class="text-muted">Par <?php echo htmlspecialchars($response['user_id'], ENT_QUOTES, 'UTF-8'); ?> le <?php echo htmlspecialchars($response['created_at'], ENT_QUOTES, 'UTF-8'); ?></small>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2 class="my-4">Ajouter une réponse</h2>
    <form action="thread.php?id=<?php echo $threadId; ?>" method="post">
        <div class="form-group">
            <label for="body">Votre réponse</label>
            <textarea class="form-control" id="body" name="body" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Publier</button>
    </form>
</div>

<?php include_once '../../public/templates/footer.php'; ?>
