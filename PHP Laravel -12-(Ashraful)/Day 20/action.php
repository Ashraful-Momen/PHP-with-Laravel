<?php
require_once "vendor/autoload.php";
use App\classes\classes;


if(isset($_GET['page'])){
    if('home'==$_GET['page']){
        
        $data = new classes();
        $mainmenu = $data -> navmenu();
        
        include 'pages/home.php';

    }
    else{
        echo "Error!";
    }
}
else{
    echo "Error!";
}