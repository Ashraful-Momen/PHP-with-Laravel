<?php 

$student=[
    'id'=> 1,
    'name'=>"Rahim",
    "standerd"=> "9th",
    "blood group"=>"o+",
    "fathers Name"=>"kamal Pasa"

];



// echo $len."\n";

// $key=array_keys($student);
// $len=count($key);

// echo $len;
// print_r($key);
/*
for($i=0; $i<$len;$i++)
{
    echo $student[$i];
}
*/


// var_dump($student[$key]);


// // echo $student[$key[3]];
// $len =count($key);



// for($i=$len; $i>= 0; $i--)
// {
//     echo $student[$i]." ".$student[$key[$i]]."\n";
// }




//line arry every element in new line;
// foreach($student as $s){

//     echo $student."\n";

// }


// $nothing = array_keys($student);

// $len = count($student);

// for($i =0; $i < $len; $i++)
// {
//        if($i<($len-1)){
//         echo $student[$nothing[$i]].", ";
//        }
//        else{
//         echo $student[$nothing[$i]].". ";
//        }

// }

$fruits="Mango, Banana, Apple, Orange,Grape";
$tElemetOfFruit = explode(', ',$fruits);

//regular expression important:
$tElemetOfFruit = preg_split('/, |,/', $fruits);
$res = array_reverse($tElemetOfFruit);
print_r($tElemetOfFruit);
print_r($res);

?>