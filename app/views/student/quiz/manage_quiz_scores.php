<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../../../auth/login.php');
    exit;
}

require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\QuizController;

$database = new Database();
$db = $database->getConnection();

$quizController = new QuizController($db);

// Récupérer les scores de l'utilisateur
$userId = $_SESSION['user']['id'];
$scores = $quizController->getScoresByUser($userId);

include_once '../../../../public/templates/header.php';
include_once '../navbar_student.php';
?>

<style>
    body {
        background: url('../../../../public/image_and_video/gif/anim_background2.gif');
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

    .list-group-item {
        background-color: #ffffff;
        border: 1px solid #ddd;
        margin-bottom: 10px;
        border-radius: 4px;
    }
</style>

<div class="container mt-5">
    <h1 class="text-center mb-4">Mes Scores de Quiz</h1>
    <?php if (empty($scores)): ?>
        <p class="text-center">Aucun score enregistré pour le moment.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($scores as $score): ?>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header text-center">
                            Quiz: <?php echo htmlspecialchars($score['quiz_name']); ?>
                        </div>
                        <div class="card-body text-center">
                            <p>Score: <?php echo htmlspecialchars($score['score']); ?>%</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../../../../public/templates/footer.php'; ?>
