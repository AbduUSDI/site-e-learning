<?php

require_once '../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\AuthController;

$database = new Database();
$db = $database->getConnection();

$authController = new AuthController($db);
$error = $authController->register();

if ($error) {
    // Rediriger vers la page de connexion avec un message d'erreur
    header("Location: login.php?error=" . urlencode($error));
    exit();
} else {
    // Rediriger vers la page de connexion avec un message de succès
    $success = "Inscription réussie. Vous pouvez maintenant vous connecter.";
    header("Location: login.php?success=" . urlencode($success));
    exit();
}
