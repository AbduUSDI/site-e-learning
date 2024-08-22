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
<style>
    body {
        background: #f8f9fa;
    }

    .container {
        margin-top: 20px;
    }

    .modal-content {
        border-radius: 8px;
    }

    .form-control {
        border-radius: 4px;
    }

    .btn {
        border-radius: 4px;
    }

    @media (max-width: 576px) {
        .modal-dialog {
            max-width: 100%;
            margin: 20px;
        }

        .modal-content {
            border-radius: 8px;
        }
    }
</style>
<div class="container">
    <h1 class="mt-5">Gestion de la Médiathèque</h1>
    <button id="addFormation" class="btn btn-primary mt-3 mb-3">Ajouter une Formation</button>
    <div id="formationsList">
        <!-- Liste des formations sera injectée ici -->
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
                        <p class="card-text">${page.content}</p>
                        ${page.video_url ? `<iframe src="${page.video_url}" frameborder="0" allowfullscreen></iframe>` : ''}
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
</script>

<?php include_once '../../../../public/templates/footer.php'; ?>
