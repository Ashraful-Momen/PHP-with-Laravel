<?php


include 'pages/includes/header.php';

if(isset($_GET["page"])){
    if( 'home' == $_GET["page"]){
        include 'pages/frontend/home.php';
    }
    if( 'add-slider' == $_GET["page"]){
        include 'pages/admin/add-slider.php';
    }
}

include 'pages/includes/footer.php';


