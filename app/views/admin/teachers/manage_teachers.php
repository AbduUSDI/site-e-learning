<?php

session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérification que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../../../auth/login.php');
    exit;
}

// Gestion de la durée de la session
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: ../../../public/login.php');
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
    background-image: url('../../../../public/image_and_video/gif/anim_background.gif');
    font-family: Arial, sans-serif;
    padding-top: 48px; /* Pour ajuster le décalage dû à la navbar fixe */
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.card, .form-container {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    padding: 20px;
}

h1, h2 {
    text-align: center;
    margin-bottom: 20px;
    font-weight: bold;
    color: #333;
}

.table-responsive {
    margin-bottom: 20px;
}

.modal-header, .modal-footer {
    background-color: #f0f2f5;
}

.modal-title {
    font-weight: bold;
    color: #333;
}

.btn-primary, .btn-success, .btn-warning {
    border-radius: 5px;
}

.mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-5">
    <h1 class="text-center">Gérer les Enseignants</h1>

    <div class="row">
        <!-- Liste des Formateurs -->
        <div class="col-md-6">
            <div class="card">
                <h2>Liste des Formateurs</h2>
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
            <div class="form-container">
                <h2>Contacter un Formateur</h2>
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
                        <textarea id="contactMessage" class="form-control" name="message" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </form>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <!-- Emploi du Temps -->
        <div class="col-md-6">
            <div class="form-container">
                <h2>Attribuer un Emploi du Temps</h2>
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
                        <textarea id="schedule" class="form-control" name="schedule" rows="4" required></textarea>
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
            fetch(`../validate/get_teacher_profile.php?id=${teacherId}`)
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
        fetch('../validate/contact_teacher.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                contactForm.reset(); // Vider le formulaire de contact après envoi
            }
        })
        .catch(error => console.error('Erreur:', error));
    });

    // Gérer le formulaire d'emploi du temps
    const scheduleForm = document.getElementById('scheduleForm');
    scheduleForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(scheduleForm);
        fetch('../validate/assign_schedule.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                scheduleForm.reset(); // Vider le formulaire d'emploi du temps après envoi
            }
        })
        .catch(error => console.error('Erreur:', error));
    });
});
</script>

<?php include '../../../../public/templates/footer.php'; ?>
