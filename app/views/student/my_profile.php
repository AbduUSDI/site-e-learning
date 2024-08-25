<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérification que l'utilisateur est connecté et est un étudiant
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../../auth/login.php');
    exit;
}

// Gestion de la durée de la session
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: ../../auth/login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

require_once '../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\UserController;
use App\Controllers\ProfileController;
use App\Controllers\FriendController;
use App\Controllers\ThreadController;
use App\Controllers\ResponseController;
use App\Controllers\AuthController;

$database = new Database();
$db = $database->getConnection();

$userController = new UserController($db);
$profileController = new ProfileController($db);
$friendController = new FriendController($db);
$threadController = new ThreadController($db);
$responseController = new ResponseController($db);

$userId = $_SESSION['user']['id'];
$currentUser = $userController->getUserById($userId);
$userProfile = $profileController->getProfileByUserId($userId);

$friendRequests = $friendController->getFriendRequests($userId);
$friends = $friendController->getFriends($userId);
$userThreads = $threadController->getThreadsByUserId($userId);
$userResponses = $responseController->getResponsesByUserId($userId);

// Déconnexion si le bouton est cliqué
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    
    $authController = new AuthController($db);
    $authController->logoutAdmin();
}

include_once '../../../public/templates/header.php';
?>

<style>
    body {
        background: url('../../../public/image_and_video/gif/anim_background2.gif');
        font-family: Arial, sans-serif;
        color: #333;
    }
    .profile-header {
        text-align: center;
        margin-bottom: 30px;
        background-color: black;
        color: white;
        padding: 30px;
        border-radius: 10px;
    }
    .profile-header img {
        border-radius: 50%;
        width: 150px;
        height: 150px;
        object-fit: cover;
        border: 5px solid white;
    }
    .profile-header h1 {
        font-size: 2.5rem;
        margin-top: 15px;
    }
    .profile-header p {
        font-size: 1.2rem;
    }
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    .card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        padding: 20px;
    }
    .card h3 {
        margin-bottom: 20px;
        font-size: 1.5rem;
    }
    .card p {
        margin-bottom: 10px;
        font-size: 1.1rem;
    }
    .sidebar-item h3 {
        font-size: 1.25rem;
        margin-bottom: 15px;
    }
    .list-group-item {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: #f8f9fa;
        margin-bottom: 10px;
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
    .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
    }
    .btn-outline-danger:hover {
        color: #fff;
        background-color: #dc3545;
        border-color: #dc3545;
    }
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    .btn-success:hover {
        background-color: #218838;
        border-color: #218838;
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
    .profile-section {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .profile-section .left-sidebar,
    .profile-section .right-sidebar {
        flex: 1;
        margin: 0 10px;
    }
    .profile-section .main-content {
        flex: 2;
        margin: 0 10px;
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

    @media (max-width: 768px) {
        .profile-section {
            flex-direction: column;
        }
        .profile-section .left-sidebar,
        .profile-section .right-sidebar,
        .profile-section .main-content {
            margin: 0;
        }
    }
</style>
<nav class="navbar navbar-expand-lg navbar bg">
    <a class="navbar-brand" href="student_dashboard.php">Espace Étudiant</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="mediateque/view_courses.php">La médiatèque</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="quiz/views_quizzes.php">Les Quiz</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="exam/view_exams.php">Evaluations</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="messages.php">Messagerie</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="my_profile.php">Mon profil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="lives/view_lives.php">Les lives</a>
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
            <li><a class="dropdown-item text-dark" href="../forum/index.php">Page d'accueil</a></li>
          </ul>
        </li>
      <form method="POST" class="d-inline">
        <button type="submit" name="logout" class="btn btn-outline-danger">Déconnexion</button>
      </form>
    </ul>
    </div>
  </div>
</nav>
<div class="profile-header">
    <img src="../../../public/uploads/profil_picture/<?php echo htmlspecialchars($userProfile['photo_profil'] ?? 'default.jpg'); ?>" alt="Photo de profil">
    <h1><?php echo htmlspecialchars($userProfile['prenom'] . ' ' . $userProfile['nom']); ?></h1>
    <p><?php echo htmlspecialchars($userProfile['biographie'] ?? 'À propos de moi'); ?></p>
    <button class="btn btn-outline-light btn-modifier-profile" type="button" data-toggle="modal" data-target="#editProfileModal">Modifier le profil</button>
</div>

<div class="container">
    <div class="profile-section">
        <div class="left-sidebar">
            <div class="card">
                <h3>Informations personnelles</h3>
                <p><strong>Nom d'utilisateur:</strong> <?php echo htmlspecialchars($currentUser['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($currentUser['email']); ?></p>
                <p><strong>Date de naissance:</strong> <?php echo htmlspecialchars($userProfile['date_naissance']); ?></p>
                <button class="btn btn-primary btn-modifier-userprofile" type="button" data-toggle="modal" data-target="#editUserProfileModal" style="width: auto;">Modifier les informations profil</button>
            </div>

            <div class="card">
                <h3>Mes amis</h3>
                <ul class="list-group">
                    <?php if (empty($friends)): ?>
                        <li class="list-group-item">Vous n'avez pas encore d'amis.</li>
                    <?php else: ?>
                        <?php foreach ($friends as $friend): ?>
                            <li class="list-group-item">
                                <a href="#" class="friend-profile-link" data-toggle="modal" data-target="#friendProfileModal" data-user-id="<?php echo $friend['friend_id']; ?>">
                                    <?php echo htmlspecialchars($friend['username']); ?>
                                </a>
                                <form action="my_profile.php" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet ami ?');">
                                    <input type="hidden" name="action" value="remove_friend">
                                    <input type="hidden" name="friend_id" value="<?php echo $friend['friend_id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="card">
                <h3>Mon Certificat</h3>
                <?php if ($currentUser['certificate_issued']): ?>
                    <p><a href="../../../public/uploads/certificates/certificat_<?php echo $userId; ?>.pdf" target="_blank" class="btn btn-success">Télécharger le certificat</a></p>
                <?php else: ?>
                    <p>Votre certificat sera disponible ici une fois que votre cursus sera validé.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="main-content">
            <div class="card">
                <h3>Mes posts</h3>
                <?php if (empty($userThreads)): ?>
                    <p>Vous n'avez pas encore créé de post.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($userThreads as $thread): ?>
                            <li class="list-group-item">
                                <strong><?php echo htmlspecialchars($thread['title']); ?></strong>
                                <div>
                                    <button class="btn btn-sm btn-warning" data-toggle="collapse" data-target="#editThreadForm<?php echo $thread['id']; ?>">Modifier</button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce post ?');">
                                        <input type="hidden" name="action" value="delete_thread">
                                        <input type="hidden" name="id" value="<?php echo $thread['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                    </form>
                                </div>
                                <div class="collapse" id="editThreadForm<?php echo $thread['id']; ?>">
                                    <form action="my_profile.php" method="POST">
                                        <input type="hidden" name="action" value="update_thread">
                                        <input type="hidden" name="id" value="<?php echo $thread['id']; ?>">
                                        <div class="form-group">
                                            <label for="title<?php echo $thread['id']; ?>">Titre</label>
                                            <input type="text" class="form-control" id="title<?php echo $thread['id']; ?>" name="title" value="<?php echo htmlspecialchars($thread['title']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="body<?php echo $thread['id']; ?>">Contenu</label>
                                            <textarea class="form-control" id="body<?php echo $thread['id']; ?>" name="body" required><?php echo htmlspecialchars($thread['body']); ?></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success mt-2">Enregistrer les modifications</button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="card">
                <h3>Mes réponses</h3>
                <?php if (empty($userResponses)): ?>
                    <p>Vous n'avez pas encore répondu à un post.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($userResponses as $response): ?>
                            <li class="list-group-item">
                                <p><?php echo htmlspecialchars(substr($response['body'], 0, 50)) . '...'; ?></p>
                                <div>
                                    <button class="btn btn-sm btn-warning" data-toggle="collapse" data-target="#editResponseForm<?php echo $response['id']; ?>">Modifier</button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">
                                        <input type="hidden" name="action" value="delete_response">
                                        <input type="hidden" name="id" value="<?php echo $response['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                    </form>
                                </div>
                                <div class="collapse" id="editResponseForm<?php echo $response['id']; ?>">
                                    <form action="my_profile.php" method="POST">
                                        <input type="hidden" name="action" value="update_response">
                                        <input type="hidden" name="id" value="<?php echo $response['id']; ?>">
                                        <div class="form-group">
                                            <label for="body<?php echo $response['id']; ?>">Contenu</label>
                                            <textarea class="form-control" id="body<?php echo $response['id']; ?>" name="body" required><?php echo htmlspecialchars($response['body']); ?></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success mt-2">Enregistrer les modifications</button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="right-sidebar">
            <div class="card">
                <h3>Demandes d'amis en attente</h3>
                <?php if (empty($friendRequests)): ?>
                    <p>Vous n'avez pas de demandes d'amis en attente.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($friendRequests as $request): ?>
                            <li class="list-group-item">
                                <span>Demande de <?php echo htmlspecialchars($request['sender_username']); ?></span>
                                <form action="my_profile.php" method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="respond_friend_request">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                    <input type="hidden" name="status" value="accepted">
                                    <button type="submit" class="btn btn-sm btn-success">Accepter</button>
                                </form>
                                <form action="my_profile.php" method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="respond_friend_request">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                    <input type="hidden" name="status" value="declined">
                                    <button type="submit" class="btn btn-sm btn-danger">Refuser</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Profil de l'ami -->
<div class="modal fade" id="friendProfileModal" tabindex="-1" role="dialog" aria-labelledby="friendProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="friendProfileModalLabel">Profil de l'ami</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="friend-profile-content">
                    <!-- Contenu du profil chargé via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modals pour modifier le profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Modifier le profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="my_profile.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($userProfile['prenom'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($userProfile['nom'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="date_naissance" class="form-label">Date de naissance</label>
                        <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="<?php echo $userProfile['date_naissance'] ?? ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="biographie" class="form-label">Biographie</label>
                        <textarea class="form-control" id="biographie" name="biographie"><?php echo htmlspecialchars($userProfile['biographie'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="photo_profil" class="form-label">Photo de profil</label>
                        <input type="file" class="form-control" id="photo_profil" name="photo_profil">
                    </div>
                    <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserProfileModal" tabindex="-1" aria-labelledby="editUserProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserProfileModalLabel">Modifier les informations personnelles</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="my_profile.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_user_profile">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($currentUser['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe (optionnel)</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword"><i class="fas fa-eye"></i></button>
                    </div>
                    <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var modifierButtons = document.querySelectorAll('.btn-modifier');
    modifierButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var target = this.getAttribute('data-target');
            var form = document.querySelector(target);
            if (form) {
                var isExpanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !isExpanded);
                form.classList.toggle('show');
            }
        });
    });

    var modifierProfileButton = document.querySelector('.btn-modifier-profile');
    if (modifierProfileButton) {
        modifierProfileButton.addEventListener('click', function() {
            var target = this.getAttribute('data-target');
            var modal = document.querySelector(target);
            if (modal) {
                var modalInstance = new bootstrap.Modal(modal);
                modalInstance.show();
            }
        });
    }
    // Handle the profile modification button for user profile information
    var modifierUserProfileButton = document.querySelector('.btn-modifier-userprofile');
    if (modifierUserProfileButton) {
        modifierUserProfileButton.addEventListener('click', function() {
            var target = this.getAttribute('data-target');
            var modal = document.querySelector(target);
            if (modal) {
                var modalInstance = new bootstrap.Modal(modal);
                modalInstance.show();
            }
        });
    }

    var friendProfileLinks = document.querySelectorAll('.friend-profile-link');

    friendProfileLinks.forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            var userId = this.getAttribute('data-user-id');

            fetch('ajax/get_user_profile.php?friend_id=' + userId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('friend-profile-content').innerHTML = data;
                })
                .catch(error => console.error('Erreur:', error));
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('new_password');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        const eyeIcon = this.querySelector('i');
        if (type === 'password') {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    });
});
</script>

<?php include '../../../public/templates/footer.php'; ?>
