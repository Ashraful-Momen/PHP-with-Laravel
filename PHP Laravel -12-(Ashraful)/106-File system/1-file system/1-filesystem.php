<?php

// echo readfile('read.txt');

// $file = readfile('./read.txt');

// if(file_exists($file)){
//     echo $file;
// }
// else{
//     echo 'file not exist';
// }
 #-----------------Copy File : ----------------------

// $file = './read.txt';

// if(file_exists($file)){
//     copy($file,'copyfile.txt'); #copy(copy_fileName, previous_fileName)
//     readfile('copyfile.txt');
// }
// else{
//     echo 'file not exist';
// }
#------------------Rename file ---------------------

// $file = "new_file.txt";
// echo readfile($file);
// if(file_exists($file)){
//   rename("new_file.txt","old_file.txt");	#rename(previous_file, rename_filename)
// }else{
//   echo "File doest not exist" . "<br><br>";
// }
#------------------unlink(Delete_file_name with full of path)/ same funcition : delete() ---------------------
// $file = "delete.txt";
// echo readfile($file);
// if(file_exists($file)){
//   unlink($file);	#unlike(file_name) : delete the file form server ;
//   echo "file deleted";
// }else{
//   echo "File doest not exist" . "<br><br>";
// }
#------------------mkdir+file_exits()---------------------

// mkdir('./Shuvo');



// if(!file_exists('./Shuvo')){
//     mkdir('./Shuvo');
// }
// else{
//     echo 'folder already exit';
// }
#------------------file_size() => Return Byte: ---------------------

// $file = "read.txt";
// echo filesize($file) . "<br>";

// echo filetype($file); #filetype(): use also for file + folder

// echo "<br>".filetype('Shuvo');

// echo "<br>".realpath('shuvo'); #realpath () : return full path // __DIR__// __FILE__

// echo "<br>".__FILE__;
// echo "<br>".__DIR__;
// echo '<pre>';

// print_r(pathinfo('read')) ; //passing full path : DIR name, base name, file/folder name and extension;

// echo '</pre>';
#====================================Details About File information :VVI============================================
// $file = 'copyfile.txt';
// $path =  realpath($file) . "<br>"; 
// print_r(pathinfo($path));       //--- passing full path
#=============================================================================

// print_r(pathinfo($path,PATHINFO_DIRNAME)). "<br>"; //------- return only dirname

// print_r(pathinfo($path,PATHINFO_BASENAME)). "<br>"; //------return only basename

// print_r(pathinfo($path,PATHINFO_EXTENSION)). "<br>"; //------return only extension

// print_r(pathinfo($path,PATHINFO_FILENAME)). "<br>"; //------return only filename

#------------------------------------------------------------ ---------------------
//Show filename
$path = 'Shuvo';
echo basename($path) ."<br/>";

//Show filename, but cut off file extension for ".php" files
echo basename($path,".txt");


$file = "read.txt";
$path = realpath($file). "<br>";

echo "<br>".dirname($path). "<br>"; //------ Return the path of the parent directory:

echo dirname($path,2) . "<br>";


?>