<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;
// Vérifiez que l'utilisateur est connecté et qu'il est un administrateur
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

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
        background: url('../../../../public/image_and_video/webp/background_image_index.webp') no-repeat center center;
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
    background-color: #fff; /* Changer la couleur de fond du bouton */
    border: none; /* Supprimer les bordures */
    outline: none; /* Supprimer l'outline */
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba%280, 0, 0, 0.5%29' stroke-width='2' linecap='round' linejoin='round' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        /* Remplacer la couleur de l'icône par une couleur plus foncée */
        /* Vous pouvez ajuster la couleur rgba(0, 0, 0, 0.5) pour un contraste différent */
    }

    .navbar-toggler:focus {
        outline: none; /* Assurez-vous que le bouton ne montre pas d'outline au focus */
    }
    .navbar-toggler-icon {
        width: 25px;
        height: 25px;
    }
</style>
<nav class="navbar navbar-expand-lg navbar bg">
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
