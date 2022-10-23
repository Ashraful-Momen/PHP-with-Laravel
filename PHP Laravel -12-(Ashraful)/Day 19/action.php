<?php
require_once "vendor/autoload.php";
use App\classes\classes;
if(isset($_GET['page'])){
    if('header'==$_GET['page']){
        // echo "Hello World!";
        $data = new classes();
        $products = $data -> allproduct();
        // print_r($products);
        // echo "<pre>";
        // print_r($data->dataprint());
        // include 'pages/header.php';
        include 'pages/home.php';

    }
    else{
        echo "Error!";
    }
}
else{
    echo "Error!";
}