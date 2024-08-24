<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../../auth/login.php');
    exit;
}

require_once '../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\FormationController;
use App\Controllers\ProgressController;
use App\Controllers\AuthController;
use App\Controllers\QuizController;

$database = new Database();
$db = $database->getConnection();

$formationController = new FormationController($db);
$progressController = new ProgressController($db);
$quizController = new QuizController($db);

// Récupérer tous les quiz pour l'utilisateur
$userId = $_SESSION['user']['id'];
$quizzes = $quizController->getAllQuizzes();
$previousResults = $quizController->getPreviousResults($userId);

// Déconnexion si le bouton est cliqué
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $authController = new AuthController($db);
    $authController->logoutInFolder();
}

$formations = $formationController->getFormationsByUser($_SESSION['user']['id']);

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

    .card {
        margin-bottom: 20px;
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        padding: 15px;
        font-size: 1.5rem;
        background-color: #343a40;
        color: #ffffff;
    }

    .card-body {
        padding: 20px;
        background-color: #f8f9fa;
    }

    .list-group-item {
        background-color: #ffffff;
        border: 1px solid #ddd;
        margin-bottom: 10px;
        border-radius: 4px;
    }

    .progress {
        height: 20px;
        margin-top: 10px;
    }

    .progress-bar {
        line-height: 20px;
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
        .row {
            flex-direction: column;
        }

        .col-md-6 {
            width: 100%;
        }

        .card-header {
            font-size: 1.25rem;
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
                <a class="nav-link" href="quiz/views_quizzes.php">Mes Quiz</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="messages.php">Mes Messages</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="my_profile.php">Mon Profil</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                    Forum
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item text-dark" href="../../forum/add_thread.php">Créer une discussion</a></li>
                    <li><a class="dropdown-item text-dark" href="../../forum/threads.php">Les discussions</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-dark" href="../../forum/my_threads.php">Mes publications</a></li>
                </ul>
            </li>
        
        <form method="POST" class="form-inline my-2 my-lg-0">
            <button type="submit" name="logout" class="btn btn-outline-danger">Déconnexion</button>
        </form>
    </ul>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="text-center mb-4">Bienvenue dans votre espace étudiant</h1>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-header text-center">
                    <h3>Mes Formations</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php if (empty($formations)): ?>
                            <li class="list-group-item text-center">Aucun cours disponible</li>
                        <?php else: ?>
                            <?php foreach ($formations as $formation): ?>
                                <?php 
                                    $progressPercentage = $formationController->getStudentProgress($_SESSION['user']['id'], $formation['id']);
                                ?>
                                <li class="list-group-item">
                                    <a href="mediateque/view_courses.php?formation_id=<?php echo $formation['id']; ?>">
                                        <?php echo htmlspecialchars_decode($formation['name']); ?>
                                    </a>
                                    <div class="progress mt-2">
                                        <div class="progress-bar" role="progressbar" style="width: <?php echo $progressPercentage; ?>%;" aria-valuenow="<?php echo $progressPercentage; ?>" aria-valuemin="0" aria-valuemax="100">
                                            <?php echo round($progressPercentage); ?>%
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-header text-center">
                    <h3>Mes Quiz</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($quizzes)): ?>
                        <p class="text-center">Aucun quiz disponible.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($quizzes as $quiz): ?>
                                <li class="list-group-item">
                                    <h5><?php echo htmlspecialchars($quiz['quiz_name']); ?></h5>
                                    <p><?php echo htmlspecialchars($quiz['description']); ?></p>
                                    <?php if (isset($previousResults[$quiz['id']])): ?>
                                        <p class="text-success">Score précédent : <?php echo $previousResults[$quiz['id']]['score']; ?>%</p>
                                        <a href="quiz/take_quiz.php?quiz_id=<?php echo $quiz['id']; ?>&resume=true" class="btn btn-warning">Reprendre le quiz</a>
                                    <?php else: ?>
                                        <a href="quiz/take_quiz.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn btn-primary">Commencer le quiz</a>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../../public/templates/footer.php'; ?>
