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

$database = new Database();
$db = $database->getConnection();

$userController = new UserController($db);
$threadController = new ThreadController($db);

$threads = $threadController->getAllThreads();

include_once '../../public/templates/header.php';
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
h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-5">
    <h1 class="my-4">Forum</h1>
    <div class="row">
        <div class="col-md-8">
            <h2>Derniers Threads</h2>
            <ul class="list-group mb-4">
                <?php foreach ($threads as $thread): ?>
                    <li class="list-group-item">
                        <h5><a href="thread.php?id=<?php echo $thread['id']; ?>"><?php echo htmlspecialchars($thread['title']); ?></a></h5>
                        <p><?php echo htmlspecialchars($thread['body']); ?></p>
                        <small class="text-muted">Par <?php echo htmlspecialchars($thread['user_id']); ?> le <?php echo $thread['created_at']; ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<?php include_once '../../public/templates/footer.php'; ?>


