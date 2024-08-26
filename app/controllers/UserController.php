<?php

namespace App\Controllers;

use App\Models\User;

class UserController
{
    private $user;

    public function __construct($db)
    {
        $this->user = new User($db);
    }

    // Méthode pour récupérer tous les utilisateurs
    public function getAllUsers()
    {
        return $this->user->getAllUsers();
    }
    // Méthode pour récupérer un utilisateur par ID
    public function getUserById($id)
    {
        return $this->user->findById($id);
    }
    public function getUserByUsername($username)
    {
        return $this->user->getUserByUsername($username);
    }
    // Méthode pour ajouter un utilisateur
    public function addUser($username, $email, $password, $role_id)
    {
        $this->user->setName($username);
        $this->user->setEmail($email);
        $this->user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        $this->user->setRoleId($role_id);

        return $this->user->addUser();
    }

    public function updateUser($id, $username, $email, $role_id, $password = null)
    {
        // Définir les propriétés de l'utilisateur
        $this->user->setId($id);
        $this->user->setName($username);
        $this->user->setEmail($email);
        $this->user->setRoleId($role_id);

        // Si un mot de passe est fourni, le hacher et le définir
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $this->user->setPassword($hashedPassword);
        }

        // Mettre à jour l'utilisateur
        return $this->user->updateUser();
    }
    // Méthode pour mettre à jour le profil utilisateur
    public function updateUserProfile($userId, $username, $email, $newPassword = null)
    {
    // Appel de la méthode du modèle pour mettre à jour le profil
    $updateSuccess = $this->user->updateUserProfile($userId, $username, $email, $newPassword);

        if ($updateSuccess) {
        // Si la mise à jour a réussi, mettez à jour la session utilisateur
        $_SESSION['user']['username'] = $username;
        $_SESSION['user']['email'] = $email;

        // Retourner un booléen indiquant que la mise à jour a réussi
        return true;
        } else {
        // Retourner un booléen indiquant que la mise à jour a échoué
        return false;
        }
    }


    // Méthode pour supprimer un utilisateur
    public function deleteUser($id)
    {
        return $this->user->deleteUser($id);
    }
    public function getUsersByRole($role_id)
    {
        return $this->user->getUsersByRole($role_id);
    }
    public function validateStudent($studentId, $validationStatus)
    {
        return $this->user->validateCursus($studentId, $validationStatus);
    }
    public function getCursusValidationStats() {
        return $this->user->getCursusValidationStats();
    }
}
