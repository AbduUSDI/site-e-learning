<?php
session_start();

require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\CategoryController;

$database = new Database();
$db = $database->getConnection();

$categoryController = new CategoryController($db);

$categoryId = isset($_POST['category_id']) ? $_POST['category_id'] : null;
$status = isset($_POST['status']) ? $_POST['status'] : null;

if ($categoryId && $status && in_array($status, ['not_started', 'in_progress', 'completed'])) {
    if ($categoryController->updateCategoryProgress($_SESSION['user']['id'], $categoryId, $status)) {
        echo json_encode(['success' => true, 'status' => ucfirst(str_replace('_', ' ', $status))]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}