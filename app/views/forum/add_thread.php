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

    .card {
        margin-bottom: 20px;
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        background-color: #343a40;
        color: #ffffff;
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
        color: #333;
    }

    .form-control {
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        color: #333;
        border-radius: 4px;
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

    .alert {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .dropdown-menu {
        background-image: url(../../../public/image_and_video/gif/anim_background.gif);
    }
</style>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            Créer une nouvelle discussion
        </div>
        <div class="card-body">
            <?php if (isset($error_message)): ?>
                <div class="alert" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <form method="post" action="add_thread.php">
                <div class="form-group">
                    <label for="title">Titre</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Titre de la discussion" required>
                </div>
                <div class="form-group mt-3">
                    <label for="body">Contenu</label>
                    <textarea class="form-control" id="body" name="body" rows="5" placeholder="Contenu de la discussion" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Créer la discussion</button>
            </form>
        </div>
    </div>
</div>

<?php include_once '../../../public/templates/footer.php'; ?>
