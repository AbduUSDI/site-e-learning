<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérification que l'utilisateur est connecté et a un rôle autorisé (1, 2, 3)
$allowedRoles = [1, 2, 3];

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role_id'], $allowedRoles)) {
    header('Location: ../../auth/login.php');
    exit;
}

// Gestion de la durée de la session
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: ../../auth/login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

require_once '../../../vendor/autoload.php';

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

include_once '../../../public/templates/header.php';
include_once 'templates/navbar_forum.php';
?>

<style>
    body {
        background: url('../../../public/image_and_video/gif/anim_background2.gif');
        font-family: Arial, sans-serif;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .navbar {
        background-color: #343a40;
        padding: 10px 0;
    }

    .navbar a {
        color: #ffffff;
        text-decoration: none;
        font-weight: bold;
        margin: 0 15px;
    }

    .navbar a:hover {
        text-decoration: underline;
    }

    .container {
        margin-top: 50px;
    }

    h1 {
        text-align: center;
        margin-bottom: 40px;
        font-size: 2.5rem;
        font-weight: bold;
        color: white;
    }

    .threads-section {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }

    .threads-list, .active-threads {
        width: 48%;
    }

    .card {
        margin-bottom: 20px;
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #343a40;
        color: #ffffff;
        padding: 10px 15px;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
        font-weight: bold;
    }

    .card-body {
        padding: 20px;
        background-color: #f8f9fa;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: bold;
        color: #333;
    }

    .card-text {
        color: #555;
    }

    .card-footer {
        background-color: #f8f9fa;
        padding: 10px;
        border-top: none;
        border-radius: 0 0 8px 8px;
        text-align: right;
    }

    .btn {
        font-size: 14px;
        padding: 10px 20px;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    .list-group-item {
        background-color: #ffffff;
        border: 1px solid #ddd;
        margin-bottom: 10px;
        border-radius: 4px;
    }

    .hero {
        background: url('../../../public/image_and_video/webp/background_image_index.webp') no-repeat center center;
        background-size: cover;
        text-align: center;
        padding: 40px 20px;
        border-radius: 10px;
        margin-bottom: 40px;
    }

    .hero h1 {
        font-size: 3.5rem;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .hero p {
        font-size: 1.25rem;
    }
    .zero {
        background: whitesmoke;
        text-align: center;
        padding: 40px 20px;
        border-radius: 10px;
        margin-bottom: 40px;
    }
    .navbar-toggler {
        background-color: #fff;
        border: none;
        outline: none;
    }

    .navbar-toggler-icon {
        width: 25px;
        height: 25px;
    }

    .dropdown-menu {
        background-image: url(../../../public/image_and_video/gif/anim_background.gif);
    }
</style>

<div class="container mt-4 hero">
    <h1 class="my-4"><?php echo htmlspecialchars($currentThread['title']); ?></h1>
    <div class="card">
    <div class="card-header"></div>
    <div class="mt-5 zero card-body">
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
</div>
</div>
<?php include_once '../../../public/templates/footer.php'; ?>
