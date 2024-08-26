<?php 
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../../auth/login.php');
    exit;
}

require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\FormationController;
use App\Controllers\AuthController;

$database = new Database();
$db = $database->getConnection();

$formationController = new FormationController($db);

// Déconnexion si le bouton est cliqué
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $authController = new AuthController($db);
    $authController->logoutInFolder();
}

// Récupérer les formations de l'étudiant
$formations = $formationController->getFormationsByUser($_SESSION['user']['id']);

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

    .progress {
        height: 20px;
        border-radius: 10px;
    }

    .progress-bar {
        background-color: #28a745;
        font-size: 14px;
        line-height: 20px;
    }
</style>

<div class="container mt-5">
    <h1 class="text-center mb-4">Mes Cours</h1>
    <?php if (empty($formations)): ?>
        <p class="text-center">Aucune formation disponible.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($formations as $formation): ?>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header text-center">
                            <?php echo htmlspecialchars_decode($formation['name']); ?>
                        </div>
                        <div class="card-body">
                            <p><?php echo htmlspecialchars_decode($formation['description']); ?></p>
                            <?php
                            // Calculer la progression de l'étudiant dans la formation
                            $progress = $formationController->getStudentProgress($_SESSION['user']['id'], $formation['id']);
                            ?>
                            <div class="progress mb-3">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $progress; ?>%;" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100">
                                    <?php echo $progress; ?>%
                                </div>
                            </div>
                            <a href="formation_details.php?formation_id=<?php echo $formation['id']; ?>" class="btn btn-primary">Voir les cours</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../../../../public/templates/footer.php'; ?>
