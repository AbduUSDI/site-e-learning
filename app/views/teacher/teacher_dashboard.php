<?php
session_start();

require_once '../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\QuizController;
use App\Controllers\ExamController;
use App\Controllers\ExamSubmissionController;
use App\Controllers\MessageController;
use App\Controllers\AuthController;

$database = new Database();
$db = $database->getConnection();

$quizController = new QuizController($db);
$examController = new ExamController($db);
$submissionController = new ExamSubmissionController($db);
$messageController = new MessageController($db);
$authController = new AuthController($db);

// Vérifiez que l'utilisateur est connecté et qu'il est un enseignant
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!$user || $user['role_id'] != 2) { // Supposant que role_id 2 est pour les enseignants
    header('Location: ../../auth/login.php');
    exit();
}

// Récupérer les données nécessaires pour le tableau de bord
$quizzes = $quizController->getAllQuizzes();
$exams = $examController->getExams();
$submissions = $submissionController->getSubmissionsByExam(0); // Récupère toutes les soumissions
$messages = $messageController->getMessagesByUserId($user['id']);

// Déconnexion si le bouton est cliqué
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    
    $authController = new AuthController($db);
    $authController->logoutAdmin();
}

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

    .table-responsive {
        margin-bottom: 50px;
    }

    .table {
        background-color: #ffffff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .table th {
        background-color: #343a40;
        color: #ffffff;
        padding: 15px;
        font-weight: bold;
        text-align: center;
    }

    .table td {
        padding: 15px;
        text-align: center;
        vertical-align: middle;
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

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #218838;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #5a6268;
    }

    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
    }

    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
    }

    .modal-content {
        border-radius: 8px;
    }

    .form-control {
        border-radius: 4px;
    }

    .form-group label {
        font-weight: 600;
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

    /* Ajout de la section "hero" pour donner une touche professionnelle */
    .hero {
        background: url('../../../public/image_and_video/webp/background_image_index.webp') no-repeat center center;
        background-size: cover;
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        border-radius: 10px;
    }

    .hero h1 {
        font-size: 3.5rem;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .hero p {
        font-size: 1.25rem;
    }

    .navbar-toggler {
    background-color: #fff;
    border: none;
    outline: none;
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba%280, 0, 0, 0.5%29' stroke-width='2' linecap='round' linejoin='round' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
    }

    .navbar-toggler:focus {
        outline: none;
    }

    .navbar-toggler-icon {
        width: 25px;
        height: 25px;
    }

    /* Style pour les formations en cartes */
    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .card-header {
        background-color: #343a40;
        color: #ffffff;
        padding: 15px;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
        font-weight: bold;
    }

    .card-body {
        padding: 20px;
        background-color: #f8f9fa;
    }

    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }

    .btn-info:hover {
        background-color: #138496;
        border-color: #117a8b;
    }
    /* Statistiques des élèves */
    .stats-container {
        margin-top: 50px;
    }

    .stats-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        margin-bottom: 20px;
        padding: 20px;
    }

    .stats-card h5 {
        font-size: 2.5rem;
        margin-bottom: 10px;
        color: #333;
    }

    .card-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .card-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .card-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .card-primary h5,
    .card-success h5,
    .card-danger h5 {
    }
    .dropmodifed {
        background-color: black;
    }
</style>

<nav class="navbar navbar-expand-lg navbar bg">
    <a class="navbar-brand" href="teacher_dashboard.php">Espace enseignant</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="my_profile.php">Mon profil</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="evaluationDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Évaluations
                </a>
                <div class="dropdown-menu dropmodified" aria-labelledby="evaluationDropdown">
                    <a class="dropdown-item text-dark" href="exams/manage_exams.php">Gérer les examens</a>
                    <a class="dropdown-item text-dark" href="exams/correction_exam.php">Corriger les examens</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="students/manage_students.php">Gérer les élèves</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="quiz/manage_quizzes.php">Gérer les quiz</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="live/manage_lives.php">Gérer les lives</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="messages.php">Messagerie</a>
            </li>
            <li class="nav-item">
        <form method="POST" class="form-inline ml-auto">
            <button type="submit" name="logout" class="btn btn-outline-danger">Déconnexion</button>
        </form>
    </li>
    </ul>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="text-center text-white">Tableau de Bord Enseignant</h1>
    
    <div class="row card-container">
        <div class="col-md-4">
            <div class="card stats-card card-primary">
                <div class="card-body">
                    <h5><?php echo count($quizzes); ?></h5>
                    <p>Mes Quiz</p>
                    <a href="quiz/manage_quizzes.php" class="btn btn-info">Gérer les Quiz</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card card-success">
                <div class="card-body">
                    <h5><?php echo count($exams); ?></h5>
                    <p>Mes Examens</p>
                    <a href="exams/manage_exams.php" class="btn btn-info">Gérer les Examens</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card card-danger">
                <div class="card-body">
                    <h5><?php echo count($submissions); ?></h5>
                    <p>Soumissions Récentes</p>
                    <a href="exams/correction_exam.php" class="btn btn-info">Voir les Soumissions</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row card-container">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Messages Récents
                </div>
                <div class="card-body">
                    <?php if (count($messages) > 0): ?>
                        <ul>
                            <?php foreach ($messages as $message): ?>
                                <li><?php echo htmlspecialchars($message['sender_username']) . ': ' . htmlspecialchars($message['body']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="messages.php" class="btn btn-secondary">Voir tous les messages</a>
                    <?php else: ?>
                        <p>Aucun message pour le moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../../public/templates/footer.php'; ?>
