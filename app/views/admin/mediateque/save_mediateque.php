<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\FormationController;
use App\Controllers\CategoryController;
use App\Controllers\SubCategoryController;
use App\Controllers\PageController;

$database = new Database();
$db = $database->getConnection();

$formationController = new FormationController($db);
$categoryController = new CategoryController($db);
$subCategoryController = new SubCategoryController($db);
$pageController = new PageController($db);

header('Content-Type: application/json; charset=utf-8');  // Assurer l'encodage UTF-8 pour les réponses JSON

// Vérification de la méthode de requête
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    error_log("Token envoyé: " . $_POST['csrf_token']);
    error_log("Token en session: " . $_SESSION['csrf_token']);
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
        exit;
    }
}

// Déterminer l'action à effectuer
$action = $_POST['action'] ?? $_GET['action'] ?? null;

switch ($action) {
    case 'get_formations':
        $formations = $formationController->getAllFormations();
        echo json_encode($formations);
        break;

    case 'get_formation':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']);
            exit;
        }
        $formation = $formationController->getFormationById($id);
        if ($formation) {
            echo json_encode($formation);
        } else {
            http_response_code(404); 
            echo json_encode(['status' => 'error', 'message' => 'Formation not found.']);
        }
        break;

    case 'save_formation':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!$name || !$description) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
            exit;
        }

        if ($id) {
            $success = $formationController->updateFormation($id, $name, $description);
        } else {
            $success = $formationController->createFormation($name, $description);
        }

        if ($success) {
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to save formation.']);
        }
        break;

    case 'delete_formation':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $success = $formationController->deleteFormation($id);
            if ($success) {
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete formation.']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']);
        }
        break;

    case 'get_category':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']);
            exit;
        }
        $category = $categoryController->getCategoryById($id);
        if ($category) {
            echo json_encode($category);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Category not found.']);
        }
        break;

    case 'save_category':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $formation_id = filter_input(INPUT_POST, 'formation_id', FILTER_VALIDATE_INT);

        if (!$title || !$formation_id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
            exit;
        }

        if ($id) {
            $success = $categoryController->updateCategory($id, $title, $formation_id);
        } else {
            $success = $categoryController->createCategory($title, $formation_id);
        }

        if ($success) {
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to save category.']);
        }
        break;

    case 'delete_category':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $success = $categoryController->deleteCategory($id);
            if ($success) {
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete category.']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']);
        }
        break;

    case 'get_subcategory':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']);
            exit;
        }
        $subCategory = $subCategoryController->getSubCategoryById($id);
        if ($subCategory) {
            echo json_encode($subCategory);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'SubCategory not found.']);
        }
        break;

    case 'save_subcategory':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);

        if (!$title || !$category_id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
            exit;
        }

        if ($id) {
            $success = $subCategoryController->updateSubCategory($id, $title, $category_id);
        } else {
            $success = $subCategoryController->createSubCategory($title, $category_id);
        }

        if ($success) {
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to save subcategory.']);
        }
        break;

    case 'delete_subcategory':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $success = $subCategoryController->deleteSubCategory($id);
            if ($success) {
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete subcategory.']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']);
        }
        break;

    case 'get_page':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']);
            exit;
        }
        $page = $pageController->getPageById($id);
        if ($page) {
            echo json_encode($page);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Page not found.']);
        }
        break;

        case 'get_pages':
            $page = filter_input(INPUT_POST, 'page', FILTER_VALIDATE_INT) ?: 1;
            $limit = filter_input(INPUT_POST, 'limit', FILTER_VALIDATE_INT) ?: 5;
        
            $start = ($page - 1) * $limit;
        
            $pages = $pageController->getPagesWithPagination($start, $limit);
            $totalPages = $pageController->getTotalPages($limit);
        
            echo json_encode(['pages' => $pages, 'totalPages' => $totalPages]);
            break;
        

        case 'save_page':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $content = filter_input(INPUT_POST, 'content', FILTER_UNSAFE_RAW);
            $subcategory_id = filter_input(INPUT_POST, 'subcategory_id', FILTER_VALIDATE_INT);
            $video_url = filter_input(INPUT_POST, 'video_url', FILTER_VALIDATE_URL);
        
            // Récupérer l'URL actuelle de la vidéo si elle existe déjà
            $currentVideoUrl = null;
            if ($id) {
                $currentPage = $pageController->getPageById($id);
                if ($currentPage) {
                    $currentVideoUrl = $currentPage['video_url'];
                }
            }
        
            // Gestion de l'upload de fichier vidéo
            if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['video']['tmp_name'];
                $fileName = $_FILES['video']['name'];
                $uploadFileDir = '../../../../public/image_and_video/mp4/';
                $dest_path = $uploadFileDir . $fileName;
        
                if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                    echo json_encode(['status' => 'error', 'message' => 'File upload failed.']);
                    exit;
                }
                $video_url = $dest_path;  // Mettre à jour l'URL de la vidéo avec le nouveau chemin de fichier
            } elseif (empty($video_url)) {
                // Si l'utilisateur n'a pas fourni de nouvelle URL vidéo ou uploadé un nouveau fichier, conserver l'URL actuelle
                $video_url = $currentVideoUrl;
            }
        
            // Validation des données
            if (!$title || !$content || !$subcategory_id) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
                exit;
            }
        
            // Mise à jour ou création de la page
            if ($id) {
                $success = $pageController->updatePage($id, $title, $content, $video_url, $subcategory_id);
            } else {
                $success = $pageController->createPage($title, $content, $video_url, $subcategory_id);
            }
        
            // Réponse JSON
            if ($success) {
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to save page.']);
            }
            break;        

    case 'delete_page':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $success = $pageController->deletePage($id);
            if ($success) {
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete page.']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
        break;
}
