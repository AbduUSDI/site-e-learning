<?php
namespace App\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;

class QuizController
{
    private $quiz;
    private $question;
    private $answer;

    public function __construct($db)
    {
        $this->quiz = new Quiz($db);
        $this->question = new Question($db);
        $this->answer = new Answer($db);
    }

    // Méthodes pour les Quiz
    public function getAllQuizzes()
    {
        return $this->quiz->getAllQuizzes();
    }

    public function createQuizWithQuestions($quizName, $description, $courseId, $questions)
{
    // Créer le quiz
    $this->quiz->setQuizName($quizName);
    $this->quiz->setDescription($description);
    $this->quiz->setCourseId($courseId);
    $this->quiz->createQuiz();

    // Récupérer l'ID du quiz nouvellement créé
    $quizId = $this->quiz->getLastInsertId();

    foreach ($questions as $question) {
        // Créer chaque question associée au quiz
        $this->question->setQuizId($quizId);
        $this->question->setQuestionText($question['question_text']);
        $this->question->create();

        // Récupérer l'ID de la question nouvellement créée
        $questionId = $this->question->getLastInsertId();

        // Créer les réponses associées à la question
        foreach ($question['answers'] as $answer) {
            $this->answer->setQuestionId($questionId);
            $this->answer->setAnswerText($answer['answer_text']);
            // Utilisation de isset pour vérifier si is_correct est défini, sinon par défaut à 0
            $isCorrect = isset($answer['is_correct']) ? 1 : 0;
            $this->answer->setIsCorrect($isCorrect);
            $this->answer->create();
        }
    }
}

    // Méthode pour mettre à jour un quiz avec ses questions et réponses
    public function updateQuizWithQuestions($id, $quizName, $description, $courseId, $questions)
    {
        // Mettre à jour les informations du quiz
        $this->quiz->setId($id);
        $this->quiz->setQuizName($quizName);
        $this->quiz->setDescription($description);
        $this->quiz->setCourseId($courseId);
        $this->quiz->updateQuiz();

        foreach ($questions as $questionData) {
            // Si une question a un ID, elle existe déjà, on la met à jour
            if (isset($questionData['id'])) {
                $this->question->setId($questionData['id']);
                $this->question->setQuestionText($questionData['question_text']);
                $this->question->update();
                $questionId = $questionData['id'];
            } else {
                // Sinon, c'est une nouvelle question, on la crée
                $this->question->setQuizId($id);
                $this->question->setQuestionText($questionData['question_text']);
                $this->question->create();
                $questionId = $this->question->getLastInsertId();
            }

            // Maintenant, on gère les réponses pour cette question
            foreach ($questionData['answers'] as $answerData) {
                if (isset($answerData['id'])) {
                    // Mettre à jour la réponse existante
                    $this->answer->setId($answerData['id']);
                    $this->answer->setAnswerText($answerData['answer_text']);
                    $this->answer->setIsCorrect(isset($answerData['is_correct']) ? 1 : 0);
                    $this->answer->update();
                } else {
                    // Ajouter une nouvelle réponse
                    $this->answer->setQuestionId($questionId);
                    $this->answer->setAnswerText($answerData['answer_text']);
                    $this->answer->setIsCorrect(isset($answerData['is_correct']) ? 1 : 0);
                    $this->answer->create();
                }
            }

            // Gérer les réponses supprimées
            $existingAnswerIds = array_column($questionData['answers'], 'id');
            $this->answer->deleteMissingAnswers($questionId, $existingAnswerIds);
        }

        // Gérer les questions supprimées
        $existingQuestionIds = array_column($questions, 'id');
        $this->question->deleteMissingQuestions($id, $existingQuestionIds);
    }
    public function deleteQuiz($id)
    {
        $this->quiz->setId($id);
        return $this->quiz->deleteQuiz();
    }

    // Méthodes pour les Questions
    public function createQuestion($quiz_id, $question_text)
    {
        $this->question->setQuizId($quiz_id);
        $this->question->setQuestionText($question_text);
        return $this->question->create();
    }

    public function updateQuestion($id, $question_text)
    {
        $this->question->setId($id);
        $this->question->setQuestionText($question_text);
        return $this->question->update();
    }

    public function deleteQuestion($id)
    {
        $this->question->setId($id);
        return $this->question->delete();
    }

    public function getQuestionsByQuiz($quiz_id)
    {
        return $this->question->getByQuiz($quiz_id);
    }

    // Méthodes pour les Réponses
    public function createAnswer($question_id, $answer_text, $is_correct)
    {
        $this->answer->setQuestionId($question_id);
        $this->answer->setAnswerText($answer_text);
        $this->answer->setIsCorrect($is_correct);
        return $this->answer->create();
    }

    public function updateAnswer($id, $answer_text, $is_correct)
    {
        $this->answer->setId($id);
        $this->answer->setAnswerText($answer_text);
        $this->answer->setIsCorrect($is_correct);
        return $this->answer->update();
    }

    public function deleteAnswer($id)
    {
        $this->answer->setId($id);
        return $this->answer->delete();
    }

    public function getAnswersByQuestion($question_id)
    {
        return $this->answer->getByQuestion($question_id);
    }
    public function getAllCourses()
    {
        return $this->quiz->getAllCourses();
    }
}
