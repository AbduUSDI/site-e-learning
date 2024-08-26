<?php

use App\Controllers\AuthController;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// Déconnexion si le bouton est cliqué
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
  $authController = new AuthController($db);
  $authController->logoutAdmin();
}

header('Content-Type: text/html; charset=utf-8');
?>

<nav class="navbar navbar-expand-lg navbar bg">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Forum</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <?php if ($user): ?>
            <?php
              $roleId = is_object($user) ? $user->role_id : (is_array($user) ? $user['role_id'] : null);
              $spacePage = '../../auth/index.html';

              switch ($roleId) {
                case 1:
                  $spacePage = '../admin/my_profile.php';
                  break;
                case 2:
                  $spacePage = '../teacher/my_profile.php';
                  break;
                case 3:
                  $spacePage = '../student/my_profile.php';
                  break;
              }
            ?>
            <a class="nav-link" href="<?php echo htmlspecialchars($spacePage); ?>">Mon profil</a>
          <?php endif; ?>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
            Les discussions
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="add_thread.php">Créer une discussion</a></li>
            <li><a class="dropdown-item" href="threads.php">Les discussions</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="my_threads.php">Mes publications</a></li>
          </ul>
        </li>

        <?php if ($user && $user['role_id'] == 1): ?>
          <li class="nav-item">
            <a class="nav-link" href="../admin/admin_dashboard.php">Mon espace administrateur</a>
          </li>
        <?php endif; ?>

        <?php if ($user && $user['role_id'] == 2): ?>
          <li class="nav-item">
            <a class="nav-link" href="../teacher/teacher_dashboard.php">Mon espace formateur</a>
          </li>
        <?php endif; ?>

        <?php if ($user && $user['role_id'] == 3): ?>
          <li class="nav-item">
            <a class="nav-link" href="../student/student_dashboard.php">Mon espace apprenant</a>
          </li>
        <?php endif; ?>
      </ul>

      <form class="d-flex">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-secondary text-white" type="submit">Rechercher</button>
      </form>

      <?php if ($user): ?>
        <form method="POST" class="ml-auto">
          <button type="submit" name="logout" class="btn btn-outline-danger ml-2">Déconnexion</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</nav>
