<?php

namespace App\Controllers;

use App\Models\Category;

class CategoryController {

    private $categoryModel;

    public function __construct($db) {
        $this->categoryModel = new Category($db);
    }

    public function getCategoriesByFormation($formation_id) {
        return $this->categoryModel->getCategoriesByFormation($formation_id);
    }

    public function getCategoryById($id) {
        return $this->categoryModel->getCategoryById($id);
    }

    public function createCategory($title, $formation_id) {
        return $this->categoryModel->createCategory($title, $formation_id);
    }

    public function updateCategory($id, $title, $formation_id) {
        return $this->categoryModel->updateCategory($id, $title, $formation_id);
    }

    public function deleteCategory($id)
    {
        error_log("Tentative de suppression de la catégorie avec ID: " . $id);
        $result = $this->categoryModel->deleteCategory($id);
        if ($result) {
            error_log("Suppression réussie pour la catégorie avec ID: " . $id);
        } else {
            error_log("Échec de la suppression pour la catégorie avec ID: " . $id);
        }
        return $result;
    }
    public function getCategoryProgress($userId, $categoryId) 
    {
        return $this->categoryModel->getCategoryProgress($userId, $categoryId);
    }
    public function updateCategoryProgress($userId, $categoryId, $status) 
    {
        return $this->categoryModel->updateCategoryProgress($userId, $categoryId, $status);
    }
}
