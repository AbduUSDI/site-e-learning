<?php

namespace App\Controllers;

use App\Models\Page;

class PageController {

    private $pageModel;

    public function __construct($db) {
        $this->pageModel = new Page($db);
    }

    public function getPagesBySubCategory($subcategory_id) {
        return $this->pageModel->getPagesBySubCategory($subcategory_id);
    }

    public function getPageById($id) {
        return $this->pageModel->getPageById($id);
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
}
