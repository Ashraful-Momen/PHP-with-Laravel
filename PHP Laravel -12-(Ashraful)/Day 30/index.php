<?php

// effecient code .... with design pettern : 
// create class as iterable elements;
// interface force to code decorately;


class District implements IteratorAggregate,Countable{
 
    public $data ;
    public function __construct()
    {
        $this->data =[];
    }

    //setter;
    function addDistirct($a){
        array_push($this->data,$a);
    }
    //getter:
    function getter($a){
        return $this->data;
    }
    #Fatal error: Class District contains 1 abstract method and must therefore be declared abstract or implement the remaining methods (IteratorAggregate::getIterator) in C:\xampp\htdocs\Ashraful\Day 30\index.php on line 6
    function getIterator()
    {
        return new ArrayIterator($this);
    }
    function count(): int
    {
        return count($this->data);
    }

    
}


$obj = new District();

// $obj-> addDistirct('Dhaka');
// $obj-> addDistirct('khulna');
// $obj-> addDistirct('chittagong');
// $obj-> addDistirct('Rajshahi');
// $obj-> addDistirct('Mymenshing');

echo "<pre>";
print_r($obj);
echo "</pre>";
$display= $obj->getter($obj->data);

// $count = array_keys($obj);

// print_r($count);
echo count($obj);
print_r($obj) ;