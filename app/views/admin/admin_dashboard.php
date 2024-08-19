<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérification que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../../../app/auth/login.php');
    exit;
}

// Gestion de la durée de la session
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: ../../../app/auth/login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

require_once '../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\AuthController;

$database = new Database();
$db = $database->getConnection();

// Déconnexion si le bouton est cliqué
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    
    $authController = new AuthController($db);
    $authController->logoutAdmin();
}

include_once '../../../public/templates/header.php'; ?>

<style>
h1, h2, h3 {
    text-align: center;
}

body {
    background-image: url('../../../public/image_and_video/gif/anim_background.gif');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la classe fixed-top de la navbar */
}

h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Vérifiez que l'utilisateur est connecté et qu'il est un administrateur
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="admin_dashboard.php">Admin Dashboard</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
      <li class="nav-item">
          <a class="nav-link" href="users/manage_users.php">Gérer les Utilisateurs</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="teachers/manage_teachers.php">Gérer les Enseignants</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="students/manage_students.php">Gérer les Étudiants</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="quizzes/manage_quizzes.php">Gérer les Quiz</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="mediateque/manage_mediateque.php">Gérer la Médiathèque</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="forum/manage_forum.php">Gérer le Forum</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="rules/manage_rules.php">Gérer les Règles</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="my_profile.php">Mon profil</a>
        </li>
      </ul>
      <form method="POST" class="d-inline">
        <button type="submit" name="logout" class="btn btn-outline-danger">Déconnexion</button>
      </form>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Bienvenue, <?php echo htmlspecialchars($user['username']); ?>!</h1>
            <p></p>
        </div>
    </div>
    <div class="row">
        <!-- Derniers cours consultés -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Derniers cours consultés
                </div>
                <div class="card-body">
                    <?php if ($lastCourse): ?>
                        <h5 class="card-title"><?php echo htmlspecialchars($lastCourse['name']); ?></h5>
                        <p class="card-text">
                            Thème: <?php echo htmlspecialchars($lastCourse['theme']); ?><br>
                            Domaine: <?php echo htmlspecialchars($lastCourse['domain']); ?>
                        </p>
                        <a href="#" class="btn btn-primary">Voir le cours</a>
                    <?php else: ?>
                        <h5 class="card-title">Aucun cours n'a été consulté pour l'instant</h5>
                        <a href="#" class="btn btn-primary">Voir les cours</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quiz -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Quiz
                </div>
                <div class="card-body">
                    <h5 class="card-title">Participer aux Quiz</h5>
                    <p class="card-text">Testez vos connaissances avec nos quiz.</p>
                    <a href="#" class="btn btn-primary">Accéder aux Quiz</a>
                </div>
            </div>
        </div>

        <!-- Forum -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Forum
                </div>
                <div class="card-body">
                    <h5 class="card-title">Discussions du forum</h5>
                    <p class="card-text">Rejoignez les discussions sur le forum.</p>
                    <a href="../../forum/index.php" class="btn btn-primary">Accéder au Forum</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../../public/templates/footer.php'; ?>
