<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>

<nav class="navbar navbar-expand-lg navbar bg">
  <div class="container-fluid">
    <?php if ($user): ?>
      <?php
        $roleId = is_object($user) ? $user->role_id : (is_array($user) ? $user['role_id'] : null);
        $rolePage = '../../auth/index.html';

        switch ($roleId) {
          case 1:
            $rolePage = '../views/admin/admin_dashboard.php';
            break;
          case 2:
            $rolePage = '../views/teacher/teacher_dashboard.php';
            break;
          case 3:
            $rolePage = '../views/student/student_dashboard.php';
            break;
        }
      ?>
      <a class="navbar-brand" href="<?php echo htmlspecialchars($rolePage); ?>">E-learning platform</a>
    <?php else: ?>
      <a class="navbar-brand" href="index.php">E-learning</a>
    <?php endif; ?>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="index.php">Accueil forum</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="my_profile.php">Mon compte</a>
        </li>
        <li class="nav-item dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle text-white" type="button" data-toggle="dropdown" aria-expanded="false">
    Les discussions
  </button>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item text-black" href="add_thread.php">Créer une discussion</a></li>
            <li><a class="dropdown-item text-black" href="threads.php">Les discussions</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-black" href="my_threads.php">Mes publications</a></li>
            <li><a class="dropdown-item text-black" href="my_profile.php">Mon profil</a></li>
          </ul>
        </li>
        <?php if (isset($_SESSION['user'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="../student/logout.php">Déconnexion</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="../login.php">Connexion</a>
                </li>
            <?php endif; ?>

            <!-- Ici utilisation de if pour afficher un bouton seulement si l'utilisateur ayant le role en question est connecté, sinon rien ne s'affichera -->
            
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 1): ?>
                <li class="nav-item">
                    <a class="nav-link" href="../views/admin/admin_dashboard.php">Mon espace administrateur</a>
                </li>
            <?php endif; ?>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 2): ?>
                <li class="nav-item">
                    <a class="nav-link" href="../views/teacher/teacher_dashboard.php">Mon espace formateur</a>
                </li>
            <?php endif; ?>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 3): ?>
                <li class="nav-item">
                    <a class="nav-link" href="../views/student/student_dashboard.php">Mon espace apprenant</a>
                </li>
            <?php endif; ?>
      </ul>
      <form class="d-flex">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-secondary text-white" type="submit">Rechercher</button>
      </form>
    </div>
  </div>
</nav>