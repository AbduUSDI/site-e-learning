<?php

session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérification que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../../../../app/auth/login.php');
    exit;
}

// Gestion de la durée de la session
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: ../../../../app/auth/login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\UserController;

try {
    // Connexion à la base de données
    $database = new Database();
    $conn = $database->getConnection();

    $userController = new UserController($conn);

    // Génération du token CSRF pour protéger les formulaires
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // Gestion des actions AJAX
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? null;
        if ($action) {
            if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                // Vérification du token CSRF
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    throw new Exception("Échec de la validation CSRF.");
                }

                // Récupération et validation des données
                $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                $password = $_POST['password'];
                $role_id = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
                $username = htmlspecialchars($_POST['username']);

                if ($email && $password && $role_id && $username) {
                    $userController->addUser($username, $email, $password, $role_id);
                    $response = ['status' => 'success', 'message' => 'Utilisateur ajouté avec succès.'];
                } else {
                    throw new Exception("Erreur dans les données saisies.");
                }
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } elseif ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                // Vérification du token CSRF
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    throw new Exception("Échec de la validation CSRF.");
                }

                // Récupération et validation des données
                $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                $password = !empty($_POST['password']) ? $_POST['password'] : null;
                $role_id = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
                $username = htmlspecialchars($_POST['username']);

                if ($id && $email && $role_id && $username) {
                    $userController->updateUser($id, $username, $email, $role_id, $password);
                    $response = ['status' => 'success', 'message' => 'Utilisateur modifié avec succès.'];
                } else {
                    throw new Exception("Erreur dans les données saisies.");
                }
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } elseif ($action === 'get' && isset($_GET['id'])) {
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                if ($id) {
                    $user = $userController->getUserById($id);
                    header('Content-Type: application/json');
                    echo json_encode($user);
                } else {
                    throw new Exception("ID invalide");
                }
                exit;
            } elseif ($action === 'delete' && isset($_GET['id'])) {
                // Vérification du token CSRF pour la suppression
                if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
                    throw new Exception("Échec de la validation CSRF.");
                }

                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                if ($id) {
                    $userController->deleteUser($id);
                    $response = ['status' => 'success', 'message' => 'Utilisateur supprimé avec succès.'];
                } else {
                    throw new Exception("Erreur: ID invalide.");
                }
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
        }
    }

    // Récupération de la liste des utilisateurs pour l'affichage
    $users = $userController->getAllUsers();

} catch (Exception $e) {
    // Gestion des erreurs
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}

include_once '../../../../public/templates/header.php';
include_once '../navbar_admin.php';

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

<br>
<hr>
<h1 class="my-4 text-white">Gérer les utilisateurs</h1>
<hr>
<br>
<div class="container mt-5 hero">
    <div class="table-responsive">
        <a href="javascript:void(0);" class="btn btn-success mb-4" data-toggle="modal" data-target="#addUserModal">Ajouter un utilisateur</a>
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role_id'] == 1 ? 'Administrateur' : ($user['role_id'] == 2 ? 'Formateur' : 'Apprenant')); ?></td>
                        <td>
                            <a href="javascript:void(0);" class="btn btn-warning btn-sm btn-edit" data-id="<?php echo $user['id']; ?>" data-toggle="modal" data-target="#editUserModal">Modifier</a>
                            <a href="javascript:void(0);" class="btn btn-danger btn-sm btn-delete" data-id="<?php echo $user['id']; ?>" data-csrf_token="<?php echo $_SESSION['csrf_token']; ?>">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modals pour Ajouter et Modifier -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Ajouter un utilisateur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="form-group">
                        <label for="addEmail">Email</label>
                        <input type="email" class="form-control" id="addEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="addPassword">Mot de passe</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="addPassword" name="password" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="addUsername">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="addUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="addRole">Rôle</label>
                        <select class="form-control" id="addRole" name="role_id" required>
                            <option value="1">Administrateur</option>
                            <option value="2">Formateur</option>
                            <option value="3">Apprenant</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Modifier un utilisateur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="editPassword">Mot de passe</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="editPassword" name="password">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editUsername">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="editUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="editRole">Rôle</label>
                        <select class="form-control" id="editRole" name="role_id" required>
                            <option value="1">Administrateur</option>
                            <option value="2">Formateur</option>
                            <option value="3">Apprenant</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning">Modifier</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Gestion des formulaires Ajouter et Modifier utilisateur
document.addEventListener('DOMContentLoaded', function () {
    const addUserForm = document.getElementById('addUserForm');
    const editUserForm = document.getElementById('editUserForm');

    addUserForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(addUserForm);
        fetch('manage_users.php?action=add', {
            method: 'POST',
            body: formData
        }).then(response => response.json()) // Traiter directement la réponse en JSON
          .then(data => {
              alert(data.message);
              if (data.status === 'success') {
                  location.reload();
              }
          })
          .catch(error => console.error('Erreur:', error));
    });

    editUserForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(editUserForm);
        fetch('manage_users.php?action=edit', {
            method: 'POST',
            body: formData
        }).then(response => response.json()) // Traiter directement la réponse en JSON
          .then(data => {
              alert(data.message);
              if (data.status === 'success') {
                  location.reload();
              }
          })
          .catch(error => console.error('Erreur:', error));
    });

    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.getAttribute('data-id');
            fetch(`manage_users.php?action=get&id=${userId}`)
                .then(response => response.json()) // Traiter directement la réponse en JSON
                .then(data => {
                    document.getElementById('editUserId').value = data.id;
                    document.getElementById('editEmail').value = data.email;
                    document.getElementById('editUsername').value = data.username;
                    document.getElementById('editRole').value = data.role_id;

                    // Ouvrir le modal après avoir inséré les données
                    $('#editUserModal').modal('show');
                })
                .catch(error => console.error('Erreur:', error));
        });
    });

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.getAttribute('data-id');
            const csrfToken = this.getAttribute('data-csrf_token');
            if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                fetch(`manage_users.php?action=delete&id=${userId}&csrf_token=${csrfToken}`)
                    .then(response => response.json()) // Traiter directement la réponse en JSON
                    .then(data => {
                        alert(data.message);
                        if (data.status === 'success') {
                            location.reload();
                        }
                    })
                    .catch(error => console.error('Erreur:', error));
            }
        });
    });

    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function () {
            const passwordField = this.closest('.input-group').querySelector('input');
            const icon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});

</script>

<?php include '../../../../public/templates/footer.php'; ?>
