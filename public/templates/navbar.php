<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

require_once '../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\AuthController;

$database = new Database();
$db = $database->getConnection();

$authController = new AuthController($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $authController->logout();
}

?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <?php if ($user): ?>
      <?php
        $roleId = is_object($user) ? $user->role_id : (is_array($user) ? $user['role_id'] : null);
        $rolePage = 'index.html';

        switch ($roleId) {
          case 1:
            $rolePage = '../views/admin/admin_dasboard.php';
            break;
          case 2:
            $rolePage = '../views/admin/teacher_dasboard.php';
            break;
          case 3:
            $rolePage = '../views/admin/student_dasboard.php';
            break;
          default:
            $rolePage = 'index.html';
            break;
        }
      ?>
      <a class="navbar-brand" href="<?php echo htmlspecialchars($rolePage); ?>">E-learning</a>
    <?php else: ?>
      <a class="navbar-brand" href="index.html">E-learning</a>
    <?php endif; ?>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="my_profile.php">Mon compte</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../forum/index.php">Le forum</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../forum/index.php">La médiatèque</a>
        </li>
        <?php if ($user): ?>
            <li class="nav-item">
                <form method="POST" class="d-inline">
                    <button type="submit" name="logout" class="nav-link btn btn-link">
                        Déconnexion
                    </button>
                </form>
            </li>
            <?php if ($roleId == 1): ?>
                <li class="nav-item">
                    <a class="nav-link" href="../admin/index.php">Mon espace administrateur</a>
                </li>
            <?php elseif ($roleId == 2): ?>
                <li class="nav-item">
                    <a class="nav-link" href="../doctor/index.php">Mon espace formateur</a>
                </li>
            <?php elseif ($roleId == 3): ?>
                <li class="nav-item">
                    <a class="nav-link" href="../parent/index.php">Mon espace apprenant</a>
                </li>
            <?php endif; ?>
        <?php else: ?>
            <li class="nav-item">
                <a class="nav-link" href="../login.php">Connexion</a>
            </li>
        <?php endif; ?>
      </ul>
      <form class="d-flex">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Rechercher</button>
      </form>
    </div>
  </div>
</nav>
