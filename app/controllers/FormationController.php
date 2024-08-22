<?php

namespace App\Controllers;

use App\Models\Formation;
use PDO;

class FormationController
{
    private $db;
    private $formationModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->formationModel = new Formation($db);
    }

    public function getAllFormations()
    {
        return $this->formationModel->getAllFormations();
    }

    public function getFormationById($id)
    {
        return $this->formationModel->getFormationById($id);
    }

    public function createFormation($name, $description) {
        return $this->formationModel->createFormation($name, $description);
    }

    public function updateFormation($id, $name, $description) {
        return $this->formationModel->updateFormation($id, $name, $description);
    }


    public function deleteFormation($id)
    {
        $this->formationModel->setId($id);
        return $this->formationModel->deleteFormation();
    }
}
