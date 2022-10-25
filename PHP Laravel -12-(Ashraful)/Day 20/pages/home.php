<?php

# public , private , protected : usage 
# class , object :


// class Sum{
//     public $number=100;
//     public function addAmount($amount){
//         $this->number += $amount;
//     }
//     public function reduceAmount($amount){
//         $this->number -= $amount;
//     }


// }

//  class animal {
//     public $name='something';
  
//     public function sleep($name)
//     {
//         echo "{$name}can sleep";
//     }
//     public function eat()
//     {
//         echo "can eat";
//     }
//  }
//  class person extends animal {
//     public $name;
//     public function think()
//     {
//         echo "can think";
//     }
   
//  }
//  class cat extends person {
    
  
//  }
//  $man = new cat();
//  $man->eat();
//  $abc = new Sum(); 
    // echo "<pre>";
    // print_r($mainmenu);
    // echo "</pre>"."<br>";

#---------------Day 20---------------

class Amount{
    public $amount;
    public function __construct()
    {
        $this->amount=100;
    }
    public function addFund($addAmount){
        $this->amount += $addAmount;
    }
    public function deFund($deductAmount){
        $this->amount -= $deductAmount;
    }

    $try = new Amount();
    // $monenyCal = new Amount();
    // $monenyCal->addFund(50);

}
?>

<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
   
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Navbar</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    

                       
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 mx-auto">
                    <?php 
                        foreach($mainmenu as $menu)
                        {?>
                        <li class="nav-item">
                            <a class="nav-link disable " aria-current="page" href="#"><?php echo $menu[0][1];?></a> 
                        </li>
                       
                        <?php
                    }?>
                    </ul>
                       
                </div>
            </div>
        </nav>
    </header>


  

        <!-- card sectino end -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center my-5">
                    created by Md.Ashraful momen
                </div>
            </div>
        </div>
    </footer>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js" integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
    </script>
</body>

</html>