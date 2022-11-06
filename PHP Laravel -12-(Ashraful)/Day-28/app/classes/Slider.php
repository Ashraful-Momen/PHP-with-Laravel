<?php
namespace App\classes;
use App\classes\Database;


class Slider
{
    public $title,$desc,$image,$files,$db;

    public function __construct($request,$files)
    {
        $this->title = $request['title'];
        $this->desc = $request['desc'];
        $this->image = $files['image'];
        $this->db = new Database('shuvo');

        // $this->file = $files;
    }

    public function addSliderItem()
    {
        echo $this->title;
        echo $this->desc;
        echo ($this->files);
        echo "<pre>";
        print_r ($this->image);
         $imageName = "assets/images/slider-images/".time().$this->image['name'];
         $sql = "INSERT INTO `slider_items`(`title`, `desc`, `images`) VALUES ('$this->title','$this->desc','$imageName')";
         
         $con=$this->db->dbConnect();
         mysqli_query($con,$sql);

         move_uploaded_file($this->image['tmp_name'],$imageName);
         echo  "<br>Slider Data saved Successfully";
        // print_r (time());
        
       
    }
    public function updateSliderItem()
    {
       
    }
    public function deleteSliderItem()
    {
       
    }

}