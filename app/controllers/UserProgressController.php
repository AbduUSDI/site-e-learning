<?php
namespace App\Controllers;

use App\Models\UserProgress;

class UserProgressController
{
    private $userProgressModel;

    public function __construct($db)
    {
        $this->userProgressModel = new UserProgress($db);
    }

    public function getUserProgressByUserId($user_id)
    {
        return $this->userProgressModel->getUserProgressByUserId($user_id);
    }

    public function createUserProgress($data)
    {
        $this->userProgressModel->setUserId($data['user_id']);
        $this->userProgressModel->setChapterId($data['chapter_id']);
        $this->userProgressModel->setCourseId($data['course_id']);
        $this->userProgressModel->setFormationId($data['formation_id']);
        $this->userProgressModel->setProgress($data['progress']);
        $this->userProgressModel->setQuizScore($data['quiz_score']);
        $this->userProgressModel->setTestScore($data['test_score']);
        $this->userProgressModel->setCompleted($data['completed']);
        return $this->userProgressModel->createUserProgress();
    }

    public function updateUserProgress($id, $data)
    {
        $this->userProgressModel->setId($id);
        $this->userProgressModel->setProgress($data['progress']);
        $this->userProgressModel->setQuizScore($data['quiz_score']);
        $this->userProgressModel->setTestScore($data['test_score']);
        $this->userProgressModel->setCompleted($data['completed']);
        return $this->userProgressModel->updateUserProgress();
    }

    public function deleteUserProgress($id)
    {
        $this->userProgressModel->setId($id);
        return $this->userProgressModel->deleteUserProgress();
    }
}
