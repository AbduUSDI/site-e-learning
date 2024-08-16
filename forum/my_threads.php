<?php
session_start();
require_once '../functions/Database.php';
require_once '../functions/User.php';
require_once '../functions/Forum.php';
require_once 'MongoDB.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->connect();

$user = new User2($db);
$thread = new Thread($db);
$response = new Response($db);
$mongoClient = new MongoDB();

$userId = $_SESSION['user']['id'];
$currentUser = $user->getUserById($userId);

// Gestion des actions CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $result = false;
        $message = '';
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

        try {
            switch ($action) {
                case 'update_thread':
                    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
                    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
                    $body = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_STRING);
                
                    if ($id && $title && $body) {
                        $result = $thread->updateThread($id, $title, $body);
                        $message = $result ? "Discussion mise à jour avec succès." : "Erreur lors de la mise à jour de la discussion.";
                    } else {
                        $message = "Tous les champs sont requis pour mettre à jour une discussion.";
                    }
                    break;

                case 'update_response':
                    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
                    $body = $_POST['body'];
                    
                    if ($id && $body) {
                        $result = $response->updateResponse($id, $body);
                        $message = $result ? "Commentaire mise à jour avec succès." : "Erreur lors de la mise à jour du commentaire.";
                    } else {
                        $message = "Tous les champs sont requis pour mettre à jour un commentaire.";
                    }
                    break;

                case 'delete_thread':
                    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    
                    if ($id) {
                        $result = $thread->deleteThread($id) && $mongoClient->deleteThread($id);
                        $message = $result ? "Discussion supprimée avec succès." : "Erreur lors de la suppression de la discussion.";
                    } else {
                        $message = "ID de la discussion invalide pour la suppression.";
                    }
                    break;

                case 'delete_response':
                    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    
                    if ($id) {
                        $result = $response->deleteResponse($id);
                        $message = $result ? "Réponse supprimée avec succès." : "Erreur lors de la suppression de la réponse.";
                    } else {
                        $message = "ID de réponse invalide pour la suppression.";
                    }
                    break;

                default:
                    $message = "Action non reconnue.";
            }
        } catch (Exception $e) {
            $message = "Une erreur est survenue : " . $e->getMessage();
            error_log($e->getMessage());
        }

        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $result ? 'success' : 'danger';
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

$userThreads = $thread->getThreadsByUserId($userId);
$userResponses = $response->getResponsesByUserId($userId);

include '../templates/header.php';
include 'templates/navbar_forum.php';
?>

<style>
    body {
        background-image: url('../image/background.jpg');
        padding-top: 48px;
    }
    h1, h2, h3 {
        text-align: center;
        color: #333;
    }
    .container {
        background: rgba(255, 255, 255, 0.8);
        border-radius: 15px;
        padding: 20px;
        margin-top: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .form-control, .btn, .alert {
        border-radius: 5px;
    }
    .alert {
        margin-top: 20px;
    }
    .list-group-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<div class="container mt-5">
    <h1>Mes threads</h1>
    <?php if (empty($userThreads)): ?>
        <p>Vous n'avez pas encore créé de thread.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($userThreads as $thread): ?>
                <tr>
                    <td><?php echo htmlspecialchars($thread['title']); ?></td>
                    <td><?php echo $thread['created_at']; ?></td>
                    <td>
                        <button class="btn btn-warning btn-modifier" type="button" data-bs-toggle="collapse" data-bs-target="#editThreadForm<?php echo $thread['id']; ?>" aria-expanded="false" aria-controls="editThreadForm<?php echo $thread['id']; ?>">
                            Modifier
                        </button>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce thread ?');">
                            <input type="hidden" name="action" value="delete_thread">
                            <input type="hidden" name="id" value="<?php echo $thread['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
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
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="add_thread.php" class="btn btn-info">Créer un nouveau thread</a>
</div>

<div class="container mt-5">
    <h2>Mes réponses</h2>
    <?php if (empty($userResponses)): ?>
        <p>Vous n'avez pas encore fait de réponse.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Contenu</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userResponses as $response): ?>
                    <tr>
                        <td><?php echo htmlspecialchars(substr($response['body'], 0, 50)) . '...'; ?></td>
                        <td><?php echo $response['created_at']; ?></td>
                        <td>
                            <button class="btn btn-warning btn-modifier" type="button" data-bs-toggle="collapse" data-bs-target="#editResponseForm<?php echo $response['id']; ?>" aria-expanded="false" aria-controls="editResponseForm<?php echo $response['id']; ?>">
                                Modifier
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">
                                <input type="hidden" name="action" value="delete_response">
                                <input type="hidden" name="id" value="<?php echo $response['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="collapse" id="editResponseForm<?php echo $response['id']; ?>">
                                <form action="my_profile.php" method="POST">
                                    <input type="hidden" name="action" value="update_response">
                                    <input type="hidden" name="id" value="<?php echo $response['id']; ?>">
                                    <div class="form-group">
                                        <label for="body<?php echo $response['id']; ?>">Contenu</label>
                                        <textarea class="form-control" id="body<?php echo $response['id']; ?>" name="body" required><?php echo htmlspecialchars($response['body'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success mt-2">Enregistrer les modifications</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var modifierButtons = document.querySelectorAll('.btn-modifier');
    modifierButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var target = this.getAttribute('data-bs-target');
            var form = document.querySelector(target);
            if (form) {
                var isExpanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !isExpanded);
                form.classList.toggle('show');
            }
        });
    })});
</script>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>

<?php include '../templates/footer.php'; ?>