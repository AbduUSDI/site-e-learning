<?php
session_start();

require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\QuizController;

// Vérification que l'utilisateur est authentifié et a les droits nécessaires
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Vérification que la requête est de type GET et que l'ID du quiz est fourni
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['quiz_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$quizId = $_GET['quiz_id'];

$database = new Database();
$db = $database->getConnection();

$quizController = new QuizController($db);

try {
    // Récupération des questions du quiz
    $questions = $quizController->getQuestionsByQuiz($quizId);

    // Formatage des réponses pour chaque question
    foreach ($questions as &$question) {
        $question['answers'] = $quizController->getAnswersByQuestion($question['id']);
    }

    echo json_encode(['status' => 'success', 'questions' => $questions]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}