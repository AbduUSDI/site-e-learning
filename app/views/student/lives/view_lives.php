<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\LiveController;
use App\Controllers\AuthController;

$database = new Database();
$db = $database->getConnection();

$liveController = new LiveController($db);
$authController = new AuthController($db);

// Vérifiez que l'utilisateur est connecté et qu'il est un étudiant
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!$user || $user['role_id'] != 3) {
    header('Location: ../../auth/login.php');
    exit();
}

// Récupérer les sessions live disponibles pour l'étudiant
$lives = $liveController->getAllLivesStudent();

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
        background-color: #f8f9fa;
        display: flex;
        flex-direction: row;
        align-items: center;
        padding: 20px;
    }

    .card img {
        border-radius: 50%;
        margin-right: 20px;
        width: 100px;
        height: 100px;
    }

    .card h5 {
        margin-bottom: 10px;
        font-size: 1.25rem;
        font-weight: bold;
    }

    .card p {
        margin-bottom: 10px;
        font-size: 1rem;
    }

    .card a {
        margin-top: 10px;
        display: inline-block;
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border-radius: 4px;
        text-decoration: none;
    }

    .card a:hover {
        background-color: #0056b3;
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
    <h2 class="text-white">Sessions Live Disponibles</h2>

    <?php if (count($lives) > 0): ?>
        <div class="row">
            <?php foreach ($lives as $live): ?>
                <div class="col-md-6">
                    <div class="card">
                        <img src="../../../../public/uploads/profil_picture/<?php echo htmlspecialchars($live['photo_profil'] ?? '../../../../public/image_and_video/avatar_default.jpg'); ?>" alt="Photo de profil du formateur">
                        <div>
                            <h5><?php echo htmlspecialchars($live['title']); ?></h5>
                            <p><?php echo htmlspecialchars($live['description']); ?></p>
                            <p><strong>Date :</strong> <?php echo htmlspecialchars($live['live_date']); ?></p>
                            <p><strong>Formateur :</strong> <?php echo htmlspecialchars($live['prenom']) . ' ' . htmlspecialchars($live['nom']); ?></p>
                            <a href="<?php echo htmlspecialchars($live['link']); ?>" target="_blank">Assister au live</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-white">Aucune session live disponible pour le moment.</p>
    <?php endif; ?>
</div>

<?php include '../../../../public/templates/footer.php'; ?>
