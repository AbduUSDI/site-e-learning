<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../../../auth/login.php');
    exit;
}

require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\QuizController;

$database = new Database();
$db = $database->getConnection();

$quizController = new QuizController($db);

// Récupérer l'ID du quiz depuis l'URL
$quizId = isset($_GET['quiz_id']) ? $_GET['quiz_id'] : null;

if (!$quizId) {
    echo "Quiz non spécifié.";
    exit;
}

// Récupérer les détails du quiz et les questions
$quiz = $quizController->getQuizById($quizId);
$questions = $quizController->getQuestionsByQuiz($quizId);

include_once '../../../../public/templates/header.php';
include_once '../navbar_student.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userAnswers = $_POST['answers'] ?? [];
    $totalQuestions = count($questions);
    $score = 0;

    foreach ($questions as $question) {
        $correctAnswers = [];
        $selectedAnswers = $userAnswers[$question['id']] ?? [];

        // Récupérer les bonnes réponses pour cette question
        foreach ($quizController->getAnswersByQuestion($question['id']) as $answer) {
            if ($answer['is_correct']) {
                $correctAnswers[] = $answer['id'];
            }
        }

        // Vérifier si les réponses sélectionnées sont correctes
        $correctSelected = !array_diff($selectedAnswers, $correctAnswers);
        $incorrectSelected = !array_diff($correctAnswers, $selectedAnswers);

        if ($correctSelected && $incorrectSelected) {
            $score++;
        }
    }

    // Calcul du pourcentage de score
    $scorePercentage = ($score / $totalQuestions) * 100;

    // Sauvegarder le score dans la table quiz_results
    $quizController->saveUserScore($quizId, $_SESSION['user']['id'], $scorePercentage);

    echo "<div class='container mt-5'><h3 class='text-white'>Votre score: " . $score . " / " . $totalQuestions . " points (" . round($scorePercentage, 2) . "%)</h3></div>";
}
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
        background-color: #343a40;
        color: #ffffff;
        padding: 10px 15px;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
        font-weight: bold;
    }

    .card-body {
        padding: 20px;
        background-color: #f8f9fa;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    .list-group-item {
        background-color: #ffffff;
        border: 1px solid #ddd;
        margin-bottom: 10px;
        border-radius: 4px;
    }
</style>

<div class="container mt-5">
    <h1 class="text-center mb-4"><?php echo htmlspecialchars($quiz['quiz_name']); ?></h1>
    <form method="post">
        <?php foreach ($questions as $question): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <?php echo htmlspecialchars($question['question_text']); ?>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($quizController->getAnswersByQuestion($question['id']) as $answer): ?>
                            <li class="list-group-item">
                                <label>
                                    <input type="checkbox" name="answers[<?php echo $question['id']; ?>][]" value="<?php echo $answer['id']; ?>">
                                    <?php echo htmlspecialchars($answer['answer_text']); ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Soumettre</button>
        </div>
    </form>
</div>

<?php include '../../../../public/templates/footer.php'; ?>
