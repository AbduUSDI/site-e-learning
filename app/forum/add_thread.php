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

// Traitement du formulaire lorsqu'il est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $body = $_POST['body'];
    $user_id = $_SESSION['user']['id'];

    // Utilisation de la méthode createPost pour créer un nouveau thread
    if ($threadController->createThread($user_id, $title, $body, 'thread')) {
        // Redirection vers la page d'accueil du forum après la création réussie du thread
        header('Location: index.php');
        exit;
    } else {
        // Message d'erreur en cas d'échec de la création du thread
        $error_message = "Erreur lors de la création de la discussion. Veuillez réessayer.";
    }
}

include_once '../../public/templates/header.php';
include_once 'templates/navbar_forum.php';
?>

<style>
h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../image/backgroundwebsite.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la classe fixed-top de la navbar */
}

h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-5">
    <h1>Créer une nouvelle discussion</h1>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="add_thread.php">
        <div class="form-group">
            <label for="title">Titre</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="body">Contenu</label>
            <textarea class="form-control" id="body" name="body" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Créer la discussion</button>
    </form>
</div>

<?php include_once '../../public/templates/footer.php'; ?>
