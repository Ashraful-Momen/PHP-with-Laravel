<?php
namespace App\classes;
class classes
{
   
    // public $data = [];
    
    // public $products = [];
    // public function dataprint(){
    //     $this->data=[
    //         0 => [
    //             'Name' => 'Oleraj',
    //             'ID' => '18192103072',
    //             'Dept' => 'CSE',
    //             'Year' => '2019'
    //         ],
    //         1 => [
    //             'Name' => 'Oleraj',
    //             'ID' => '18192103072',
    //             'Dept' => 'EEE',
    //             'Year' => '2019'
    //         ],
    //         2 => [
    //             'Name' => 'Oleraj',
    //             'ID' => '18192103044',
    //             'Dept' => 'EEE',
    //             'Year' => '2020'
    //         ]
            
    //     ];
       
        
    //     return $this->data;
    // }
    public $menu = [];  
    public function navmenu()
    {
        $this->menu =
        [
          
                [
                    0=>['one',"Home"]
                ],

                [   0=>['tow', "About"]
                ],
                [   0=>['three',"Contact"]
                
                ],
                [   0=>['four',"Services"=>['a','b','c'],['a','b','c'],['a','b','c']]
                ],
                [   0=>['five',"Blog"]
                ]
            
             ];
             return $this->menu;
        
    }

    public function one(){
        header('Location: action.php?page=home');  
    }
}