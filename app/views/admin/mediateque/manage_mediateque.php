<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérification que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../../login.php');
    exit;
}

// Gestion de la durée de la session
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: ../../../auth/login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

require_once '../../../../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\FormationController;
use App\Controllers\CategoryController;
use App\Controllers\SubCategoryController;
use App\Controllers\PageController;

// Génération d'un token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$database = new Database();
$db = $database->getConnection();

$formationController = new FormationController($db);
$categoryController = new CategoryController($db);
$subCategoryController = new SubCategoryController($db);
$pageController = new PageController($db);

$formations = $formationController->getAllFormations();

header('Content-Type: text/html; charset=utf-8'); 

include_once '../../../../public/templates/header.php';
include_once '../navbar_admin.php';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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

    .table-responsive {
        margin-bottom: 50px;
    }

    .table {
        background-color: #ffffff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .table th {
        background-color: #343a40;
        color: #ffffff;
        padding: 15px;
        font-weight: bold;
        text-align: center;
    }

    .table td {
        padding: 15px;
        text-align: center;
        vertical-align: middle;
    }

    .btn {
        font-size: 14px;
        padding: 10px 20px;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #218838;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #5a6268;
    }

    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
    }

    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
    }

    .modal-content {
        border-radius: 8px;
    }

    .form-control {
        border-radius: 4px;
    }

    .form-group label {
        font-weight: 600;
    }

    footer {
        background-color: #343a40;
        color: white;
        padding: 20px 0;
        text-align: center;
        margin-top: 50px;
    }

    footer a {
        color: #adb5bd;
        text-decoration: none;
    }

    footer a:hover {
        text-decoration: underline;
    }
    .hero {
        background: url('../../../../public/image_and_video/webp/background_image_index.webp') no-repeat center center;
        background-size: cover;
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        border-radius: 10px;
    }

    .hero h1 {
        font-size: 3.5rem;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .hero p {
        font-size: 1.25rem;
    }
    .navbar-toggler {
    background-color: #fff;
    border: none;
    outline: none;
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba%280, 0, 0, 0.5%29' stroke-width='2' linecap='round' linejoin='round' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
    }

    .navbar-toggler:focus {
        outline: none;
    }
    .navbar-toggler-icon {
        width: 25px;
        height: 25px;
    }
    #scrollToTopBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 99;
        font-size: 18px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 50%;
        padding: 10px 15px;
        cursor: pointer;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    #scrollToTopBtn:hover {
        background-color: #333;
    }
    #scrollToBottomBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 80px;
        z-index: 99;
        font-size: 18px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 50%;
        padding: 10px 15px;
        cursor: pointer;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    #scrollToBottomBtn:hover {
        background-color: #333;
    }
</style>
<div class="container">
    <h1 class="mt-5">Gestion de la Médiathèque</h1>
    <button id="addFormation" class="btn btn-primary mt-3 mb-3">Ajouter une Formation</button>
    <div id="formationsList">
        <!-- Liste des formations sera généré ici -->
    </div>
</div>

<!-- Modals -->
<!-- Modal Formation -->
<div id="formationModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Formation</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formationForm">
                    <input type="hidden" id="formationId" name="id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="form-group">
                        <label for="formationName">Nom</label>
                        <input type="text" id="formationName" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="formationDescription">Description</label>
                        <textarea id="formationDescription" name="description" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Catégorie -->
<div id="categoryModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Catégorie</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <input type="hidden" id="categoryId" name="id">
                    <input type="hidden" id="formationIdForCategory" name="formation_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="form-group">
                        <label for="categoryTitle">Titre</label>
                        <input type="text" id="categoryTitle" name="title" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sous-Catégorie -->
<div id="subCategoryModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sous-Catégorie</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="subCategoryForm">
                    <input type="hidden" id="subCategoryId" name="id">
                    <input type="hidden" id="categoryIdForSubCategory" name="category_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="form-group">
                        <label for="subCategoryTitle">Titre</label>
                        <input type="text" id="subCategoryTitle" name="title" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Page -->
<div id="pageModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Page</h5>
                <button type="button" class="close" data-dismiss="modal">&times;"></button>
            </div>
            <div class="modal-body">
                <form id="pageForm" enctype="multipart/form-data">
                    <input type="hidden" id="pageId" name="id">
                    <input type="hidden" id="subCategoryIdForPage" name="subcategory_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="form-group">
                        <label for="pageTitle">Titre</label>
                        <input type="text" id="pageTitle" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="pageContent">Contenu</label>
                        <textarea id="pageContent" name="content" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="videoFile">Ajouter un fichier vidéo</label>
                        <input type="file" id="videoFile" name="video" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="videoUrl">ou URL de la vidéo</label>
                        <input type="text" id="videoUrl" name="video_url" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>
<button onclick="topFunction()" id="scrollToTopBtn" title="Retour en haut">
    <i class="fas fa-arrow-up"></i>
</button>
<button onclick="bottomFunction()" id="scrollToBottomBtn" title="Aller en bas">
    <i class="fas fa-arrow-down"></i>
</button>

<!-- Inclusion de jQuery (version complète, pas la version 'slim' qui ne supporte pas AJAX) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function () {
    loadFormations();

    // Charger la liste des formations et leurs catégories, sous-catégories et pages
    function loadFormations() {
    $.ajax({
        url: 'save_mediateque.php',
        method: 'POST',
        data: { action: 'get_formations', csrf_token: '<?php echo $_SESSION['csrf_token']; ?>' },
        success: function (data) {
            $('#formationsList').empty();
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(function (formation) {
                    var formationHtml = `
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">${formation.name}</h5>
                                <p class="card-text">${formation.description}</p>
                                <button class="btn btn-secondary editFormation" data-id="${formation.id}">Modifier</button>
                                <button class="btn btn-danger deleteFormation" data-id="${formation.id}">Supprimer</button>
                                <button class="btn btn-primary addCategory" data-formation-id="${formation.id}">Ajouter une Catégorie</button>
                                <div class="mt-3" id="categoriesList-${formation.id}">
                                    ${loadCategories(formation.categories)}
                                </div>
                            </div>
                        </div>
                    `;
                    $('#formationsList').append(formationHtml);
                });
            } else {
                $('#formationsList').html('<p>Aucune formation disponible.</p>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Erreur lors du chargement des formations:', error);
            alert('Une erreur est survenue lors du chargement des formations.');
        }
    });
}

function loadCategories(categories) {
    var categoriesHtml = '';
    if (Array.isArray(categories) && categories.length > 0) {
        categories.forEach(function (category) {
            categoriesHtml += `
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">${category.title}</h6>
                        <button class="btn btn-secondary editCategory" data-id="${category.id}">Modifier</button>
                        <button class="btn btn-danger deleteCategory" data-id="${category.id}">Supprimer</button>
                        <button class="btn btn-primary addSubCategory" data-category-id="${category.id}">Ajouter une Sous-Catégorie</button>
                        <div class="mt-3" id="subCategoriesList-${category.id}">
                            ${loadSubCategories(category.subcategories)}
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        categoriesHtml = '<p>Aucune catégorie disponible.</p>';
    }
    return categoriesHtml;
}

function loadSubCategories(subcategories) {
    var subCategoriesHtml = '';
    if (Array.isArray(subcategories) && subcategories.length > 0) {
        subcategories.forEach(function (subcategory) {
            subCategoriesHtml += `
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">${subcategory.title}</h6>
                        <button class="btn btn-secondary editSubCategory" data-id="${subcategory.id}">Modifier</button>
                        <button class="btn btn-danger deleteSubCategory" data-id="${subcategory.id}">Supprimer</button>
                        <button class="btn btn-primary addPage" data-subcategory-id="${subcategory.id}">Ajouter une Page</button>
                        <div class="mt-3" id="pagesList-${subcategory.id}">
                            ${loadPages(subcategory.pages)}
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        subCategoriesHtml = '<p>Aucune sous-catégorie disponible.</p>';
    }
    return subCategoriesHtml;
}

function loadPages(pages) {
    var pagesHtml = '';
    if (Array.isArray(pages) && pages.length > 0) {
        pages.forEach(function (page) {
            pagesHtml += `
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">${page.title}</h6>
                        <p class="card-text" name="page-description">${page.content}</p>
                        ${page.video_url ? `<video controls style="width: 100%;"><source src="${page.video_url}" type="video/mp4">Your browser does not support the video tag.</video>` : ''}
                        <button class="btn btn-secondary editPage" data-id="${page.id}">Modifier</button>
                        <button class="btn btn-danger deletePage" data-id="${page.id}">Supprimer</button>
                    </div>
                </div>
            `;
        });
    } else {
        pagesHtml = '<p>Aucune page disponible.</p>';
    }
    return pagesHtml;
}

    // Ajouter une formation
    $('#addFormation').click(function () {
        $('#formationForm')[0].reset();
        $('#formationId').val('');
        $('#formationModal').modal('show');
    });

    // Soumettre le formulaire de formation
    $('#formationForm').submit(function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        formData += '&action=save_formation';
        $.ajax({
            url: 'save_mediateque.php',
            method: 'POST',
            data: formData,
            success: function (response) {
                if (response.status === 'success') {
                    loadFormations();
                    $('#formationModal').modal('hide');
                } else {
                    alert('Erreur lors de l\'ajout de la formation.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors de l\'enregistrement de la formation:', error);
                alert('Une erreur est survenue lors de l\'enregistrement de la formation.');
            }
        });
    });

    $(document).on('click', '.editFormation', function () {
    var id = $(this).data('id');
    if (!id) {
        console.error('Invalid ID:', id);
        return;
    }
    $.ajax({
        url: 'save_mediateque.php',
        method: 'POST',
        data: { action: 'get_formation', id: id, csrf_token: '<?php echo $_SESSION['csrf_token']; ?>' },
        success: function (response) {
            if (response.status === 'error') {
                console.error('Erreur: ', response.message);
                alert('Erreur lors de la récupération des données de la formation.');
            } else {
                var formation = response;

                // Décodez les entités HTML avant de remplir le formulaire
                $('#formationId').val(formation.id);
                $('#formationName').val(htmlspecialchars_decode(formation.name));
                $('#formationDescription').val(htmlspecialchars_decode(formation.description));

                $('#formationModal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            console.error('Erreur lors de la récupération des données de la formation:', error, xhr.responseText);
            alert('Une erreur est survenue lors de la récupération des données de la formation.');
        }
    });
});

function htmlspecialchars_decode(str) {
    return $('<textarea />').html(str).text();
}



    // Supprimer une formation
    $(document).on('click', '.deleteFormation', function () {
        var id = $(this).data('id');
        if (confirm('Êtes-vous sûr de vouloir supprimer cette formation ?')) {
            $.ajax({
                url: 'save_mediateque.php',
                method: 'POST',
                data: { action: 'delete_formation', id: id, csrf_token: '<?php echo $_SESSION['csrf_token']; ?>' },
                success: function (response) {
                    loadFormations();
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors de la suppression de la formation:', error);
                    alert('Une erreur est survenue lors de la suppression de la formation.');
                }
            });
        }
    });

    // Ajouter une catégorie
    $(document).on('click', '.addCategory', function () {
        var formationId = $(this).data('formation-id');
        $('#categoryForm')[0].reset();
        $('#formationIdForCategory').val(formationId);
        $('#categoryModal').modal('show');
    });

    // Soumettre le formulaire de catégorie
    $('#categoryForm').submit(function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        formData += '&action=save_category';
        $.ajax({
            url: 'save_mediateque.php',
            method: 'POST',
            data: formData,
            success: function (response) {
                if (response.status === 'success') {
                    loadFormations();
                    $('#categoryModal').modal('hide');
                } else {
                    alert('Erreur lors de l\'enregistrement de la catégorie.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors de l\'enregistrement de la catégorie:', error);
                alert('Une erreur est survenue lors de l\'enregistrement de la catégorie.');
            }
        });
    });

     // Modifier une catégorie
    $(document).on('click', '.editCategory', function () {
    var id = $(this).data('id');
    $.ajax({
        url: 'save_mediateque.php',
        method: 'POST',
        data: { action: 'get_category', id: id, csrf_token: '<?php echo $_SESSION['csrf_token']; ?>' },
        success: function (data) {
            if (data.status === 'error') {
                console.error('Erreur: ', data.message);
                alert('Erreur lors de la récupération des données de la catégorie.');
            } else {
                // Les données sont reçues sous forme d'objet
                var category = data;

                // Remplir le formulaire du modal avec les données de la catégorie
                $('#categoryId').val(category.id);
                $('#categoryTitle').val(htmlspecialchars_decode(category.title));
                $('#formationIdForCategory').val(category.formation_id);

                // Ouvrir le modal
                $('#categoryModal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            console.error('Erreur lors de la récupération des données de la catégorie:', error, xhr.responseText);
            alert('Une erreur est survenue lors de la récupération des données de la catégorie.');
        }
    });
});



    // Supprimer une catégorie
    $(document).on('click', '.deleteCategory', function () {
        var id = $(this).data('id');
        if (confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')) {
            $.ajax({
                url: 'save_mediateque.php',
                method: 'POST',
                data: { action: 'delete_category', id: id, csrf_token: '<?php echo $_SESSION['csrf_token']; ?>' },
                success: function (response) {
                    loadFormations();
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors de la suppression de la catégorie:', error);
                    alert('Une erreur est survenue lors de la suppression de la catégorie.');
                }
            });
        }
    });

    // Ajouter une sous-catégorie
    $(document).on('click', '.addSubCategory', function () {
        var categoryId = $(this).data('category-id');
        $('#subCategoryForm')[0].reset();
        $('#categoryIdForSubCategory').val(categoryId);
        $('#subCategoryModal').modal('show');
    });

    // Soumettre le formulaire de sous-catégorie
    $('#subCategoryForm').submit(function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        formData += '&action=save_subcategory';
        $.ajax({
            url: 'save_mediateque.php',
            method: 'POST',
            data: formData,
            success: function (response) {
                if (response.status === 'success') {
                    loadFormations();
                    $('#subCategoryModal').modal('hide');
                } else {
                    alert('Erreur lors de l\'enregistrement de la sous-catégorie.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors de l\'enregistrement de la sous-catégorie:', error);
                alert('Une erreur est survenue lors de l\'enregistrement de la sous-catégorie.');
            }
        });
    });

// Modifier une sous-catégorie
$(document).on('click', '.editSubCategory', function () {
    var id = $(this).data('id');
    $.ajax({
        url: 'save_mediateque.php',
        method: 'POST',
        data: { action: 'get_subcategory', id: id, csrf_token: '<?php echo $_SESSION['csrf_token']; ?>' },
        success: function (response) {
            if (response.status === 'error') {
                console.error('Erreur: ', response.message);
                alert('Erreur lors de la récupération des données de la sous-catégorie.');
            } else {

                var subcategory = response;

                $('#subCategoryId').val(subcategory.id);
                $('#subCategoryTitle').val(htmlspecialchars_decode(subcategory.title));
                $('#categoryIdForSubCategory').val(subcategory.category_id);

                // Ouvrir le modal
                $('#subCategoryModal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            console.error('Erreur lors de la récupération des données de la sous-catégorie:', error, xhr.responseText);
            alert('Une erreur est survenue lors de la récupération des données de la sous-catégorie.');
        }
    });
});



    // Supprimer une sous-catégorie
    $(document).on('click', '.deleteSubCategory', function () {
        var id = $(this).data('id');
        if (confirm('Êtes-vous sûr de vouloir supprimer cette sous-catégorie ?')) {
            $.ajax({
                url: 'save_mediateque.php',
                method: 'POST',
                data: { action: 'delete_subcategory', id: id, csrf_token: '<?php echo $_SESSION['csrf_token']; ?>' },
                success: function (response) {
                    loadFormations();
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors de la suppression de la sous-catégorie:', error);
                    alert('Une erreur est survenue lors de la suppression de la sous-catégorie.');
                }
            });
        }
    });

    // Ajouter une page
    $(document).on('click', '.addPage', function () {
        var subCategoryId = $(this).data('subcategory-id');
        $('#pageForm')[0].reset();
        $('#subCategoryIdForPage').val(subCategoryId);
        $('#pageModal').modal('show');
    });

    // Soumettre le formulaire de page
    $('#pageForm').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'save_page');
        formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
        $.ajax({
            url: 'save_mediateque.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status === 'success') {
                    loadFormations();
                    $('#pageModal').modal('hide');
                    location.reload();
                } else {
                    alert('Erreur lors de l\'enregistrement de la page.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors de l\'enregistrement de la page:', error);
                alert('Une erreur est survenue lors de l\'enregistrement de la page.');
            }
        });
    });

    // Modifier une page
$(document).on('click', '.editPage', function () {
    var id = $(this).data('id');
    $.ajax({
        url: 'save_mediateque.php',
        method: 'POST',
        data: { action: 'get_page', id: id, csrf_token: '<?php echo $_SESSION['csrf_token']; ?>' },
        success: function (response) {
            if (response.status === 'error') {
                console.error('Erreur: ', response.message);
                alert('Erreur lors de la récupération des données de la page.');
            } else {
                var page = response;

                $('#pageId').val(page.id);
                $('#pageTitle').val(htmlspecialchars_decode(page.title));
                $('#pageContent').val(htmlspecialchars_decode(page.content));
                $('#subCategoryIdForPage').val(page.subcategory_id);

                if (page.video_url) {
                    $('#videoUrl').val(page.video_url);
                } else {
                    $('#videoUrl').val('');
                }
                // Vider le champ du fichier vidéo pour ne pas réutiliser le dernier fichier sélectionné
                $('#videoFile').val('');

                $('#pageModal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            console.error('Erreur lors de la récupération des données de la page:', error, xhr.responseText);
            alert('Une erreur est survenue lors de la récupération des données de la page.');
        }
    });
});


    // Supprimer une page
    $(document).on('click', '.deletePage', function () {
        var id = $(this).data('id');
        if (confirm('Êtes-vous sûr de vouloir supprimer cette page ?')) {
            $.ajax({
                url: 'save_mediateque.php',
                method: 'POST',
                data: { action: 'delete_page', id: id, csrf_token: '<?php echo $_SESSION['csrf_token']; ?>' },
                success: function (response) {
                    loadFormations();
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors de la suppression de la page:', error);
                    alert('Une erreur est survenue lors de la suppression de la page.');
                }
            });
        }
    });
});
window.onscroll = function() {scrollFunction()};

    function scrollFunction() {
        // Montrer le bouton "Retour en haut" si l'utilisateur a défilé plus de 20px
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("scrollToTopBtn").style.display = "block";
        } else {
            document.getElementById("scrollToTopBtn").style.display = "none";
        }

        // Montrer le bouton "Aller en bas" si l'utilisateur n'est pas encore en bas
        if ((window.innerHeight + window.scrollY) < document.body.offsetHeight) {
            document.getElementById("scrollToBottomBtn").style.display = "block";
        } else {
            document.getElementById("scrollToBottomBtn").style.display = "none";
        }
    }

    function topFunction() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    function bottomFunction() {
        window.scrollTo({
            top: document.body.scrollHeight, // Aller à la hauteur totale de la page
            behavior: 'smooth'
        });
    }

</script>

<?php include_once '../../../../public/templates/footer.php'; ?>
