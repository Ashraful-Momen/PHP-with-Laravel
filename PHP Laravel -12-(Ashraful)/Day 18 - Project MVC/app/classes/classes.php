<?php
namespace App\classes;
class classes
{
    public $text;
    public $data = [];
    public function __construct(){
        $this->text = "This is nothing but a start up of a project.";
    }
    public function printing(){
        echo "<br>";
        echo $this->text;
    }
    public function dataprint(){
        $this->data=[
            0 => [
                'Name' => 'Oleraj',
                'ID' => '18192103072',
                'Dept' => 'CSE',
                'Year' => '2019'
            ],
            1 => [
                'Name' => 'Oleraj',
                'ID' => '18192103072',
                'Dept' => 'EEE',
                'Year' => '2019'
            ],
            2 => [
                'Name' => 'Oleraj',
                'ID' => '18192103044',
                'Dept' => 'EEE',
                'Year' => '2020'
            ]
            
        ];
        return $this->data;
    }
    public function one(){
        header('Location: action.php?page=header');  
    }
}