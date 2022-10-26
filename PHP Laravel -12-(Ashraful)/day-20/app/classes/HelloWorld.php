<?php
namespace App\classes;

class HelloWorld
{
    public $data = [];
    public $products = [];


    public function __construct()
    {

    }

    public function loadPage()
    {
       header('Location: action.php?page=home');
    }


    public function studentsData(){
        $this->data = [
            0 => [
                'id' => 1,
                'name' => 'Habib Sir',
                'email' => 'demo@gmail.com',
                'mobile' => '0000000000'
            ],
            1 => [
                'id' => 2,
                'name' => 'Sahadat Sir',
                'email' => 'sahadat@gmail.com',
                'mobile' => '1111111111'
            ],
            2 => [
                'id' => 3,
                'name' => 'Demo Sir',
                'email' => 'kkk@gmail.com',
                'mobile' => '2222222222'
            ],
            3 => [
            'id' => 4,
            'name' => 'Demo Sir',
            'email' => 'kkk@gmail.com',
            'mobile' => '2222222222'
        ]
        ];
        return $this->data;
    }

    public function allProducts(){

        $this->products = [
            [
                "id" => 1,
                "title" => "Best Laptop 1",
                "image" => "https://images.unsplash.com/photo-1661961110218-35af7210f803?ixlib=rb-4.0.3&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=870&q=80",
                "price" => 100000,
                "short_description" => "Product 1 build on the card title and make up the bulk of the card's content.",
            ],
            [
                "id" => 2,
                "title" => "Best Laptop 2",
                "image" => "https://images.unsplash.com/photo-1661961110218-35af7210f803?ixlib=rb-4.0.3&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=870&q=80",
                "price" => 30090,
                "short_description" => "Product 2 build on the card title and make up the bulk of the card's content.",
            ],
            [
                "id" => 3,
                "title" => "Best Laptop 3",
                "image" => "https://images.unsplash.com/photo-1661961110218-35af7210f803?ixlib=rb-4.0.3&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=870&q=80",
                "price" => 30700,
                "short_description" => "Product 3 build on the card title and make up the bulk of the card's content.",
            ],
            [
                "id" => 4,
                "title" => "Best Laptop 4",
                "image" => "https://images.unsplash.com/photo-1661961110218-35af7210f803?ixlib=rb-4.0.3&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=870&q=80",
                "price" => 30800,
                "short_description" => "Product 4 build on the card title and make up the bulk of the card's content.",
            ],
            [
                "id" => 5,
                "title" => "Best Laptop 5",
                "image" => "https://images.unsplash.com/photo-1661961110218-35af7210f803?ixlib=rb-4.0.3&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=870&q=80",
                "price" => 30500,
                "short_description" => "Product 5 build on the card title and make up the bulk of the card's content.",
            ],
            [
                "id" => 6,
                "title" => "Best Laptop 6",
                "image" => "https://images.unsplash.com/photo-1661961110218-35af7210f803?ixlib=rb-4.0.3&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=870&q=80",
                "price" => 20000,
                "short_description" => "Product 6 build on the card title and make up the bulk of the card's content.",
            ],
        ];

        return $this->products;
    }

    public function menu(){

        $menu = [
            [
                1 =>["Service One","Home"]
            ],
            [
                1 =>["Service Two","About"]
            ],
            [
                1 =>["Service Three","Contact"]
            ],
            [
                1 =>["Service Four","Services"]
            ],
            [
                1 =>["Service Five","Blog"]
            ]
        ];

        return $menu;
    }

}