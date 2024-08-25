<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérifiez que l'utilisateur est connecté et qu'il est un administrateur
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../../../app/auth/login.php');
    exit;
}

// Gestion de la durée de la session
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: ../../../app/auth/login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\ExamController;

$database = new Database();
$db = $database->getConnection();

$examController = new ExamController($db);

// Gestion du téléchargement du fichier PDF et création de l'examen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['exam_file'])) {
    $uploadDir = '../../../../public/uploads/exams/';
    $uploadFile = $uploadDir . basename($_FILES['exam_file']['name']);
    $uploadOk = 1;

    // Vérification du fichier PDF
    $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
    if ($fileType != "pdf") {
        echo "Désolé, seuls les fichiers PDF sont autorisés.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES['exam_file']['tmp_name'], $uploadFile)) {
            $examTitle = $_POST['exam_title'];
            $examDescription = $_POST['exam_description'];
            $dueDate = $_POST['due_date'];
            $formationId = $_POST['formation_id'];

            // Insertion de l'examen dans la base de données
            $examController->createExam($examTitle, $examDescription, $uploadFile, $dueDate, $formationId);
            $message = "Examen créé avec succès.";
        } else {
            $message = "Désolé, une erreur est survenue lors du téléchargement de votre fichier.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_exam') {
    $examId = $_POST['exam_id'];
    $examController->deleteExam($examId);
    $message = "Examen supprimé avec succès.";
}

$exams = $examController->getExams();
$formations = $examController->getFormations();

header('Content-Type: text/html; charset=utf-8'); 

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
    <h1 class="text-white">Gérer les Examens / Évaluations</h1>

    <div class="card mb-4">
        <div class="card-header">
            Créer un Nouvel Examen / Évaluation
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="exam_title">Titre de l'examen / évaluation</label>
                    <input type="text" class="form-control" id="exam_title" name="exam_title" required>
                </div>
                <div class="form-group">
                    <label for="exam_description">Description</label>
                    <textarea class="form-control" id="exam_description" name="exam_description" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="formation_id">Formation</label>
                    <select class="form-control" id="formation_id" name="formation_id" required>
                        <option value="" disabled selected>Choisir une formation</option>
                        <?php foreach ($formations as $formation): ?>
                            <option value="<?php echo $formation['id']; ?>"><?php echo htmlspecialchars_decode($formation['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exam_file">Ajouter un fichier PDF</label>
                    <input type="file" class="form-control-file" id="exam_file" name="exam_file" accept=".pdf" required>
                </div>
                <div class="form-group">
                    <label for="due_date">Date limite de soumission</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" required>
                </div>
                <button type="submit" class="btn btn-primary">Créer l'examen / évaluation</button>
            </form>
        </div>
    </div>

    <h2 class="text-white">Liste des Examens / Évaluations</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Formation</th>
                    <th>Date Limite</th>
                    <th>Fichier</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><?php echo htmlspecialchars_decode($exam['title']); ?></td>
                        <td><?php echo htmlspecialchars_decode($exam['description']); ?></td>
                        <td><?php echo htmlspecialchars_decode($exam['formation_name']); ?></td>
                        <td><?php echo htmlspecialchars_decode($exam['due_date']); ?></td>
                        <td><a href="<?php echo htmlspecialchars_decode($exam['file_path']); ?>" target="_blank">Télécharger</a></td>
                        <td>
                            <form action="manage_exams.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet examen ?');">
                                <input type="hidden" name="action" value="delete_exam">
                                <input type="hidden" name="exam_id" value="<?php echo $exam['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../../../public/templates/footer.php'; ?>
