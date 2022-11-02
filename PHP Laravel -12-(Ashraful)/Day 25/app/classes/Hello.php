<?php
namespace  App\classes;

class Hello{
    function foo(){
        echo "Hello";
    }
    function one(){
        header("Location : action.php?page=home");
    }
}