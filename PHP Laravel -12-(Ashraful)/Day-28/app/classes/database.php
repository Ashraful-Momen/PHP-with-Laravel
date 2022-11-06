<?php
namespace App\classes;

class Database
{
    public $dbHost, $dbUserName, $dbPass, $dbName; 

    public function __construct($db)
    {
        $this->dbHost     = 'localhost';
        $this->dbUserName = 'root';
        $this->dbPass     = '';
        $this->dbName     = $db;
    }

    public function dbConnect()
    {    
        $connection = mysqli_connect($this->dbHost,$this->dbUserName, $this->dbPass ,$this->dbName);
        return  $connection;
       
    }

}