<?php 
namespace App\Controllers;
use App\Models\Styles;

class StylesController extends Styles
{

private $stylesModel;

    public function __construct() {
        $this->stylesModel = new Styles();
    }
    public function putStyle() {
        
    }
}