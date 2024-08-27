<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
use App\Config\Database;
use App\Controllers\AuthController;

$database = new Database();
$db = $database->getConnection();

// Vérifiez que l'utilisateur est connecté et qu'il est un eleve
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!$user || $user['role_id'] != 3) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas un eleve
    header('Location: ../../auth/login.php');
    exit();
}
// Déconnexion si le bouton est cliqué
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    
    $authController = new AuthController($db);
    $authController->logoutInFolder();
}
?>

<nav class="navbar navbar-expand-lg navbar bg">
    <a class="navbar-brand" href="../student_dashboard.php">Espace Étudiant</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="../mediateque/view_courses.php">La médiatèque</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../quiz/views_quizzes.php">Les quiz</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="evaluationDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                    Evaluations
                </a>
                <ul class="dropdown-menu" aria-labelledby="evaluationDropdown">
                    <li><a class="dropdown-item text-dark" href="../exam/view_exams.php">Voir les évaluations</a></li>
                    <li><a class="dropdown-item text-dark" href="../exam/view_corrections.php">Voir les corrections</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../messages.php">Messagerie</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../my_profile.php">Mon profil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../lives/view_lives.php">Les lives</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="forumDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                    Forum
                </a>
                <ul class="dropdown-menu" aria-labelledby="forumDropdown">
                    <li><a class="dropdown-item text-dark" href="../../forum/add_thread.php">Créer une discussion</a></li>
                    <li><a class="dropdown-item text-dark" href="../../forum/threads.php">Les discussions</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-dark" href="../../forum/my_threads.php">Mes publications</a></li>
                    <li><a class="dropdown-item text-dark" href="../../forum/index.php">Page d'accueil</a></li>
                </ul>
            </li>
            <form method="POST" class="d-inline">
                <button type="submit" name="logout" class="btn btn-outline-danger">Déconnexion</button>
            </form>
        </ul>
    </div>
</nav>
