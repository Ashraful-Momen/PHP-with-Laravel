<?php

// $name = [1,2,3,4,4];

// foreach($name as $n)
// {
//     echo $n."\n";
// }

// $name = ['shuvo'=>[
//     'age'=>23,
//     'job'=>['freelancer', 'developer','bloger']],
//     'latif','laboni'];

//            print_r($name['shuvo']['job'][2]);

// $name=[
//     'name'              =>"Md.Ashraful Momen",
//     'age'               => "50",
//     'profession'        => 'CSE',
//     'Blood Group'       => 'O+',
//     'Mobile Number'     => '01674317715'
// ];
// $name["Marital Status"] = "Married";
// $nothing = array_keys($name);


//call by value:array copy: $newarray=$name;
//call by array reference: $newarrary =&$name;
//$newarray[name]="Momen";
// print_r($nothing);
// print_r($justKey);
// echo $justkey;
// print_r($justKey);
// print_r($name);

// foreach($name as $key=>$value){
//     echo $key."=".$value.' ';
// }
// $len = count($nothing);

// for($i =0; $i < $len; $i++)
// {
//        if($i<($len-1)){
//         echo $name[$nothing[$i]].", ";
//        }
//        else{
//         echo $name[$nothing[$i]].". ";
//        }

// }




// $i=0;
// for($i=1; $i<=10; $i++)
// {
//     echo " ";
//     echo $i." ";
//     if($i%2==0)
//     {
//         echo "\n";
//     }
   
// }

//know about the arry methods ....H.W.


//function:globals / locals / statics

// function one($a,$b)
// {
//     echo $a." ".$b."\n";
// }
// one(2,3);
// one(a:4,b:5);

//Function Recursion: HW.
// function one($a)
// {
//     if($a>10)
//     {
//         return;
//     }
//     echo $a;
//     $a++;
//     one($a);
// }
// one(12);

function sum(...$number){  //take value as array
    
    print_r($number);

}

sum(1,2,3,4,5);
