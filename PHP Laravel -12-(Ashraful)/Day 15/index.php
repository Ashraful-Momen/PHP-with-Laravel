<?php
// require_once "vendor/autoload.php";
// use App\classes\oleraj;
// $hello = new oleraj();
// $hello -> printing();
// $hello -> printing1();
// $hello -> printing2();

// echo "  ";
// $i=1;
// function sum(){
//     global $i;
//     echo $i;
// }
// sum();   

// function sum0(...$numbers){
//     $limit = count($numbers);
//     for($i=0;$i<=$limit;$i++){
//         $sums+=$numbers($i);
//     }
//     echo $sums;
//     print_r($numbers);
// }
// sum0(...number:1,2,3,4,5);


// function sum1(int $a): int{
//     return $a;
// }

// class oleraj
// {
//     public $text;
//     public function __construct(){
//         $this->text = "Hi, I am oleraj";

//     }
//     public function printing(){
//         echo $this->text;
//         echo " ";
//         for($i=1;$i<=10;$i++){
//             if($i==10){
//                 echo $i;
//             }
//             else{
//                 echo $i.",";
//             }
//         }      
//     }
//     public function printing1(){
//         echo "  ";
//         $i=1;
//         while($i<=10){
//             echo $i." ";
//             $i++;
//         }
//     }
//     public function printing2(){
//         echo "  ";
//         $i=1;
//         do{
//             $i++;
//             echo $i;
//         }
//         while($i < 1);
//     }
// }
$names=["oleraj","hossin","mondol","Exam",1010];
$names["oleraj"]="Enjamul";
print_r($names);