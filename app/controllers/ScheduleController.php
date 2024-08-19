<?php
namespace App\Controllers;

use App\Models\Schedule;
use Exception;

class ScheduleController
{
    private $schedule;

    public function __construct($db)
    {
        $this->schedule = new Schedule($db);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function assignScheduleToTeacher($teacher_id, $schedule_content)
    {
        if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            $this->schedule->setTeacherId($teacher_id);
            $this->schedule->setSchedule($schedule_content);

            return $this->schedule->save();
        } else {
            throw new Exception('Utilisateur non connecté');
        }
    }

    public function getScheduleForTeacher($teacher_id)
    {
        return $this->schedule->getScheduleByTeacherId($teacher_id);
    }
}
