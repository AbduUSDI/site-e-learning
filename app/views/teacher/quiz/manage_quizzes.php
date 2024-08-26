<?php

session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérification que l'utilisateur est connecté et est un formateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
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
use App\Controllers\QuizController;
use App\Controllers\FormationController;

$database = new Database();
$db = $database->getConnection();

$quizController = new QuizController($db);
$formationController = new FormationController($db);

// Gestion des actions CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'addQuiz':
            $titre = $_POST['titre'];
            $description = $_POST['description'];
            $formation_id = $_POST['formation_id'];
            $questions = $_POST['questions'] ?? [];
            $quizController->createQuizWithQuestions($titre, $description, $formation_id, $questions);
            break;
        case 'editQuiz':
            $id = $_POST['id'];
            $titre = $_POST['titre'];
            $description = $_POST['description'];
            $formation_id = $_POST['formation_id'];
            $questions = $_POST['questions'] ?? [];
            $quizController->updateQuizWithQuestions($id, $titre, $description, $formation_id, $questions);
            break;
        case 'deleteQuiz':
            $id = $_POST['id'];
            $quizController->deleteQuiz($id);
            break;
    }
}

$quizzes = $quizController->getAllQuizzes();
$courses = $formationController->getAllFormations();

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

<h1 class="my-4 text-white">Gestion des Quiz</h1>
<div class="container mt-4 hero">
    <div class="table-responsive mt-4">
        <button class="btn btn-info" data-toggle="modal" data-target="#addQuizModal">Ajouter un nouveau quiz</button>
        <table class="table table-striped table-hover mb-4" style="background: white">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quizzes as $quiz): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($quiz['id']); ?></td>
                        <td><?php echo htmlspecialchars($quiz['quiz_name']); ?></td>
                        <td><?php echo htmlspecialchars($quiz['description']); ?></td>
                        <td>
                            <button class="btn btn-warning btn-edit-quiz" data-id="<?php echo $quiz['id']; ?>" data-toggle="modal" data-target="#editQuizModal">Modifier</button>
                            <button class="btn btn-danger btn-delete-quiz" data-id="<?php echo $quiz['id']; ?>" data-toggle="modal" data-target="#deleteQuizModal">Supprimer</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal pour ajouter un quiz -->
<div class="modal fade" id="addQuizModal" tabindex="-1" aria-labelledby="addQuizModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQuizModalLabel">Ajouter un nouveau quiz</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quizForm" method="post" action="">
                    <input type="hidden" name="action" value="addQuiz">
                    <div class="mb-3">
                        <label for="quizTitle" class="form-label">Titre du quiz</label>
                        <input type="text" class="form-control" id="quizTitle" name="titre" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description du quiz</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="formation_id" class="form-label">Sélectionnez un cours</label>
                        <select class="form-control" id="formation_id" name="formation_id" required>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>"><?php echo $course['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="questionsContainer">
                        <div class="question-item">
                            <div class="mb-3">
                                <label class="form-label">Question</label>
                                <input type="text" class="form-control" name="questions[0][question_text]" required>
                            </div>
                            <div class="answersContainer">
                                <div class="mb-3">
                                    <label class="form-label">Réponse</label>
                                    <input type="text" class="form-control" name="questions[0][answers][0][answer_text]" required>
                                    <input type="checkbox" name="questions[0][answers][0][is_correct]" value="1"> Correct
                                </div>
                            </div>
                            <button type="button" class="btn btn-success add-answer-btn">Ajouter une réponse</button>
                        </div>
                    </div>
                    <button type="button" id="addQuestionButton" class="btn btn-secondary mt-3">Ajouter une question</button>
                    <button type="submit" class="btn btn-primary mt-3">Ajouter le quiz</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour modifier un quiz -->
<div class="modal fade" id="editQuizModal" tabindex="-1" aria-labelledby="editQuizModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editQuizModalLabel">Modifier le quiz</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <input type="hidden" name="action" value="editQuiz">
                    <input type="hidden" id="editQuizId" name="id">
                    <div class="mb-3">
                        <label for="editQuizTitle" class="form-label">Titre du quiz</label>
                        <input type="text" class="form-control" id="editQuizTitle" name="titre" required>
                    </div>
                    <div class="mb-3">
                        <label for="editQuizDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editQuizDescription" name="description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editFormationId" class="form-label">Sélectionnez un cours</label>
                        <select class="form-control" id="editFormationId" name="formation_id" required>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>"><?php echo $course['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="editQuestionsContainer"></div>
                    <button type="button" id="editAddQuestionButton" class="btn btn-secondary mt-3">Ajouter une question</button>
                    <button type="submit" class="btn btn-primary mt-3">Modifier le quiz</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour supprimer un quiz -->
<div class="modal fade" id="deleteQuizModal" tabindex="-1" aria-labelledby="deleteQuizModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteQuizModalLabel">Supprimer le quiz</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce quiz ?</p>
                <form method="post">
                    <input type="hidden" name="action" value="deleteQuiz">
                    <input type="hidden" id="deleteQuizId" name="id">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let questionCount = 1;

    document.getElementById('addQuestionButton').addEventListener('click', function () {
        const questionItem = document.createElement('div');
        questionItem.classList.add('question-item');
        questionItem.innerHTML = `
            <div class="mb-3">
                <label class="form-label">Question</label>
                <input type="text" class="form-control" name="questions[${questionCount}][question_text]" required>
            </div>
            <div class="answersContainer">
                <div class="mb-3">
                    <label class="form-label">Réponse</label>
                    <input type="text" class="form-control" name="questions[${questionCount}][answers][0][answer_text]" required>
                    <input type="checkbox" name="questions[${questionCount}][answers][0][is_correct]" value="1"> Correct
                </div>
            </div>
            <button type="button" class="btn btn-success add-answer-btn">Ajouter une réponse</button>
        `;
        document.getElementById('questionsContainer').appendChild(questionItem);
        questionCount++;
    });

    document.getElementById('questionsContainer').addEventListener('click', function (e) {
        if (e.target.classList.contains('add-answer-btn')) {
            const answersContainer = e.target.previousElementSibling;
            const answerCount = answersContainer.querySelectorAll('.mb-3').length;
            const questionIndex = Array.from(answersContainer.closest('.question-item').parentNode.children).indexOf(answersContainer.closest('.question-item'));
            const answerHTML = `
                <div class="mb-3">
                    <label class="form-label">Réponse</label>
                    <input type="text" class="form-control" name="questions[${questionIndex}][answers][${answerCount}][answer_text]" required>
                    <input type="checkbox" name="questions[${questionIndex}][answers][${answerCount}][is_correct]" value="1"> Correct
                </div>
            `;
            answersContainer.insertAdjacentHTML('beforeend', answerHTML);
        }
    });

    // Gestion de la modification de quiz
    document.querySelectorAll('.btn-edit-quiz').forEach(button => {
        button.addEventListener('click', function () {
            const quizId = this.getAttribute('data-id');
            document.getElementById('editQuizId').value = quizId;
            const quizName = this.closest('tr').querySelector('td:nth-child(2)').innerText;
            const description = this.closest('tr').querySelector('td:nth-child(3)').innerText;
            document.getElementById('editQuizTitle').value = quizName;
            document.getElementById('editQuizDescription').value = description;

            // Charger les questions existantes
            fetch(`get_questions.php?quiz_id=${quizId}`)
                .then(response => response.json())
                .then(data => {
                    const editQuestionsContainer = document.getElementById('editQuestionsContainer');
                    editQuestionsContainer.innerHTML = '';
                    data.questions.forEach((question, index) => {
                        const questionElement = document.createElement('div');
                        questionElement.classList.add('question-item');
                        questionElement.innerHTML = `
                            <div class="mb-3">
                                <label class="form-label">Question</label>
                                <input type="hidden" name="questions[${index}][id]" value="${question.id}">
                                <input type="text" class="form-control" name="questions[${index}][question_text]" value="${question.question_text}" required>
                            </div>
                            <div class="answersContainer">
                                ${question.answers.map((answer, answerIndex) => `
                                    <div class="mb-3">
                                        <label class="form-label">Réponse</label>
                                        <input type="hidden" name="questions[${index}][answers][${answerIndex}][id]" value="${answer.id}">
                                        <input type="text" class="form-control" name="questions[${index}][answers][${answerIndex}][answer_text]" value="${answer.answer_text}" required>
                                        <input type="checkbox" name="questions[${index}][answers][${answerIndex}][is_correct]" ${answer.is_correct ? 'checked' : ''} value="1"> Correct
                                    </div>
                                `).join('')}
                            </div>
                            <button type="button" class="btn btn-success add-answer-btn">Ajouter une réponse</button>
                        `;
                        editQuestionsContainer.appendChild(questionElement);
                    });
                });
        });
    });

    // Ajout de nouvelles questions dans le modal de modification
        document.getElementById('editAddQuestionButton').addEventListener('click', function () {
        const editQuestionsContainer = document.getElementById('editQuestionsContainer');
        const questionCount = editQuestionsContainer.querySelectorAll('.question-item').length;
        const questionItem = document.createElement('div');
        questionItem.classList.add('question-item');
        questionItem.innerHTML = `
            <div class="mb-3">
                <label class="form-label">Question</label>
                <input type="text" class="form-control" name="questions[${questionCount}][question_text]" required>
            </div>
            <div class="answersContainer">
                <div class="mb-3">
                    <label class="form-label">Réponse</label>
                    <input type="text" class="form-control" name="questions[${questionCount}][answers][0][answer_text]" required>
                    <input type="checkbox" name="questions[${questionCount}][answers][0][is_correct]" value="1"> Correct
                </div>
            </div>
            <button type="button" class="btn btn-success add-answer-btn">Ajouter une réponse</button>
        `;
        editQuestionsContainer.appendChild(questionItem);
    });


    // Ajouter une réponse dans le modal de modification
    document.getElementById('editQuestionsContainer').addEventListener('click', function (e) {
    if (e.target.classList.contains('add-answer-btn')) {
        const answersContainer = e.target.previousElementSibling;
        const answerCount = answersContainer.querySelectorAll('.mb-3').length;
        const questionIndex = Array.from(answersContainer.closest('.question-item').parentNode.children).indexOf(answersContainer.closest('.question-item'));
        const answerHTML = `
            <div class="mb-3">
                <label class="form-label">Réponse</label>
                <input type="text" class="form-control" name="questions[${questionIndex}][answers][${answerCount}][answer_text]" required>
                <input type="hidden" name="questions[${questionIndex}][answers][${answerCount}][is_correct]" value="0">
                <input type="checkbox" name="questions[${questionIndex}][answers][${answerCount}][is_correct]" value="1"> Correct
            </div>
        `;
        answersContainer.insertAdjacentHTML('beforeend', answerHTML);
    }
});


    // Gestion de la suppression de quiz
    document.querySelectorAll('.btn-delete-quiz').forEach(button => {
        button.addEventListener('click', function () {
            const quizId = this.getAttribute('data-id');
            document.getElementById('deleteQuizId').value = quizId;
        });
    });
});
</script>

<?php include '../../../../public/templates/footer.php'; ?>
