<?php
require_once "vendor/autoload.php";
use App\classes\Slider;



include 'pages/includes/header.php';


if(isset($_GET["page"])){
    if( 'home' == $_GET["page"]){
        include 'pages/frontend/home.php';
    }
    if( 'add-slider' == $_GET["page"]){
        if (isset($_POST['add_slider_submit'])) {
            $slider = new Slider($_POST,$_FILES);
            $slider->addSliderItem();
        }
        include 'pages/admin/add-slider.php';
    }
}

include 'pages/includes/footer.php';




