<?php
namespace App\classes;
class classes
{
   
    public $data = [];
    public $products = [];
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

    public function allproduct()
    {
        $this->products =
        [
             [
                'id'  => 1,
                // "img" => 'src: path'
                'title' => "Best laptop 1",
                'price' => ' 30000',
                'short_description' => " asdfhuh fnwefrn anfnwefn anfni "
            ],
             [
                'id'  => 2,
                'title' => "Best laptop 2",
                'price' => ' 40000',
                'short_description' => " ;asjfhhodf fnwefrn anfnwefn anfni "
            ],
             [
                'id'  => 3,
                'title' => "Best laptop 3",
                'price' => ' 50000',
                'short_description' => " akonhonoh fnwefrn anfnwefn anfni "
            ]
            
             ];
             return $this->products;
        
    }
    public function one(){
        header('Location: action.php?page=header');  
    }
}