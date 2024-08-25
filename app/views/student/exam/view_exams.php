<?php
session_start();

require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\ExamController;
use App\Controllers\ExamSubmissionController;
use App\Controllers\AuthController;

$database = new Database();
$db = $database->getConnection();

$examController = new ExamController($db);
$examSubmissionController = new ExamSubmissionController($db);
$authController = new AuthController($db);

// Vérifiez que l'utilisateur est connecté et qu'il est un étudiant
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!$user || $user['role_id'] != 3) {
    header('Location: ../../auth/login.php');
    exit();
}

// Récupérer la liste des formations de l'étudiant
$formations = $examController->getFormationsByStudent($user['id']);

// Récupérer les examens associés à la formation sélectionnée
$selectedFormation = isset($_GET['formation_id']) ? $_GET['formation_id'] : null;
$exams = $selectedFormation ? $examController->getAllExamsByFormation($selectedFormation) : null;

// Gestion de la soumission d'examen
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['submission_file'])) {
    $examId = $_POST['exam_id'];
    $uploadDir = '../../../../public/uploads/student_submissions/';
    $uploadFile = $uploadDir . basename($_FILES['submission_file']['name']);
    $uploadOk = 1;

    // Vérification du fichier PDF
    $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
    if ($fileType != "pdf") {
        $message = "Désolé, seuls les fichiers PDF sont autorisés.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES['submission_file']['tmp_name'], $uploadFile)) {
            // Enregistrer la soumission de l'examen
            $examSubmissionController->submitExam($examId, $user['id'], $uploadFile);
            $message = "Dépôt correctement effectué, un formateur va la corriger et vous envoyer la correction.";
        } else {
            $message = "Désolé, une erreur est survenue lors du téléchargement de votre fichier.";
        }
    }
}

include_once '../../../../public/templates/header.php';
include_once '../navbar_student.php';
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

    .card {
        margin-bottom: 20px;
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        padding: 15px;
        font-size: 1.5rem;
        background-color: #343a40;
        color: #ffffff;
    }

    .card-body {
        padding: 20px;
        background-color: #f8f9fa;
    }

    .list-group-item {
        background-color: #ffffff;
        border: 1px solid #ddd;
        margin-bottom: 10px;
        border-radius: 4px;
    }

    .progress {
        height: 20px;
        margin-top: 10px;
    }

    .progress-bar {
        line-height: 20px;
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
        background-color: #fff;
        border: none;
        outline: none;
    }

    .navbar-toggler-icon {
        width: 25px;
        height: 25px;
    }

    @media (max-width: 768px) {
        .row {
            flex-direction: column;
        }

        .col-md-6 {
            width: 100%;
        }

        .card-header {
            font-size: 1.25rem;
        }
    }

    .alert {
        padding: 20px;
        background-color: #4CAF50;
        color: white;
        margin-bottom: 15px;
        border-radius: 4px;
    }

    .alert-error {
        background-color: #f44336;
    }

    .closebtn {
        margin-left: 15px;
        color: white;
        font-weight: bold;
        float: right;
        font-size: 22px;
        line-height: 20px;
        cursor: pointer;
        transition: 0.3s;
    }

    .closebtn:hover {
        color: black;
    }
</style>

<div class="container mt-5">
    <?php if ($message): ?>
        <div class="alert <?php echo strpos($message, 'Dépôt correctement effectué') !== false ? '' : 'alert-error'; ?>">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <h2 class="text-white">Sélectionnez une formation</h2>
    
    <!-- Formulaire de sélection de formation -->
    <form method="GET" action="view_exams.php">
        <div class="form-group">
            <label class="text-white" for="formation">Formation</label>
            <select class="form-control" id="formation" name="formation_id" required onchange="this.form.submit()">
                <option value="" disabled selected>Choisir une formation</option>
                <?php while ($formation = $formations->fetch(PDO::FETCH_ASSOC)) : ?>
                    <option value="<?php echo $formation['id']; ?>" <?php echo $formation['id'] == $selectedFormation ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars_decode($formation['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
    </form>

    <hr>

    <?php if ($selectedFormation && $exams) : ?>
        <h2 class="text-white">Examens disponibles</h2>
        
        <!-- Liste des examens sous forme de cartes -->
        <div class="row" id="examsContainer">
            <?php while ($exam = $exams->fetch(PDO::FETCH_ASSOC)) : ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars_decode($exam['title']); ?></h5>
                            <p class="card-description"><?php echo htmlspecialchars_decode($exam['description']); ?></p>
                            <div class="card-actions">
                                <?php if (!empty($exam['file_path'])): ?>
                                    <a class="btn btn-outline-success" href="<?php echo htmlspecialchars_decode($exam['file_path']); ?>" download>Télécharger PDF</a>
                                <?php else: ?>
                                    <span class="text-danger">PDF non disponible</span>
                                <?php endif; ?>
                            </div>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="form-group mt-3">
                                    <label for="submission_file">Déposer votre fichier PDF complété</label>
                                    <input type="file" class="form-control-file" id="submission_file" name="submission_file" accept=".pdf" required>
                                    <input type="hidden" name="exam_id" value="<?php echo $exam['id']; ?>">
                                </div>
                                <button type="submit" class="btn btn-outline-info mt-2">Soumettre l'examen</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <p class="text-white">Veuillez sélectionner une formation pour voir les examens disponibles.</p>
    <?php endif; ?>
</div>

<?php include '../../../../public/templates/footer.php'; ?>
