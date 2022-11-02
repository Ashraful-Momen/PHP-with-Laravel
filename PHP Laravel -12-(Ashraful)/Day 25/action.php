<?php
require_once "vendor/autoload.php";
use App\classes\home;
if(isset($_GET['page'])){
    if('home'==$_GET['page']){
        
        $data = new home();
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