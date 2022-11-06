<?php
namespace App\classes;

class LoadPages
{
    

    public function loadPage()
    {
       header('Location: action.php?page=home');
    }

}