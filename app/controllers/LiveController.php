<?php

namespace App\Controllers;

use App\Models\Live;

class LiveController
{
    private $liveModel;

    public function __construct($db)
    {
        $this->liveModel = new Live($db);
    }
    public function createLive($title, $description, $date, $link, $utilisateur_id)
    {
        return $this->liveModel->createLive($title, $description, $date, $link, $utilisateur_id);
    }

    public function updateLive($id, $title, $description, $date, $link, $utilisateur_id)
    {
        return $this->liveModel->updateLive($id, $title, $description, $date, $link, $utilisateur_id);
    }
    public function deleteLive($id)
    {
        return $this->liveModel->deleteLive($id);
    }

    public function getAllLives()
    {
        return $this->liveModel->getAllLives();
    }
    public function getAllLivesStudent() {
        return $this->liveModel->getAllLivesStudent();
    }
}
