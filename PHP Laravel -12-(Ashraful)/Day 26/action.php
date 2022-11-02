<?php
require_once "vendor/autoload.php";
use App\classes\LoadPages;

include 'pages/include/header.php';

if(isset($_GET['pages'])){
    if('home'==$_GET['pages'])
    {
        include 'pages/frontend/home.php';
    }
    if('add-slider'==$_GET['pages'])
    {
        include 'pages/admin/add-slider.php';
    }

}


include 'pages/include/footer.php';