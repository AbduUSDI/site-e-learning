<?php

session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérification que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../../login.php');
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
use App\Controllers\CourseController;
use App\Controllers\MessageController;

$database = new Database();
$db = $database->getConnection();

$userController = new UserController($db);
$courseController = new CourseController($db);
$messageController = new MessageController($db);

// Récupération des apprenants
$students = $userController->getUsersByRole(3);

include_once '../../../../public/templates/header.php';
include_once '../navbar_admin.php';
?>

<style>
h1, h2, h3 {
    text-align: center;
}

body {
    background-image: url('../../../../public/image_and_video/gif/anim_background.gif');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la classe fixed-top de la navbar */
}

h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-5">
    <br>
    <hr>
    <h1 class="text-center">Gérer les Étudiants</h1>
    <hr>
    <br>
    <div class="row">
        <!-- Liste des Étudiants -->
        <div class="col-md-6">
            <h2>Liste des Étudiants</h2>
            <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nom d'utilisateur</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['username']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm btn-profile" data-id="<?php echo $student['id']; ?>" data-toggle="modal" data-target="#profileModal">Voir le Profil</button>
                                <button class="btn btn-secondary btn-sm btn-message" data-id="<?php echo $student['id']; ?>" data-toggle="modal" data-target="#messageModal">Envoyer un Message</button>
                                <button class="btn btn-success btn-sm btn-course" data-id="<?php echo $student['id']; ?>" data-toggle="modal" data-target="#courseModal">Ajouter une Formation</button>
                                <button class="btn btn-warning btn-sm btn-validate" data-id="<?php echo $student['id']; ?>" data-toggle="modal" data-target="#validateModal">Valider le Cursus</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<!-- Modals pour Voir le Profil, Envoyer un Message, Ajouter une Formation, Valider le Cursus -->
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">Profil de l'Étudiant</h5>
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

<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Envoyer un Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="messageForm">
                    <div class="form-group">
                        <label for="messageStudentId">Étudiant</label>
                        <select id="messageStudentId" class="form-control" name="student_id">
                            <?php foreach ($students as $student): ?>
                                <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['username']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="messageBody">Message</label>
                        <textarea id="messageBody" class="form-control" name="message" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="courseModal" tabindex="-1" role="dialog" aria-labelledby="courseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseModalLabel">Ajouter une Formation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="courseForm">
                    <div class="form-group">
                        <label for="courseStudentId">Étudiant</label>
                        <select id="courseStudentId" class="form-control" name="student_id">
                            <?php foreach ($students as $student): ?>
                                <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['username']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="courseName">Nom de la Formation</label>
                        <input type="text" id="courseName" class="form-control" name="course_name" required>
                    </div>
                    <button type="submit" class="btn btn-success">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="validateModal" tabindex="-1" role="dialog" aria-labelledby="validateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="validateModalLabel">Valider le Cursus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="validateForm">
                    <div class="form-group">
                        <label for="validateStudentId">Étudiant</label>
                        <select id="validateStudentId" class="form-control" name="student_id">
                            <?php foreach ($students as $student): ?>
                                <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['username']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="validationStatus">Cursus Validé</label>
                        <select id="validationStatus" class="form-control" name="validation_status">
                            <option value="1">Oui</option>
                            <option value="0">Non</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning">Valider</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Gérer le formulaire de message
    const messageForm = document.getElementById('messageForm');
    messageForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(messageForm);
        fetch('../validate/send_message_to_student.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                messageForm.reset(); // Vider le formulaire après envoi
            }
        })
        .catch(error => console.error('Erreur:', error));
    });

    // Gérer le formulaire d'ajout de formation
    const courseForm = document.getElementById('courseForm');
    courseForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(courseForm);
        fetch('../validate/assign_course_to_student.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                courseForm.reset(); // Vider le formulaire après envoi
            }
        })
        .catch(error => console.error('Erreur:', error));
    });

    // Gérer le formulaire de validation du cursus
    const validateForm = document.getElementById('validateForm');
    validateForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(validateForm);
        fetch('../validate/validate_student.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                validateForm.reset(); // Vider le formulaire après envoi
            }
        })
        .catch(error => console.error('Erreur:', error));
    });
});
</script>

<?php include '../../../../public/templates/footer.php'; ?>
