<?php
session_start();

require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\ExamController;
use App\Controllers\AuthController;

$database = new Database();
$db = $database->getConnection();

$examController = new ExamController($db);
$authController = new AuthController($db);

// Vérifiez que l'utilisateur est connecté et qu'il est un étudiant
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!$user || $user['role_id'] != 3) {
    header('Location: ../../auth/login.php');
    exit();
}

// Récupérer toutes les corrections associées aux soumissions de l'étudiant
$feedbacks = $examController->getFeedbacksByStudentId($user['id']);

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
</style>

<div class="container mt-5">
    <h2 class="text-white">Corrections du Formateur</h2>

    <?php if (!empty($feedbacks)) : ?>
        <?php foreach ($feedbacks as $feedback) : ?>
            <div class="card">
                <div class="card-header">Examen: <?php echo htmlspecialchars($feedback['title']); ?></div>
                <div class="card-body">
                    <p><?php echo htmlspecialchars($feedback['message']); ?></p>
                    <?php if (!empty($feedback['audio_path'])): ?>
                        <audio controls>
                            <source src="<?php echo htmlspecialchars($feedback['audio_path']); ?>" type="audio/mpeg">
                            Votre navigateur ne supporte pas la lecture audio.
                        </audio>
                    <?php else: ?>
                        <p class="text-danger">Pas de message audio disponible.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-white">Aucune correction disponible pour vos soumissions.</p>
    <?php endif; ?>
</div>

<?php include '../../../../public/templates/footer.php'; ?>
