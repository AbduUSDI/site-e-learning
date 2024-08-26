<?php
namespace App\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use Exception;
class QuizController
{
    private $db;
    private $quizModel;
    private $questionModel;
    private $answerModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->quizModel = new Quiz($db);
        $this->questionModel = new Question($db);
        $this->answerModel = new Answer($db);
    }

    public function createQuizWithQuestions($quizName, $description, $formationId, $questions)
{
    // Créer le quiz
    $this->quizModel->setQuizName($quizName);
    $this->quizModel->setDescription($description);
    $this->quizModel->setFormationId($formationId);
    $this->quizModel->createQuiz($quizName, $description, $formationId);

    // Récupérer l'ID du quiz nouvellement créé
    $quizId = $this->quizModel->getLastInsertId();

    foreach ($questions as $question) {
        // Créer chaque question associée au quiz
        $this->questionModel->setQuizId($quizId);
        $this->questionModel->setQuestionText($question['question_text']);
        $this->questionModel->create();

        // Récupérer l'ID de la question nouvellement créée
        $questionId = $this->questionModel->getLastInsertId();

        // Créer les réponses associées à la question
        foreach ($question['answers'] as $answer) {
            $this->answerModel->setQuestionId($questionId);
            $this->answerModel->setAnswerText($answer['answer_text']);
            // Utilisation de isset pour vérifier si is_correct est défini, sinon par défaut à 0
            $isCorrect = isset($answer['is_correct']) ? 1 : 0;
            $this->answerModel->setIsCorrect($isCorrect);
            $this->answerModel->create();
        }
    }
}


    public function updateQuizWithQuestions($quizId, $quizName, $description, $formationId, $questions)
    {
        // Mettre à jour le quiz
        $this->quizModel->setId($quizId);
        $this->quizModel->setQuizName($quizName);
        $this->quizModel->setDescription($description);
        $this->quizModel->setFormationId($formationId);
        $this->quizModel->updateQuiz($quizId, $quizName, $description, $formationId);

        foreach ($questions as $question) {
            if (isset($question['id']) && !empty($question['id'])) {
                // Si la question existe déjà, on la met à jour
                $this->questionModel->setId($question['id']);
                $this->questionModel->setQuestionText($question['question_text']);
                $this->questionModel->update();
            } else {
                // Si c'est une nouvelle question, on la crée
                $this->questionModel->setQuizId($quizId);
                $this->questionModel->setQuestionText($question['question_text']);
                $this->questionModel->create();
                $question['id'] = $this->questionModel->getLastInsertId();  // Récupérer l'ID de la nouvelle question
            }

            // Traiter les réponses associées à cette question
            foreach ($question['answers'] as $answer) {
                if (isset($answer['id']) && !empty($answer['id'])) {
                    // Si la réponse existe déjà, on la met à jour
                    $this->answerModel->setId($answer['id']);
                    $this->answerModel->setAnswerText($answer['answer_text']);
                    $this->answerModel->setIsCorrect(isset($answer['is_correct']) ? 1 : 0);
                    $this->answerModel->update();
                } else {
                    // Si c'est une nouvelle réponse, on la crée
                    $this->answerModel->setQuestionId($question['id']);
                    $this->answerModel->setAnswerText($answer['answer_text']);
                    $this->answerModel->setIsCorrect(isset($answer['is_correct']) ? 1 : 0);
                    $this->answerModel->create();
                }
            }
        }
    }



    public function deleteQuiz($id)
    {
        $this->db->beginTransaction();
        try {
            $this->questionModel->deleteQuestionsByQuizId($id);
            $this->quizModel->deleteQuiz($id);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getAllQuizzes()
    {
        return $this->quizModel->getAllQuizzes();
    }

    public function getQuestionsByQuiz($quiz_id)
    {
        return $this->questionModel->getQuestionsByQuiz($quiz_id);
    }

    public function getAnswersByQuestion($question_id)
    {
        return $this->answerModel->getAnswersByQuestionId($question_id);
    }
    public function getUserQuizResult($quizId, $userId) {
        return $this->quizModel->getUserQuizResult($quizId, $userId);
    }
    public function getScoresByUser($userId) {
        return $this->quizModel->getScoresByUser($userId);
    }
    public function getPreviousResults($userId) {
        return $this->quizModel->getPreviousResults($userId);
    }
    public function saveUserScore($quizId, $userId, $score) {
        return $this->quizModel->saveUserScore($quizId, $userId, $score);
    }
    public function getQuizById($id) {
        return $this->quizModel->getQuizById($id);
    }
}
