<?php
namespace App\Controllers;

use App\Models\Test;

class TestController
{
    private $testModel;

    public function __construct($db)
    {
        $this->testModel = new Test($db);
    }

    public function getAllTests()
    {
        return $this->testModel->getAllTests();
    }

    public function getTestById($id)
    {
        return $this->testModel->getTestById($id);
    }

    public function createTest($data)
    {
        $this->testModel->setChapterId($data['chapter_id']);
        $this->testModel->setOrder($data['order']);
        $this->testModel->setQuestion($data['question']);
        $this->testModel->setAnswer($data['answer']);
        $this->testModel->setIsCorrect($data['is_correct']);
        return $this->testModel->createTest();
    }

    public function updateTest($id, $data)
    {
        $this->testModel->setId($id);
        $this->testModel->setQuestion($data['question']);
        $this->testModel->setAnswer($data['answer']);
        $this->testModel->setIsCorrect($data['is_correct']);
        $this->testModel->setOrder($data['order']);
        return $this->testModel->updateTest();
    }

    public function deleteTest($id)
    {
        $this->testModel->setId($id);
        return $this->testModel->deleteTest();
    }
}
