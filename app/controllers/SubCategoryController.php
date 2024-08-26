<?php

namespace App\Controllers;

use App\Models\SubCategory;

class SubCategoryController {

    private $subCategoryModel;

    public function __construct($db) {
        $this->subCategoryModel = new SubCategory($db);
    }

    public function getSubCategoriesByCategory($category_id) {
        return $this->subCategoryModel->getSubCategoriesByCategory($category_id);
    }

    public function getSubCategoryById($id) {
        return $this->subCategoryModel->getSubCategoryById($id);
    }   

    public function createSubCategory($title, $category_id) {
        return $this->subCategoryModel->createSubCategory($title, $category_id);
    }

    public function updateSubCategory($id, $title, $category_id) {
        return $this->subCategoryModel->updateSubCategory($id, $title, $category_id);
    }

    public function deleteSubCategory($id)
    {
        $this->subCategoryModel->setId($id);
        return $this->subCategoryModel->deleteSubCategory();
    }
}
