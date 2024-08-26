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
use App\Controllers\MessageController;
use App\Controllers\ScheduleController;

$database = new Database();
$db = $database->getConnection();

$userController = new UserController($db);
$messageController = new MessageController($db);
$scheduleController = new ScheduleController($db);

// Récupération des formateurs
$teachers = $userController->getUsersByRole(2);

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

    .hero {
        background: url('../../../../public/image_and_video/webp/background_image_index.webp') no-repeat center center;
        background-size: cover;
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .hero h1 {
        font-size: 3.5rem;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .hero p {
        font-size: 1.25rem;
    }

    .container {
        margin-top: 50px;
    }

    h1, h2 {
        text-align: center;
        margin-bottom: 40px;
        font-size: 2.5rem;
        font-weight: bold;
        color: white;
    }

    .card, .form-container {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        padding: 20px;
    }

    .table-responsive {
        margin-bottom: 20px;
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


<div class="container mt-5">
    <h1 class="text-center">Gérer les Enseignants</h1>

    <div class="row">
        <!-- Liste des Formateurs -->
        <div class="col-md-6">
            <h2 class="text-white">Liste des Formateurs</h2>
            <div class="card hero">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nom d'utilisateur</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teachers as $teacher): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($teacher['username']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm btn-profile" data-id="<?php echo $teacher['id']; ?>" data-toggle="modal" data-target="#profileModal">Voir le Profil</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Contacter un Formateur -->
        <div class="col-md-6">
            <h2 class="text-white">Contacter un Formateur</h2>
            <div class="form-container hero">
                <form id="contactForm">
                    <div class="form-group">
                        <label for="contactTeacherId">Formateur</label>
                        <select id="contactTeacherId" class="form-control" name="teacher_id">
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?php echo $teacher['id']; ?>"><?php echo htmlspecialchars($teacher['username']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contactMessage">Message</label>
                        <textarea id="contactMessage" class="form-control" name="body" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </form>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <!-- Emploi du Temps -->
        <div class="col-md-12">
            <h2 class="text-white">Attribuer un Emploi du Temps</h2>
            <div class="form-container hero">
                <form id="scheduleForm">
                    <div class="form-group">
                        <label for="scheduleTeacherId">Formateur</label>
                        <select id="scheduleTeacherId" class="form-control" name="teacher_id">
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?php echo $teacher['id']; ?>"><?php echo htmlspecialchars($teacher['username']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="schedule">Emploi du Temps</label>
                        <textarea id="schedule" class="form-control" name="schedule" rows="10" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Attribuer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modals pour Voir le Profil -->
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">Profil du Formateur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Contenu du profil sera chargé ici via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-profile').forEach(button => {
        button.addEventListener('click', function () {
            const teacherId = this.getAttribute('data-id');
            fetch(`get_teacher_profile.php?id=${teacherId}`)
                .then(response => response.text())
                .then(data => {
                    document.querySelector('#profileModal .modal-body').innerHTML = data;
                })
                .catch(error => console.error('Erreur:', error));
        });
    });

    // Gérer le formulaire de contact
    const contactForm = document.getElementById('contactForm');
    contactForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(contactForm);
        fetch('contact_teacher.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                contactForm.reset();
            }
        })
        .catch(error => console.error('Erreur:', error));
    });

    // Gérer le formulaire d'emploi du temps
    const scheduleForm = document.getElementById('scheduleForm');
    scheduleForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(scheduleForm);
        fetch('assign_schedule.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                scheduleForm.reset();
            }
        })
        .catch(error => console.error('Erreur:', error));
    });
});
</script>

<?php include '../../../../public/templates/footer.php'; ?>
