<?php
require_once 'app/classes/HelloWorld.php';
use App\classes\HelloWorld;


if(isset($_GET["page"])){
    if( 'home' == $_GET["page"]){

        $data = new HelloWorld();
        $menuItems = $data->menu();
        $products = $data->allProducts();
//        echo "<pre>";
//        print_r($products);
        include 'pages/home.php';
    }
}



//echo "This is action page";
