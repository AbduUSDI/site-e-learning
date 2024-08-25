<?php
session_start();

require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\UserController;
use App\Controllers\FormationController;
use App\Controllers\QuizController;
use App\Controllers\ExamSubmissionController;

$database = new Database();
$db = $database->getConnection();

$userController = new UserController($db);
$formationController = new FormationController($db);
$quizController = new QuizController($db);
$submissionController = new ExamSubmissionController($db);

// Vérifiez que l'utilisateur est connecté et qu'il est un formateur
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!$user || $user['role_id'] != 2) {
    header('Location: ../../auth/login.php');
    exit();
}

// Récupération des élèves
$students = $userController->getUsersByRole(3);
$formations = $formationController->getAllFormations();
$quizzes = $quizController->getAllQuizzes();

// Récupérer le quiz sélectionné
$selectedQuizId = isset($_GET['quiz_id']) ? $_GET['quiz_id'] : null;

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
</style>

<div class="container mt-5">
    <h1 class="text-center text-white">Gérer les Étudiants</h1>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Nom d'utilisateur</th>
                    <th>Email</th>
                    <th>Cours Assignés</th>
                    <th>Évaluations Déposées</th>
                    <th>Score Quiz</th>
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
                            $assignedFormation = $formationController->getFormationsByUser($student['id']);
                            if (!empty($assignedFormation)) {
                                foreach ($assignedFormation as $formation) {
                                    echo htmlspecialchars_decode($formation['name']) . '<br>';
                                }
                            } else {
                                echo 'Aucun cours assigné';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $submissionsCount = $submissionController->getSubmissionsCountByStudent($student['id']);
                            echo $submissionsCount ? $submissionsCount : 'Aucune soumission';
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($selectedQuizId) {
                                $score = $quizController->getUserQuizResult($selectedQuizId, $student['id']);
                                echo $score ? $score['score'] : 'Non noté';
                            } else {
                                echo 'Sélectionnez un quiz';
                            }
                            ?>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm btn-profile" data-id="<?php echo $student['id']; ?>" data-toggle="modal" data-target="#profileModal">Voir le Profil</button>
                            <button class="btn btn-secondary btn-sm btn-message" data-id="<?php echo $student['id']; ?>" data-toggle="modal" data-target="#messageModal">Envoyer un Message</button>
                            <button class="btn btn-success btn-sm btn-formation" data-id="<?php echo $student['id']; ?>" data-toggle="modal" data-target="#formationModal">Ajouter une Formation</button>
                            <button class="btn btn-warning btn-sm btn-validate" data-id="<?php echo $student['id']; ?>" data-toggle="modal" data-target="#validateModal">Valider le Cursus</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Filtre pour sélectionner un quiz -->
    <div class="form-group">
        <label class="text-white" for="quizSelect">Sélectionnez un Quiz pour voir les scores</label>
        <select id="quizSelect" class="form-control" onchange="window.location.href='manage_students.php?quiz_id=' + this.value;">
            <option value="">Choisir un quiz</option>
            <?php foreach ($quizzes as $quiz): ?>
                <option value="<?php echo $quiz['id']; ?>" <?php echo $quiz['id'] == $selectedQuizId ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($quiz['quiz_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
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
                                <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars_decode($student['username']); ?></option>
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

<div class="modal fade" id="formationModal" tabindex="-1" role="dialog" aria-labelledby="formationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formationModalLabel">Ajouter une Formation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formationFormulaire">
                <div class="form-group">
                    <label for="formationStudentId">Étudiant</label>
                    <select id="formationStudentId" class="form-control" name="user_id">
                        <?php foreach ($students as $user): ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars_decode($user['username']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                    <div class="form-group">
                        <label for="formationId">Nom de la Formation</label>
                        <select id="formationId" class="form-control" name="formation_id">
                            <?php foreach ($formations as $formation): ?>
                                <option value="<?php echo $formation['id']; ?>"><?php echo htmlspecialchars_decode($formation['name']); ?></option>
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
                <h5 class="modal-title" id="validateModalLabel">Valider l'obtention de la formation</h5>
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
    <!-- Inclusion de jQuery (version complète, pas la version 'slim' qui ne supporte pas AJAX) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
    $(document).ready(function() {
    $('.btn-profile').on('click', function() {
        var userId = $(this).data('id');

        $.ajax({
            url: 'get_profile.php',  // Fichier PHP qui va traiter la requête AJAX
            type: 'GET',
            data: { user_id: userId },
            success: function(response) {
                // Insère la réponse (le contenu du profil) dans la modal
                $('#profileModal .modal-body').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Erreur AJAX: ' + status + ' - ' + error);
            }
        });
    });
});
document.addEventListener('DOMContentLoaded', function () {
    // Gestion le formulaire de message
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
                messageForm.reset();
                $('#messageModal').modal('hide');
                location.reload();
            }
        })
        .catch(error => console.error('Erreur:', error));
    });

    // Gestion le formulaire d'ajout de formation
    const formationForm = document.getElementById('formationFormulaire');
    formationForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(formationForm);
        fetch('assign_course_to_student.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                formationForm.reset();
                $('#formationModal').modal('hide');
                location.reload();
            }
        })
        .catch(error => console.error('Erreur:', error));
    });

    // Gestion le formulaire de validation du cursus
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
                validateForm.reset();
                $('#validateModal').modal('hide');
                location.reload();
            }
        })
        .catch(error => console.error('Erreur:', error));
    });
});

</script>

<?php include '../../../../public/templates/footer.php'; ?>
