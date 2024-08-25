<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\AuthController;
use App\Controllers\ExamController;
use App\Controllers\ExamSubmissionController;

$database = new Database();
$db = $database->getConnection();

$authController = new AuthController($db);
$examController = new ExamController($db);
$submissionController = new ExamSubmissionController($db);

// Vérifiez que l'utilisateur est connecté et qu'il est un formateur
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!$user || $user['role_id'] != 2) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas un formateur
    header('Location: ../../login.php');
    exit();
}

// Gestion des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Déconnexion
    if (isset($_POST['logout'])) {
        $authController->logoutInFolder();
        exit();  // Assurez-vous que le script s'arrête après la déconnexion
    }

    // Soumission de feedback
    if (isset($_POST['submission_id'], $_POST['feedback_message'])) {
        $submissionId = $_POST['submission_id'];
        $message = $_POST['feedback_message'];
        $audioPath = null;

        // Gérer le téléchargement de l'audio
        if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../../../public/uploads/audio/';
            $audioPath = $uploadDir . basename($_FILES['audio_file']['name']);
            move_uploaded_file($_FILES['audio_file']['tmp_name'], $audioPath);
        }

        // Soumettre le feedback
        $examController->submitFeedback($submissionId, $message, $audioPath);
        $feedbackMessage = "Feedback envoyé avec succès.";
    }
}

// Récupérer les soumissions d'examen
$examId = isset($_GET['exam_id']) ? $_GET['exam_id'] : null;
$submissions = $examId ? $submissionController->getSubmissionsByExam($examId) : [];

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

    .corrected-message {
        color: green;
        font-weight: bold;
    }

    .corrected {
        background-color: #d4edda;
    }

</style>

<div class="container mt-5">
    <h1 class="text-white">Correction des Soumissions</h1>

    <form method="GET" action="correction_exam.php">
        <div class="form-group">
            <label class="text-white" for="exam_id">Sélectionner un examen</label>
            <select class="form-control" id="exam_id" name="exam_id" onchange="this.form.submit()">
                <option value="" disabled selected>Choisir un examen</option>
                <?php $exams = $examController->getExams(); ?>
                <?php foreach ($exams as $exam): ?>
                    <option value="<?php echo $exam['id']; ?>" <?php echo $exam['id'] == $examId ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($exam['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <hr>

    <?php if ($examId && count($submissions) > 0): ?>
        <h2 class="text-white">Soumissions pour l'examen sélectionné</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Fichier</th>
                        <th>Date de Soumission</th>
                        <th>Feedback</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submissions as $submission): ?>
                        <?php
                        // Vérifier si la soumission a déjà été corrigée
                        $feedback = $examController->getFeedbackBySubmissionId($submission['id']);
                        $isCorrected = !empty($feedback);
                        ?>
                        <tr class="<?php echo $isCorrected ? 'corrected' : ''; ?>">
                            <td><?php echo htmlspecialchars($submission['username']); ?></td>
                            <td><a href="<?php echo htmlspecialchars($submission['file_path']); ?>" target="_blank">Télécharger</a></td>
                            <td><?php echo htmlspecialchars($submission['submitted_at']); ?></td>
                            <td>
                                <?php if ($isCorrected): ?>
                                    <p class="corrected-message">Corrigé</p>
                                <?php endif; ?>
                                <form action="correction_exam.php?exam_id=<?php echo $examId; ?>" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                                    <div class="form-group">
                                        <textarea class="form-control" name="feedback_message" rows="2" placeholder="Entrez votre message de feedback ici"><?php echo $isCorrected ? htmlspecialchars($feedback['message']) : ''; ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="audio_file">Ajouter un fichier audio (facultatif)</label>
                                        <input type="file" class="form-control-file" id="audio_file" name="audio_file" accept="audio/*">
                                    </div>
                                    <button type="submit" class="btn btn-primary" <?php echo $isCorrected ? 'disabled' : ''; ?>><?php echo $isCorrected ? 'Feedback déjà soumis' : 'Envoyer le feedback'; ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($examId): ?>
        <p class="text-white">Aucune soumission pour cet examen.</p>
    <?php else: ?>
        <p class="text-white">Veuillez sélectionner un examen pour voir les soumissions.</p>
    <?php endif; ?>
</div>

<?php include '../../../../public/templates/footer.php'; ?>
