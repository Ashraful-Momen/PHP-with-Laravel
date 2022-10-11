<?php

namespace  App\classes;


class Helloworld
{
    public $text;
    public function __construct()
    {
        $this->text="hello world";
    }
    public function one()
    {
        echo $this->text;
    }

}