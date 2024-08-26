<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérification que l'utilisateur est connecté et a un rôle autorisé (1, 2, 3)
$allowedRoles = [1, 2, 3];

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role_id'], $allowedRoles)) {
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
use App\Config\MongoDBForum;
use App\Controllers\UserController;
use App\Controllers\ThreadController;
use App\Controllers\ProfileController;
use App\Controllers\FriendController;
use App\Controllers\MessageController;

$database = new Database();
$db = $database->getConnection();

$userController = new UserController($db);
$threadController = new ThreadController($db);
$profileController = new ProfileController($db);
$friendController = new FriendController($db);
$messageController = new MessageController($db);
$mongoClient = new MongoDBForum();

$threads = $threadController->getAllThreads();

// Récupérer les profils des utilisateurs pour afficher le nom et prénom
$userProfiles = [];
foreach ($threads as $thread) {
    $userId = $thread['user_id'];
    if (!isset($userProfiles[$userId])) {
        $userProfiles[$userId] = $profileController->getProfileByUserId($userId);
    }
}

// Récupération des vues depuis MongoDB (si applicable)
$viewsCollection = $mongoClient->getCollection('views');
$activeThreads = $viewsCollection->find([], ['sort' => ['views' => -1], 'limit' => 5])->toArray();

$threadTitles = [];
foreach ($activeThreads as $activeThread) {
    $threadId = $activeThread['thread_id'];
    foreach ($threads as $thread) {
        if ($thread['id'] == $threadId) {
            $threadTitles[$threadId] = $thread['title'];
            break;
        }
    }
}

include_once '../../../public/templates/header.php';
include_once 'templates/navbar_forum.php';
?>

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

    .threads-section {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }

    .threads-list, .active-threads {
        width: 48%;
    }

    .card {
        margin-bottom: 20px;
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #343a40;
        color: #ffffff;
        padding: 10px 15px;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
        font-weight: bold;
    }

    .card-body {
        padding: 20px;
        background-color: #f8f9fa;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: bold;
        color: #333;
    }

    .card-text {
        color: #555;
    }

    .card-footer {
        background-color: #f8f9fa;
        padding: 10px;
        border-top: none;
        border-radius: 0 0 8px 8px;
        text-align: right;
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

    .list-group-item {
        background-color: #ffffff;
        border: 1px solid #ddd;
        margin-bottom: 10px;
        border-radius: 4px;
    }

    .hero {
        background: url('../../../public/image_and_video/webp/background_image_index.webp') no-repeat center center;
        background-size: cover;
        color: white;
        text-align: center;
        padding: 40px 20px;
        border-radius: 10px;
        margin-bottom: 40px;
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
        width: 25px;
        height: 25px;
    }

    .dropdown-menu {
        background-image: url(../../../public/image_and_video/gif/anim_background.gif);
    }
</style>

<div class="container mt-5">
    <div class="hero">
        <h1 class="my-4">Forum de Discussion</h1>
        <p>Bienvenue dans le forum, explorez les discussions ou démarrez votre propre thread.</p>
    </div>

    <div class="row">
        <!-- Section Derniers Threads -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">Discussions récentes</div>
                <div class="card-body">
                    <?php if (empty($threads)): ?>
                        <p>Aucune discussion n'existe.</p>
                    <?php else: ?>
                        <ul class="list-group mb-4">
                            <?php foreach (array_slice($threads, 0, 2) as $thread): ?>
                                <li class="list-group-item">
                                    <h5 class="card-title">
                                        <a href="thread.php?id=<?php echo $thread['id']; ?>">
                                            <?php echo htmlspecialchars($thread['title']); ?>
                                        </a>
                                    </h5>
                                    <p class="card-text"><?php echo htmlspecialchars($thread['body']); ?></p>
                                    <small class="text-muted">
                                        Posté par : 
                                        <a href="#" class="user-profile-link" data-toggle="modal" data-target="#userProfileModal" data-user-id="<?php echo $thread['user_id']; ?>">
                                            <?php echo htmlspecialchars($userProfiles[$thread['user_id']]['prenom'] . ' ' . $userProfiles[$thread['user_id']]['nom']); ?>
                                        </a> 
                                        le <?php echo $thread['created_at']; ?>
                                    </small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Section Threads les Plus Actifs -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">Threads les Plus Actifs</div>
                <div class="card-body">
                    <?php if (empty($activeThreads)): ?>
                        <p>Aucune discussion n'a été trouvée.</p>
                    <?php else: ?>
                        <ul class="list-group mb-4">
                            <?php foreach ($activeThreads as $activeThread): ?>
                                <li class="list-group-item">
                                    <h5 class="card-title">
                                        <a href="thread.php?id=<?php echo $activeThread['thread_id']; ?>">
                                            <?php echo htmlspecialchars($threadTitles[$activeThread['thread_id']] ?? 'Titre inconnu'); ?>
                                        </a>
                                    </h5>
                                    <small class="text-muted">Vues: <?php echo $activeThread['views']; ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<!-- Modal Profil Utilisateur -->
<div class="modal fade" id="userProfileModal" tabindex="-1" role="dialog" aria-labelledby="userProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userProfileModalLabel">Profil de l'utilisateur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="user-profile-content">
                    <!-- Contenu du profil chargé via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="addFriendButton">Ajouter en ami</button>
                <button type="button" class="btn btn-success" id="sendMessageButton">Envoyer un message</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var profileLinks = document.querySelectorAll('.user-profile-link');

    profileLinks.forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            var userId = this.getAttribute('data-user-id');

            fetch('ajax/get_user_profile.php?user_id=' + userId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('user-profile-content').innerHTML = data;
                });

            // Ajouter les actions aux boutons
            document.getElementById('addFriendButton').onclick = function() {
                fetch('ajax/send_friend_request.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'receiver_id=' + userId
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de l\'envoi de la demande d\'ami.');
                });
            };

            document.getElementById('sendMessageButton').onclick = function() {
                var message = prompt('Entrez votre message :');
                if (message) {
                    fetch('ajax/send_message.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'receiver_id=' + userId + '&message=' + encodeURIComponent(message)
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue lors de l\'envoi du message.');
                    });
                }
            };
        });
    });
});
</script>

<?php include_once '../../../public/templates/footer.php'; ?>
