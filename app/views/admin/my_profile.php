<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérification que l'utilisateur est connecté et est un étudiant
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
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

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// Déconnexion si le bouton est cliqué
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $authController = new AuthController($db);
    $authController->logoutAdmin();
}

if (!isset($_SESSION['user'])) {
    header('Location: ../../auth/login.php');
    exit;
}

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Initialisation des contrôleurs
$userController = new UserController($db);
$profileController = new ProfileController($db);
$friendController = new FriendController($db);
$threadController = new ThreadController($db);
$responseController = new ResponseController($db);

$userId = $_SESSION['user']['id'];
$currentUser = $userController->getUserById($userId);
$userProfile = $profileController->getProfileByUserId($userId);

// Gestion des actions du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $result = false;
        $message = '';
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

        try {
            switch ($action) {
                case 'update_profile':
                    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
                    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
                    $date_naissance = filter_input(INPUT_POST, 'date_naissance', FILTER_SANITIZE_STRING);
                    $biographie = filter_input(INPUT_POST, 'biographie', FILTER_SANITIZE_STRING);
                    $photo_profil = $userProfile['photo_profil'];

                    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] == UPLOAD_ERR_OK) {
                        $image = $_FILES['photo_profil'];
                        $allowed = ['jpg' => 'image/jpg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png'];
                        $filetype = $image['type'];

                        if (in_array($filetype, $allowed)) {
                            $imageName = time() . '_' . $image['name'];
                            if (move_uploaded_file($image['tmp_name'], '../../../public/uploads/profil_picture/' . $imageName)) {
                                $photo_profil = $imageName;
                            } else {
                                $error = "Erreur lors du téléchargement de l'image.";
                            }
                        } else {
                            $error = "Type de fichier non autorisé.";
                        }
                    }

                    if (!isset($error)) {
                        $result = $profileController->saveProfile($userId, $prenom, $nom, $date_naissance, $biographie, $photo_profil);
                        $message = $result ? "Profil mis à jour avec succès." : "Erreur lors de la mise à jour du profil.";
                        if ($result) {
                            $userProfile = $profileController->getProfileByUserId($userId);
                        }
                    } else {
                        $message = $error;
                    }
                    break;

                case 'send_friend_request':
                    $friend_username = filter_input(INPUT_POST, 'friend_username', FILTER_SANITIZE_STRING);
                    if ($friend_username) {
                        $receiver = $userController->getUserByUsername($friend_username);
                        if ($receiver) {
                            $result = $friendController->sendFriendRequest($userId, $receiver['id']);
                            $message = $result ? "Demande d'ami envoyée avec succès." : "Erreur lors de l'envoi de la demande d'ami.";
                        } else {
                            $message = "Utilisateur non trouvé.";
                        }
                    } else {
                        $message = "Nom d'utilisateur requis pour envoyer une demande d'ami.";
                    }
                    break;

                case 'respond_friend_request':
                    $requestId = filter_input(INPUT_POST, 'request_id', FILTER_SANITIZE_NUMBER_INT);
                    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

                    if ($requestId && $status) {
                        $result = $friendController->respondToFriendRequest($requestId, $status);
                        $message = $result ? "Demande d'ami $status avec succès." : "Erreur lors de la réponse à la demande d'ami.";
                    } else {
                        $message = "ID de demande d'ami invalide ou statut manquant.";
                    }
                    break;

                case 'remove_friend':
                    $friendId = filter_input(INPUT_POST, 'friend_id', FILTER_SANITIZE_NUMBER_INT);
                    if ($friendId) {
                        $result = $friendController->removeFriend($userId, $friendId);
                        $message = $result ? "Ami supprimé avec succès." : "Erreur lors de la suppression de l'ami.";
                    } else {
                        $message = "ID d'ami invalide pour la suppression.";
                    }
                    break;

                case 'update_thread':
                    $threadId = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
                    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
                    $body = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_STRING);

                    if ($threadId && $title && $body) {
                        $result = $threadController->updatePost($threadId, $title, $body);
                        $message = $result ? "Post mis à jour avec succès." : "Erreur lors de la mise à jour du post.";
                    } else {
                        $message = "Tous les champs sont requis pour mettre à jour un post.";
                    }
                    break;

                case 'delete_thread':
                    $threadId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    if ($threadId) {
                        $result = $threadController->deletePost($threadId);
                        $message = $result ? "Post supprimé avec succès." : "Erreur lors de la suppression du post.";
                    } else {
                        $message = "ID de post invalide pour la suppression.";
                    }
                    break;

                case 'update_response':
                    $responseId = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
                    $body = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_STRING);

                    if ($responseId && $body) {
                        $result = $responseController->updateResponse($responseId, $body);
                        $message = $result ? "Réponse mise à jour avec succès." : "Erreur lors de la mise à jour de la réponse.";
                    } else {
                        $message = "Tous les champs sont requis pour mettre à jour une réponse.";
                    }
                    break;

                case 'delete_response':
                    $responseId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    if ($responseId) {
                        $result = $responseController->deleteResponse($responseId);
                        $message = $result ? "Réponse supprimée avec succès." : "Erreur lors de la suppression de la réponse.";
                    } else {
                        $message = "ID de réponse invalide pour la suppression.";
                    }
                    break;

                case 'update_user_profile':
                    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
                    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                    $newPassword = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);
                    
                    if ($username && $email) {
                        $result = $userController->updateUserProfile($userId, $username, $email, $newPassword);
                        if ($result) {
                            $_SESSION['user']['username'] = $username;
                            $_SESSION['user']['email'] = $email;
                        }
                        $message = $result ? "Profil mis à jour avec succès." : "Erreur lors de la mise à jour du profil.";
                    } else {
                        $message = "Nom d'utilisateur et email requis.";
                    }
                    break;
                    
            }
        } catch (Exception $e) {
            $message = "Une erreur est survenue : " . $e->getMessage();
        }

        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $result ? 'success' : 'danger';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

$friendRequests = $friendController->getFriendRequests($userId);
$friends = $friendController->getFriends($userId);
$userThreads = $threadController->getThreadsByUserId($userId);
$userResponses = $responseController->getResponsesByUserId($userId);

include_once '../../../public/templates/header.php';
?>

<style>
    body {
        background: url('../../../public/image_and_video/gif/anim_background2.gif');
        font-family: Arial, sans-serif;
        color: #333;
        margin: 0;
        padding: 0;
    }
    .profile-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .profile-header img {
        border-radius: 50%;
        width: 150px;
        height: 150px;
        object-fit: cover;
    }

    .profile-header h1 {
        font-size: 2rem;
        margin-top: 10px;
    }

    .profile-header p {
        color: #555;
    }

    .left-sidebar, .right-sidebar, .main-content {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .sidebar-item h3, .content-item h3 {
        font-size: 1.5rem;
        margin-bottom: 15px;
    }

    .list-group-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 10px;
        background-color: #f8f9fa;
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
    .modal-header, .modal-footer {
        background-color: #f0f2f5;
    }

    .modal-title {
        font-weight: bold;
        color: #333;
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
    .bio {
        background-color: white;
        color: white;
        padding: 20px 0;
        text-align: center;
        margin-top: 50px;
        opacity: 75%;
        border-radius: 12px;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .left-sidebar, .right-sidebar, .main-content {
            margin-bottom: 15px;
        }

        .profile-header img {
            width: 100px;
            height: 100px;
        }

        .profile-header h1 {
            font-size: 1.75rem;
        }

        .hero h1 {
            font-size: 2.5rem;
        }

        .hero p {
            font-size: 1rem;
        }
    }

    @media (max-width: 576px) {
        .profile-header h1 {
            font-size: 1.5rem;
        }

        .hero h1 {
            font-size: 2rem;
        }

        .hero p {
            font-size: 0.875rem;
        }

        .profile-header img {
            width: 80px;
            height: 80px;
        }

        .profile-header h1 {
            font-size: 1.25rem;
        }

        .profile-header p {
            font-size: 0.875rem;
        }

        .btn {
            font-size: 12px;
            padding: 8px 15px;
        }
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
    <div class="profile-header">
        <img src="../../../public/uploads/profil_picture/<?php echo htmlspecialchars($userProfile['photo_profil'] ?? 'default.jpg'); ?>" alt="Photo de profil">
        <h1><?php echo htmlspecialchars($userProfile['prenom'] . ' ' . $userProfile['nom']); ?></h1>
        <p class="bio"><?php echo htmlspecialchars($userProfile['biographie'] ?? ''); ?></p>
        <button class="btn btn-primary btn-modifier-profile" type="button" data-toggle="modal" data-target="#editProfileModal">Modifier le profil</button>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-4">
            <div class="left-sidebar">
                <div class="sidebar-item">
                    <h3>Informations personnelles</h3>
                    <p><strong>Nom d'utilisateur:</strong> <?php echo htmlspecialchars($currentUser['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($currentUser['email']); ?></p>
                    <p><strong>Date de naissance:</strong> <?php echo htmlspecialchars($userProfile['date_naissance']); ?></p>
                    <button class="btn btn-primary btn-modifier-userprofile" type="button" data-toggle="modal" data-target="#editUserProfileModal" style="width: auto;">Modifier les informations profil</button>
                </div>
                <div class="sidebar-item">
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
            </div>
        </div>

        <div class="col-lg-6 col-md-8">
            <div class="main-content">
                <div class="content-item">
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
                <div class="content-item">
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
        </div>

        <div class="col-lg-3 col-md-4">
            <div class="right-sidebar">
                <div class="sidebar-item">
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
