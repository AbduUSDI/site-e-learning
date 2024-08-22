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
$courses = $courseController->getAllCourses();

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



<div class="container mt-5">
    <br>
    <hr>
    <h1 class="text-center">Gérer les Étudiants</h1>
    <hr>
    <br>
    <div class="row">
        <!-- Liste des Étudiants -->
        <div class="container hero">
            <h2>Liste des Étudiants</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nom d'utilisateur</th>
                            <th>Email</th>
                            <th>Cours Assignés</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['username']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td>
                                    <?php
                                    $assignedCourses = $courseController->getCoursesByStudent($student['id']);
                                    if (!empty($assignedCourses)) {
                                        foreach ($assignedCourses as $course) {
                                            echo htmlspecialchars($course['course_name']) . '<br>';
                                        }
                                    } else {
                                        echo 'Aucun cours assigné';
                                    }
                                    ?>
                                </td>
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
                        <label for="courseId">Nom de la Formation</label>
                        <select id="courseId" class="form-control" name="course_id">
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
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
        fetch('send_message_to_student.php', {
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
    fetch('assign_course_to_student.php', {
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
        fetch('validate_student.php', {
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
