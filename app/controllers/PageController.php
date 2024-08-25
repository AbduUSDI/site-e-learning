<?php

namespace App\Controllers;

use App\Models\Page;

class PageController {

    private $pageModel;

    public function __construct($db) {
        $this->pageModel = new Page($db);
    }
    public function getAllPages() {
        return $this->pageModel->getAllPages();
    }
    public function getPagesBySubCategory($subcategory_id) {
        return $this->pageModel->getPagesBySubCategory($subcategory_id);
    }

    public function getPageById($id) {
        return $this->pageModel->getPageById($id);
    }
    // Méthode pour incrémenter le compteur de vues d'une page
    public function incrementPageViewCount($pageId)
    {
        return $this->pageModel->incrementViewCount($pageId);
    }
    public function createPage($title, $content, $video_url, $subcategory_id) {
        return $this->pageModel->createPage($title, $content, $video_url, $subcategory_id);
    }

    public function updatePage($id, $title, $content, $video_url, $subcategory_id) {
        return $this->pageModel->updatePage($id, $title, $content, $video_url, $subcategory_id);
    }


    public function deletePage($id)
    {
        $this->pageModel->setId($id);
        return $this->pageModel->deletePage();
    }
    public function getPagesWithPagination($start, $limit) 
    {
        return $this->pageModel->getPagesWithPagination($start, $limit);
    }
    public function getTotalPages($limit) 
    {
        return $this->pageModel->getTotalPages($limit);
    }
}
