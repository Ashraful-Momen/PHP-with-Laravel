<?php 


# =============Abstract===============
// abstract class A{

     
//      abstract function triangle($a,$b);

// }

// class B extends A{

//     function triangle($c,$d)
//     {
//         echo (.5*($c*$d));
//     }
// }

// $obj =new B();

// $obj->triangle(3,5);

#==================Interface ===================

// interface A{
//     function show($n);
    
// }
// interface B{
//     function hello();
// }
// class c implements A,B{
//     public function show($n){
//         echo "Hello".$n=3;
//     }
//     public function hello(){
//         echo "\n Bye";
//     }

// }
// $obj= new c();
// $obj->show(2);
// $obj->hello();
#========================================

class  A{

    static $name="Momen";
    public  function __construct()
    {
        
   
        echo "I am construct\n"; 
    }

        
   
     public static function show(){
        echo " class A";
        // self::$name;
     }
}
class B extends A{
    public static function display(){
        parent::show();
    }
    
}
// $obj=new A(); #without create object the constract function won't be worked.

echo A::show();

#==================w3 school============
// class greeting {
//     public static function welcome() {
//       echo "Hello World!";
//     }
//   }
  
//   class SomeOtherClass {
    
//     public function message() {  #must be call static method inside another mothed other ways error: syntax error, unexpected identifier "greeting", expecting "function" or "const" 
//       greeting::welcome();
//     }
//   }