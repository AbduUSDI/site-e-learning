<?php

namespace App\Controllers;

use App\Models\Profile;

class ProfileController
{
    private $profileModel;

    public function __construct($db)
    {
        $this->profileModel = new Profile($db);
    }

    public function getProfileByUserId($userId)
    {
        return $this->profileModel->getProfileByUserId($userId);
    }

    public function saveProfile($userId, $firstName, $lastName, $birthDate, $biography, $imageName = null)
    {
        return $this->profileModel->saveProfile($userId, $firstName, $lastName, $birthDate, $biography, $imageName);
    }

    public function updateProfilePicture($userId, $imageName)
    {
        return $this->profileModel->updateProfilePicture($userId, $imageName);
    }
}
