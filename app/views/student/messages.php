<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\MessageController;
use App\Controllers\AuthController;

$database = new Database();
$db = $database->getConnection();

$messageController = new MessageController($db);
$authController = new AuthController($db);

// Vérifiez que l'utilisateur est connecté
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!$user) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: ../../auth/login.php');
    exit();
}

// Gestion de la déconnexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $authController->logoutInFolder();
}

// Récupérer les messages de l'utilisateur
$messages = $messageController->getMessagesByUserId($user['id']);

include_once '../../../public/templates/header.php';
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

    .message-card {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .message-sender {
        font-weight: bold;
        margin-bottom: 10px;
        color: #007bff;
    }

    .message-content {
        font-size: 1.1rem;
        margin-bottom: 10px;
    }

    .message-date {
        font-size: 0.9rem;
        color: #888;
    }

    footer {
        background-color: #343a40;
        color: white;
        padding: 20px 0;
        text-align: center;
        margin-top: 50px;
    }

    footer a {
        color: #adb5bd;
        text-decoration: none;
    }

    footer a:hover {
        text-decoration: underline;
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

    @media (max-width: 768px) {
        .message-content {
            font-size: 1rem;
        }

        .message-sender {
            font-size: 1.1rem;
        }
    }
</style>
<nav class="navbar navbar-expand-lg navbar bg">
    <a class="navbar-brand" href="student_dashboard.php">Espace Étudiant</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="mediateque/view_courses.php">Vers les cours</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="quiz/views_quizzes.php">Les quiz</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="evaluationDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                    Evaluations
                </a>
                <ul class="dropdown-menu" aria-labelledby="evaluationDropdown">
                    <li><a class="dropdown-item text-dark" href="exam/view_exams.php">Voir les évaluations</a></li>
                    <li><a class="dropdown-item text-dark" href="exam/view_corrections.php">Voir les corrections</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="messages.php">Messagerie</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="my_profile.php">Mon profil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="lives/view_lives.php">Les lives</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                    Forum
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item text-dark" href="../forum/add_thread.php">Créer une discussion</a></li>
                    <li><a class="dropdown-item text-dark" href="../forum/threads.php">Les discussions</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-dark" href="../forum/my_threads.php">Mes publications</a></li>
                    <li><a class="dropdown-item text-dark" href="../forum/index.php">Accueil</a></li>
                </ul>
            </li>
        
        <form method="POST" class="form-inline my-2 my-lg-0">
            <button type="submit" name="logout" class="btn btn-outline-danger">Déconnexion</button>
        </form>
    </ul>
    </div>
</nav>
<div class="container mt-5">
    <h1 class="text-white">Mes Messages</h1>

    <?php if (count($messages) > 0): ?>
        <?php foreach ($messages as $message): ?>
            <div class="message-card">
                <div class="message-sender">De : <?php echo htmlspecialchars($message['sender_username']); ?></div>
                <div class="message-content"><?php echo htmlspecialchars($message['body']); ?></div>
                <div class="message-date">Reçu le : <?php echo htmlspecialchars($message['created_at']); ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-white">Vous n'avez aucun message.</p>
    <?php endif; ?>
</div>

<?php include '../../../public/templates/footer.php'; ?>
