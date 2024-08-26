<?php

session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;
// Vérifiez que l'utilisateur est connecté et qu'il est un administrateur
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

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
use App\Config\MongoDB;
use App\Controllers\AuthController;
use App\Controllers\FormationController;
use App\Controllers\CategoryController;
use App\Controllers\PageController;
use App\Controllers\SubCategoryController;
use App\Controllers\ThreadController;
use App\Controllers\UserController;
use App\Controllers\ProfileController;
use App\Controllers\QuizController;

$database = new Database();
$db = $database->getConnection();

$mongoClient = new MongoDB();
$formationController = new FormationController($db);
$categoryController = new CategoryController($db);
$subCategoryController = new SubCategoryController($db);
$pageController = new PageController($db);
$profileController = new ProfileController($db);
$threadController = new ThreadController($db);
$userController = new UserController($db);
$quizController = new QuizController($db);

$users = $userController->getAllUsers();
$formations = $formationController->getAllFormations();
$pages = $pageController->getAllPages();

// Récupération des statistiques des élèves
$cursusStats = $userController->getCursusValidationStats();

// Déconnexion si le bouton est cliqué
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    
    $authController = new AuthController($db);
    $authController->logoutAdmin();
}

header('Content-Type: text/html; charset=utf-8'); 

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

    .modal-content {
        border-radius: 8px;
    }

    .modal-header, .modal-footer {
        background-color: #343a40;
        color: white;
    }

    .modal-title {
        font-weight: bold;
    }
    .h3catego {
        font-weight: bold;
    }
    .subcategomodal {
        font-style: italic;
    }
    .pagemodal {
        color: blue;
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
        color: white;
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
          <a class="nav-link" href="users/manage_users.php">Gérer les utilisateurs</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="teachers/manage_teachers.php">Gérer les enseignants</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="students/manage_students.php">Gérer les étudiants</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="quizzes/manage_quizzes.php">Gérer les quiz</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="mediateque/manage_mediateque.php">Gérer la médiathèque</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
            Forum
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item text-dark" href="../forum/add_thread.php">Créer une discussion</a></li>
            <li><a class="dropdown-item text-dark" href="../forum/threads.php">Les discussions</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-dark" href="../forum/my_threads.php">Mes publications</a></li>
            <li><a class="dropdown-item text-dark" href="../forum/index.php">Accueil forum</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="rules/manage_rules.php">Gérer les règles</a>
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
    <h2 class="text-white">Formations</h2>
    <div class="row">
        <?php foreach ($formations as $formation): ?>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <?php echo htmlspecialchars_decode($formation['name']); ?>
                    </div>
                    <div class="card-body">
                        <p><?php echo htmlspecialchars_decode($formation['description']); ?></p>
                        <button class="btn btn-info" data-toggle="modal" data-target="#formationModal<?php echo $formation['id']; ?>">Voir Détails</button>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="formationModal<?php echo $formation['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="formationModalLabel<?php echo $formation['id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="formationModalLabel<?php echo $formation['id']; ?>"><?php echo htmlspecialchars_decode($formation['name']); ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php 
                            $categories = $categoryController->getCategoriesByFormation($formation['id']);
                            foreach ($categories as $category): 
                            ?>
                                <h3 class="h3catego"><?php echo htmlspecialchars_decode($category['title']); ?></h3>
                                <?php 
                                $subcategories = $subCategoryController->getSubCategoriesByCategory($category['id']);
                                foreach ($subcategories as $subcategory): 
                                ?>
                                    <h4 class="subcategomodal"><?php echo htmlspecialchars_decode($subcategory['title']); ?></h4>
                                    <?php 
                                    $pages = $pageController->getPagesBySubCategory($subcategory['id']);
                                    foreach ($pages as $page): 
                                    ?>
                                        <div class="mt-2">
                                            <h5 class="pagemodal"><?php echo htmlspecialchars_decode($page['title']); ?></h5>
                                            <?php
                                                $base_url = '../../../public/image_and_video/mp4/';
                                                $video_url = htmlspecialchars_decode($page['video_url']);
                                                $cleaned_video_url = str_replace('../../../../public/image_and_video/mp4/', $base_url, $video_url);
                                            ?>
                                            <div class="flowplayer" data-splash="true"><video data-title="titre" controls="controls" wmode="transparent" type="video/mp4" src="<?php echo $cleaned_video_url . '?autoplay=0'; ?>" height="200" width="100%"></video></div>
                                            <p>
                                                Nombre de visionnages : <?php echo htmlspecialchars_decode($page['view_count']); ?>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="container stats-container">
    <h2 class="text-white">Statistiques des Élèves</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="stats-card card-primary text-white">
                <h5>Total des étudiants</h5>
                <p><?php echo $cursusStats['total']; ?> étudiants inscrits.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card card-success text-white">
                <h5>Cursus Validé</h5>
                <p><?php echo $cursusStats['cursus_valide']; ?> étudiants ayant validé le cursus.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card card-danger text-white">
                <h5>Cursus Non Validé</h5>
                <p><?php echo $cursusStats['cursus_non_valide']; ?> étudiants n'ayant pas validé le cursus.</p>
            </div>
        </div>
    </div>
</div>

<?php include '../../../public/templates/footer.php'; ?>
