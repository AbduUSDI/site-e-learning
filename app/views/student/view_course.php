<?php
require_once '../../../vendor/autoload.php';

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

$formations = $formationController->getAllFormations();

include_once '../../../public/templates/header.php';
?>

<div class="container mt-5">
    <h1 class="text-center">Formations</h1>
    <?php foreach ($formations as $formation): ?>
        <div class="formation">
            <h2><?php echo htmlspecialchars($formation['name']); ?></h2>
            <p><?php echo htmlspecialchars($formation['description']); ?></p>

            <?php $categories = $categoryController->getCategoriesByFormation($formation['id']); ?>
            <?php foreach ($categories as $category): ?>
                <div class="category">
                    <h3><?php echo htmlspecialchars($category['title']); ?></h3>

                    <?php $subCategories = $subCategoryController->getSubCategoriesByCategory($category['id']); ?>
                    <?php foreach ($subCategories as $subCategory): ?>
                        <div class="subcategory">
                            <h4><?php echo htmlspecialchars($subCategory['title']); ?></h4>

                            <?php $pages = $pageController->getPagesBySubCategory($subCategory['id']); ?>
                            <?php foreach ($pages as $page): ?>
                                <div class="page">
                                    <p><?php echo htmlspecialchars($page['content']); ?></p>
                                    <?php if ($page['video_url']): ?>
                                        <video controls>
                                            <source src="<?php echo htmlspecialchars($page['video_url']); ?>" type="video/mp4">
                                            Votre navigateur ne supporte pas la lecture des vidéos.
                                        </video>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php include '../../../public/templates/footer.php'; ?>
