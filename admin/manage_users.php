<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require_once '../functions/Database.php';
require_once '../functions/User.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);

$users = $user->getAllUtilisateurs();

include '../templates/header.php';
include 'navbar_admin.php';
?>
<style>
    h1, h2, h3 {
        text-align: center;
    }

    body {
        background-image: url('../image/background.jpg');
        padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
    }

    h1,
    .mt-5 {
        background: whitesmoke;
        border-radius: 15px;
    }
</style>
<div class="container mt-5">
    <h1 class="my-4">Gérer les utilisateurs</h1>
    <div class="table-responsive">
        <a href="add_user.php" class="btn btn-info mb-4">Ajouter un utilisateur</a>
        <table class="table table-bordered table-striped table-hover" style="background: white">
            <thead class="thead-dark">
                <tr>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role_id'] == 1 ? 'Administrateur' : ($user['role_id'] == 2 ? 'Formateur' : 'Apprenant')); ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../templates/footerconnected.php'; ?>
