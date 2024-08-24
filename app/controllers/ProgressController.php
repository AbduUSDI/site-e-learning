<?php
namespace App\Controllers;

use App\Models\Progress;
use PDO;

class ProgressController {
    private $progress;

    public function __construct(PDO $db) {
        $this->progress = new Progress($db);
    }

    public function markPageAsComplete($userId, $formationId, $pageId) {
        return $this->progress->markPageAsComplete($userId, $formationId, $pageId);
    }

    public function getStudentProgress($userId, $formationId) {
        return $this->progress->getCompletedPagesCount($userId, $formationId);
    }
}
