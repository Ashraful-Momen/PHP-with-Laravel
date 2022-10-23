<?php
// $age = array("shuvo"=>62,"Mukti"=>24);

// #echo "shvuo age is : ".$age['shuvo'];

// foreach ($age as $key => $value)
// {
//     echo "age of : ".$key."=".$value."\n";
// // }
// $car =array(
//     array('zBMW',200,150),
//     array('yFerary',200,150),
//     array('xMarcitiz',200,150)
// );

// for ($row=0;$row<3;$row++) #outer loop for row 
// {
//     echo "Number of Row: ".$row."\n";

//     for($col=0;$col<3;$col++) #inner loop for column
//     {
//         echo "value: ".$car[$row][$col]."\n";
//     }

    
// }

// $accending = rsort($car);

// for ($row=0;$row<3;$row++) #outer loop for row 
// {
//     echo "Number of Row: ".$row."\n";

//     for($col=0;$col<3;$col++) #inner loop for column
//     {
//         echo "value: ".$car[$row][$col]."\n";
//     }

    
// }

// $n = array(
//     'name'=> 'shvuo',
//     'age'=> '23',
//     'job'=> 'software'
// );

// $key = array_keys($n);

// print_r($key);

// $val= array_values($n);

// print_r($val);

// foreach($n as $k => $v )
// {
//     echo "keys :".$k." and  Value: ".$v."\n";
// }

//print multi dimention arry with for loop :

// $k = array_keys($n);
// $v = array_values($n);

// $len = count($n);

// for ($i=0;$i<$len;$i++)
// {
//     echo "Keys: ".$k[$i]." values: ".$v[$i]."\n";
// }

# pass array from class to a function and print:

// class gelo
// {
//     public $text;
//     public $data = [];
   
//     public function dataprint(){
//         $this->data=[
//             0 => [
//                 'Name' => 'Oleraj',
//                 'ID' => '18192103072',
//                 'Dept' => 'CSE',
//                 'Year' => '2019'
//             ],
//             1 => [
//                 'Name' => 'Oleraj',
//                 'ID' => '18192103072',
//                 'Dept' => 'EEE',
//                 'Year' => '2019'
//             ],
//             2 => [
//                 'Name' => 'Oleraj',
//                 'ID' => '18192103044',
//                 'Dept' => 'EEE',
//                 'Year' => '2020'
//             ]
            
//         ];
//         return $this->data;
//     }
// }

                
// $person = new gelo();
// $person->dataprint();


// foreach($person as $k => $v) #data
// {
//     // echo $v."   \n";
//    foreach($v as $v =>$k) #0,1,2 offset
// {
//     echo $v."   \n";
//     foreach($k as $k =>$v) # k=> ofset = index and $v =index value
//     {
//         echo $k."   ";
//         echo $v."\n";
//     }
// }
// }
#======================================End===============================================

#String to  Array : 

// $string = 'mango, banana, apple,orange';

// $put = explode(',',$string);

// var_dump($put);

// #Array to String:

// $back = implode(',',$put);

// var_dump($back);

#check elements in Array: 

// $a = array('a'=>1,'b'=>2,'c'=>3);

// $check = in_array('',$a); // pass the value of index other ways output: False.

// var_dump($check);


// array_push($a ,"3");
// var_dump($a);
// array_pop($a);
#========================
// $a = array('a'=>1,'b'=>2,'c'=>3);
// $a['d']=3;

// var_dump($a);
#================php menual===========
// $data['one'] = 1;
// $data += [ "two" => 2, "three" => 3 ];
// $data += [ "four" => 4 ];

#Note that like array_push (but unlike $array[] =) the array must exist before the unary union, which means that if you are building an array in a loop you need to declare an empty array first...

// $data = [];
// for ( $i = 1; $i < 5; $i++ ) {
//        $data += [ "element$i" => $i ];
// }

// ...which will result in an array that looks like this...

// [ "element1" => 1, "element2" => 2, "element3" => 3, "element4" => 4 ];


