<?php

namespace App\Controllers;

use App\Models\Chapter;
use PDO;

class ChapterController
{
    private $db;
    private $chapterModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->chapterModel = new Chapter($db);
    }

    public function getAllChapters()
    {
        return $this->chapterModel->getAllChapters();
    }

    public function getChapterById($id)
    {
        return $this->chapterModel->getChapterById($id);
    }

    public function getChaptersByCourseId($course_id)
    {
        return $this->chapterModel->getChaptersByCourseId($course_id);
    }

    public function createChapter($data)
    {
        $this->chapterModel->setCourseId($data['course_id']);
        $this->chapterModel->setChapterName($data['chapter_name']);
        $this->chapterModel->setContent($data['content']);
        $result = $this->chapterModel->createChapter();
        
        if ($result) {
            return $this->chapterModel->getLastInsertId();
        }
        return false;
    }
    


    public function updateChapter($id, $data)
    {
        $this->chapterModel->setId($id);
        $this->chapterModel->setChapterName($data['chapter_name']);
        $this->chapterModel->setContent($data['content']);
        return $this->chapterModel->updateChapter();
    }

    public function deleteChapter($id)
    {
        $this->chapterModel->setId($id);
        return $this->chapterModel->deleteChapter();
    }
}
