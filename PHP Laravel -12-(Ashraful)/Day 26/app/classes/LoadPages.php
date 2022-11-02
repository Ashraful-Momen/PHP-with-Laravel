<?php
namespace  App\classes;

class LoadPages{
    function __construct()
    {
        
    }
    function loadpages(){
        header("Location : action.php?pages=home");
    }
}