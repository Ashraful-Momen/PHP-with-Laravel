<?php
namespace App\classes;

class LoadPages
{
    
    public function __construct()
    {
        
    }

    public function loadPage()
    {
       header('Location: action.php?page=home');
    }

}