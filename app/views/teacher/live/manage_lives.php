<?php
session_start();

require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\LiveController;
use App\Controllers\AuthController;

$database = new Database();
$db = $database->getConnection();

$liveController = new LiveController($db);
$authController = new AuthController($db);

// Vérifiez que l'utilisateur est connecté et qu'il est un formateur
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!$user || $user['role_id'] != 2) {
    header('Location: ../../auth/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $utilisateur_id = $user['id'];  // ID du formateur connecté
        
        if ($_POST['action'] === 'create') {
            // Création d'une nouvelle session live
            $title = $_POST['title'];
            $description = $_POST['description'];
            $date = $_POST['date'];
            $link = $_POST['link'];

            $liveController->createLive($title, $description, $date, $link, $utilisateur_id);
            $message = "Session live créée avec succès.";
        } elseif ($_POST['action'] === 'update') {
            // Mise à jour d'une session live existante
            $id = $_POST['id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $date = $_POST['date'];
            $link = $_POST['link'];

            $liveController->updateLive($id, $title, $description, $date, $link, $utilisateur_id);
            $message = "Session live mise à jour avec succès.";
        } elseif ($_POST['action'] === 'delete') {
            // Suppression d'une session live
            $id = $_POST['id'];
            $liveController->deleteLive($id);
            $message = "Session live supprimée avec succès.";
        }
    }
}


// Récupérer la liste des sessions live
$lives = $liveController->getAllLives();

include_once '../../../../public/templates/header.php';
include_once '../navbar_teacher.php';
?>

<style>
    body {
        background: url('../../../../public/image_and_video/gif/anim_background2.gif');
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
</style>

<div class="container mt-5">
    <h1 class="text-white">Gérer les Sessions Live</h1>

    <?php if (isset($message)) : ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createLiveModal">Créer une nouvelle session Live</button>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Lien</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lives as $live) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($live['title']); ?></td>
                        <td><?php echo htmlspecialchars($live['description']); ?></td>
                        <td><?php echo htmlspecialchars($live['date']); ?></td>
                        <td><a href="<?php echo htmlspecialchars($live['link']); ?>" target="_blank">Rejoindre</a></td>
                        <td>
                            <button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#editLiveModal" data-id="<?php echo $live['id']; ?>" data-title="<?php echo htmlspecialchars($live['title']); ?>" data-description="<?php echo htmlspecialchars($live['description']); ?>" data-date="<?php echo htmlspecialchars($live['date']); ?>" data-link="<?php echo htmlspecialchars($live['link']); ?>">Modifier</button>
                            <form action="manage_lives.php" method="POST" style="display:inline-block;">
                                <input type="hidden" name="id" value="<?php echo $live['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette session live ?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal pour créer une session live -->
<div class="modal fade" id="createLiveModal" tabindex="-1" role="dialog" aria-labelledby="createLiveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="manage_lives.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="createLiveModalLabel">Créer une nouvelle session Live</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">Titre</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="datetime-local" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="link">Lien</label>
                        <input type="url" class="form-control" id="link" name="link" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="action" value="create">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour modifier une session live -->
<div class="modal fade" id="editLiveModal" tabindex="-1" role="dialog" aria-labelledby="editLiveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="manage_lives.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLiveModalLabel">Modifier la session Live</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editLiveId" name="id">
                    <div class="form-group">
                        <label for="editTitle">Titre</label>
                        <input type="text" class="form-control" id="editTitle" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="editDescription">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editDate">Date</label>
                        <input type="datetime-local" class="form-control" id="editDate" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="editLink">Lien</label>
                        <input type="url" class="form-control" id="editLink" name="link" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="action" value="update">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#editLiveModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var title = button.data('title');
        var description = button.data('description');
        var date = button.data('date');
        var link = button.data('link');

        var modal = $(this);
        modal.find('#editLiveId').val(id);
        modal.find('#editTitle').val(title);
        modal.find('#editDescription').val(description);
        modal.find('#editDate').val(date);
        modal.find('#editLink').val(link);
    });
</script>

<?php include '../../../../public/templates/footer.php'; ?>
