<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../../../auth/login.php');
    exit;
}

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

// Récupérer l'ID de la formation depuis l'URL
$formationId = isset($_GET['formation_id']) ? $_GET['formation_id'] : null;

if (!$formationId) {
    echo "Formation non spécifiée.";
    exit;
}

// Récupérer les détails de la formation
$formation = $formationController->getFormationById($formationId);
$categories = $categoryController->getCategoriesByFormation($formationId);

include_once '../../../../public/templates/header.php';
include_once '../navbar_student.php';
?>

<style>
    body {
        background: url('../../../../public/image_and_video/gif/anim_background2.gif');
        font-family: Arial, sans-serif;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .navbar {
        background-color: #343a40;
        padding: 10px 0;
    }

    .navbar a {
        color: #ffffff;
        text-decoration: none;
        font-weight: bold;
        margin: 0 15px;
    }

    .navbar a:hover {
        text-decoration: underline;
    }

    .container {
        margin-top: 50px;
    }

    h1 {
        text-align: center;
        margin-bottom: 40px;
        font-size: 2.5rem;
        font-weight: bold;
        color: white;
    }

    .card {
        margin-bottom: 20px;
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #343a40;
        color: #ffffff;
        padding: 10px 15px;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
        font-weight: bold;
    }

    .card-body {
        padding: 20px;
        background-color: #f8f9fa;
    }

    .list-group-item {
        background-color: #ffffff;
        border: 1px solid #ddd;
        margin-bottom: 10px;
        border-radius: 4px;
    }

    iframe {
        width: 100%;
        height: 315px;
        border: none;
        margin-top: 15px;
    }

    .filter-buttons {
        text-align: right;
        margin-bottom: 10px;
    }

    .filter-buttons button {
        margin-left: 10px;
    }

    .progress-status {
        margin-top: 10px;
        text-align: right;
        font-weight: bold;
    }
</style>

<div class="container mt-5">
    <h1 class="text-center mb-4"><?php echo htmlspecialchars_decode($formation['name']); ?></h1>
    <p class="text-center text-white"><?php echo htmlspecialchars_decode($formation['description']); ?></p>

    <?php if (empty($categories)): ?>
        <p class="text-center">Aucune catégorie disponible pour cette formation.</p>
    <?php else: ?>
        <div class="accordion" id="formationAccordion">
            <?php foreach ($categories as $category): ?>
                <?php 
                $subCategories = $subCategoryController->getSubCategoriesByCategory($category['id']); 
                $categoryProgress = $categoryController->getCategoryProgress($_SESSION['user']['id'], $category['id']);
                $status = $categoryProgress['status'] ?? 'not_started';
                ?>
                <div class="card">
                    <div class="card-header" id="heading<?php echo $category['id']; ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse<?php echo $category['id']; ?>" aria-expanded="true" aria-controls="collapse<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars_decode($category['title']); ?>
                                </button>
                            </h2>
                            <div class="filter-buttons">
                                <button class="btn btn-secondary" onclick="updateCategoryStatus(<?php echo $category['id']; ?>, 'not_started')">Non commencée</button>
                                <button class="btn btn-warning" onclick="updateCategoryStatus(<?php echo $category['id']; ?>, 'in_progress')">En cours</button>
                                <button class="btn btn-success" onclick="updateCategoryStatus(<?php echo $category['id']; ?>, 'completed')">Terminée</button>
                            </div>
                        </div>
                        <div class="progress-status">
                            Statut : <span id="status-<?php echo $category['id']; ?>"><?php echo ucfirst(str_replace('_', ' ', $status)); ?></span>
                        </div>
                    </div>

                    <div id="collapse<?php echo $category['id']; ?>" class="collapse" aria-labelledby="heading<?php echo $category['id']; ?>" data-parent="#formationAccordion">
                        <div class="card-body">
                            <?php if (empty($subCategories)): ?>
                                <p class="text-center">Aucune sous-catégorie disponible pour cette catégorie.</p>
                            <?php else: ?>
                                <?php foreach ($subCategories as $subCategory): ?>
                                    <h5><?php echo htmlspecialchars_decode($subCategory['title']); ?></h5>
                                    <?php $pages = $pageController->getPagesBySubCategory($subCategory['id']); ?>
                                    <?php if (empty($pages)): ?>
                                        <p class="text-center">Aucune page disponible pour cette sous-catégorie.</p>
                                    <?php else: ?>
                                        <div class="accordion" id="subCategoryAccordion<?php echo $subCategory['id']; ?>">
                                            <?php foreach ($pages as $page): ?>
                                                <div class="card">
                                                    <div class="card-header" id="headingPage<?php echo $page['id']; ?>">
                                                        <h2 class="mb-0">
                                                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapsePage<?php echo $page['id']; ?>" aria-expanded="true" aria-controls="collapsePage<?php echo $page['id']; ?>">
                                                                <?php echo htmlspecialchars_decode($page['title']); ?>
                                                            </button>
                                                        </h2>
                                                    </div>
                                                    <div id="collapsePage<?php echo $page['id']; ?>" class="collapse" aria-labelledby="headingPage<?php echo $page['id']; ?>" data-parent="#subCategoryAccordion<?php echo $subCategory['id']; ?>" data-id="<?php echo $page['id']; ?>">
                                                        <div class="card-body">
                                                            <p><?php echo htmlspecialchars_decode($page['content']); ?></p>
                                                            <?php if (!empty($page['video_url'])): ?>
                                                                <div class="flowplayer" data-splash="true"><video data-title="titre" controls="controls" wmode="transparent" type="video/mp4" src="<?php echo htmlspecialchars_decode($page['video_url']); ?>" height="200" width="100%"></video></div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function updateCategoryStatus(categoryId, status) {
        $.ajax({
            url: 'update_category_status.php',
            type: 'POST',
            data: {
                category_id: categoryId,
                status: status
            },
            success: function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    $('#status-' + categoryId).text(response.status);
                    alert('Statut mis à jour avec succès');
                } else {
                    alert('Erreur lors de la mise à jour du statut');
                }
            },
            error: function() {
                alert('Erreur lors de la requête AJAX');
            }
        });
    }

    $(document).ready(function() {
    $('.collapse').on('show.bs.collapse', function() {
        var pageId = $(this).attr('data-id');
        if (pageId) {
            $.ajax({
                url: 'update_view_count.php',
                type: 'POST',
                data: { id: pageId }, // Assurez-vous d'envoyer 'id' si c'est le nom de votre colonne
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        console.log('View count incremented for page ID: ' + pageId);
                    } else {
                        console.log('Failed to increment view count for page ID: ' + pageId);
                    }
                },
                error: function() {
                    console.log('Error during AJAX request');
                }
            });
        }
    });
});

</script>

<?php include '../../../../public/templates/footer.php'; ?>
