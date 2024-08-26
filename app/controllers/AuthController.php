<?php

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    private $user;

    public function __construct($db)
    {
        $this->user = new User($db);
    }

    public function login()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
            $email = $_POST['email'];
            $password = $_POST['mot_de_passe'];

            $userData = $this->user->getUserByEmail($email);

            if ($userData && password_verify($password, $userData['password'])) {
                $_SESSION['user'] = $userData;
                switch ($userData['role_id']) {
                    case 1:
                        header('Location: ../views/admin/admin_dashboard.php');
                        break;
                    case 2:
                        header('Location: ../views/teacher/teacher_dashboard.php');
                        break;
                    case 3:
                        header('Location: ../views/student/student_dashboard.php');
                        break;
                    default:
                        header('Location: login.php');
                        break;
                }
                exit;
            } else {
                $error = "Email ou mot de passe incorrect.";
                return $error;
            }
        }
    }
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['nom_utilisateur'] ?? null;
            $email = $_POST['email'] ?? null;
            $password = $_POST['mot_de_passe'] ?? null;
            $role = $_POST['role_id'] ?? null;
            // Vérifier si l'email existe déjà
            if ($this->user->getUserByEmail($email)) {
                return "L'email est déjà utilisé.";
            } else {
                // Utiliser les setters pour définir les propriétés
                $this->user->setName($username);
                $this->user->setEmail($email);
                $this->user->setPassword(password_hash($password, PASSWORD_BCRYPT));
                $this->user->setRoleId($role);
    
                // Ajouter l'utilisateur à la base de données
                $result = $this->user->addUser();
    
                if ($result) {
                    $success = "Inscription réussie. Vous pouvez maintenant vous connecter.";
                    header("Location: login.php?success=" . urlencode($success));
                    exit();
                } else {
                    return "Erreur lors de l'inscription. Veuillez réessayer.";
                }
            }
        }
        return null;
    }
    public function logout()
    {
        // Démarre la session si ce n'est pas déjà fait
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Détruire toutes les variables de session
        $_SESSION = [];

        // Supprime le cookie de session si nécessaire
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Détruire la session
        session_destroy();

        // Rediriger l'utilisateur vers la page de connexion
        header("Location: login.php");
        exit();
    }
    public function logoutAdmin()
    {
        // Démarre la session si ce n'est pas déjà fait
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Détruire toutes les variables de session
        $_SESSION = [];

        // Supprime le cookie de session si nécessaire
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Détruire la session
        session_destroy();

        // Rediriger l'utilisateur vers la page de connexion
        header("Location: ../../auth/login.php");
        exit();
    }
    public function logoutInFolder()
    {
        // Démarre la session si ce n'est pas déjà fait
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Détruire toutes les variables de session
        $_SESSION = [];

        // Supprime le cookie de session si nécessaire
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Détruire la session
        session_destroy();

        // Rediriger l'utilisateur vers la page de connexion
        header("Location: ../../../../app/auth/login.php");
        exit();
    }
}
