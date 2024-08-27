<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
use App\Config\Database;
use App\Controllers\AuthController;

$database = new Database();
$db = $database->getConnection();

// Vérifiez que l'utilisateur est connecté et qu'il est un formateur
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!$user || $user['role_id'] != 2) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas un formateur
    header('Location: ../../login.php');
    exit();
}
// Déconnexion si le bouton est cliqué
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    
    $authController = new AuthController($db);
    $authController->logoutInFolder();
}
?>

<nav class="navbar navbar-expand-lg navbar bg">
    <a class="navbar-brand" href="../teacher_dashboard.php">Espace enseignant</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="../my_profile.php">Mon profil</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="evaluationDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Évaluations
                </a>
                <div class="dropdown-menu" aria-labelledby="evaluationDropdown">
                    <a class="dropdown-item text-dark" href="../exams/manage_exams.php">Gérer les examens</a>
                    <a class="dropdown-item text-dark" href="../exams/correction_exam.php">Corriger les examens</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../students/manage_students.php">Gérer les élèves</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../quiz/manage_quizzes.php">Gérer les quiz</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../live/manage_lives.php">Gérer les lives</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../messages.php">Messagerie</a>
            </li>
            <li>
                <form method="POST" class="form-inline ml-auto">
                    <button type="submit" name="logout" class="btn btn-outline-danger">Déconnexion</button>
                </form>
            </li>
        </ul>
        
    </div>
</nav>
<style>
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
</style>