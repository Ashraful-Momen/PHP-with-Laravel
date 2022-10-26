<?php
class AmountCalculator{
    protected $amount;
    public function __construct($myAmount)
    {
        $this->amount = $myAmount;
    }
    public function addFund($addAmount){
        echo $this->amount += $addAmount;
    }
    public function deductFund($deductAmount){
        echo $this->amount -= $deductAmount;
    }
}

class ChildClass extends AmountCalculator{

    public function one(){
//        $this->deductFund(20);
    }
//

    public function deductFund($deductAmount){
       $this->amount -= $deductAmount;
       echo "The Amount is {$this->amount}";
    }

}
$anountCal = new ChildClass(100);
$anountCal -> deductFund(50);

exit();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <?php
//                print_r($menuItems);
                foreach ($menuItems as $product) {
//                    echo $product[1][0];
                    if("Services" == $product[1][1]){
                        echo "<li class=\"nav-item dropdown\">
                                <a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"navbarDropdown\" role=\"button\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                                    Services
                                </a>
                                <div class=\"dropdown-menu\" aria-labelledby=\"navbarDropdown\">
                                    <a class=\"dropdown-item\" href=\"#\">{$product[1][0]}</a>
                                    <a class=\"dropdown-item\" href=\"#\">{$product[1][0]}</a>
                                    <a class=\"dropdown-item\" href=\"#\">{$product[1][0]}</a>
                                </div>
                             </li>";
                    } else{
                        echo "<li class=\"nav-item active\">
                                <a class=\"nav-link\" href=\"#\">{$product[1][1]}<span class=\"sr-only\">(current)</span></a>
                                </li>";
                    }

                }
                ?>

<!--                <li class="nav-item">-->
<!--                    <a class="nav-link" href="#">Link</a>-->
<!--                </li>-->

<!--                <li class="nav-item">-->
<!--                    <a class="nav-link disabled" href="#">Disabled</a>-->
<!--                </li>-->
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    </nav>

    <section>
        <div class="container">
            <div class="row bg-light py-4">
                <h2> All Products ></h2>
            </div>
            <div class="row bg-light py-5 rounded">
                <?php
                foreach ($products as $product){
                    ?>
                    <div class="col-md-4 pb-3">
                        <div class="card" style="width: 18rem;">
                            <img src="https://images.unsplash.com/photo-1661961110218-35af7210f803?ixlib=rb-4.0.3&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=870&q=80" class="card-img-top w-100" alt="Any">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $product['title']; ?></h5>
                                <p class="card-text"><?php echo$product['short_description']; ?></p>
                                <p class="card-text">Price: <?php echo $product['price']; ?> BDT</p>

                                <a href="#" class="btn btn-primary">Product Details</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
</body>
</html>










<!---->
<!--?>-->
<!--<!doctype html>-->
<!--<html lang="en">-->
<!--<head>-->
<!--    <meta charset="utf-8">-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1">-->
<!--    <title>Bootstrap demo</title>-->
<!--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">-->
<!--</head>-->
<!--<body>-->
<!---->
<!--<header>-->
<!--    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">-->
<!--        <div class="container-fluid">-->
<!--            <a class="navbar-brand" href="#">Z-Com</a>-->
<!--            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">-->
<!--                <span class="navbar-toggler-icon"></span>-->
<!--            </button>-->
<!--            <div class="collapse navbar-collapse" id="navbarScroll">-->
<!--                <ul class="navbar-nav m-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">-->
<!--                    <li class="nav-item">-->
<!--                        <a class="nav-link active" aria-current="page" href="#">Home</a>-->
<!--                    </li>-->
<!--                    <li class="nav-item">-->
<!--                        <a class="nav-link" href="#">Shop</a>-->
<!--                    </li>-->
<!--                    <li class="nav-item dropdown">-->
<!--                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">-->
<!--                            Products-->
<!--                        </a>-->
<!--                        <ul class="dropdown-menu">-->
<!--                            <li><a class="dropdown-item" href="#">Electronics</a></li>-->
<!--                            <li><a class="dropdown-item" href="#">Baby</a></li>-->
<!--                            <li><a class="dropdown-item" href="#">Man</a></li>-->
<!--                            <li><a class="dropdown-item" href="#">Women</a></li>-->
<!--                        </ul>-->
<!--                    </li>-->
<!--                </ul>-->
<!--                <form class="d-flex" role="search">-->
<!--                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">-->
<!--                    <button class="btn btn-outline-success" type="submit">Search</button>-->
<!--                </form>-->
<!--            </div>-->
<!--        </div>-->
<!--    </nav>-->
<!--</header>-->
<!---->
<!--<main>-->
<!--   -->
<!--</main>-->
<!---->
<!--<footer>-->
<!--    <-->
<!--</footer>-->
<!---->
<!---->
<!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>-->
<!--<script>-->
<!--    console.log(window);-->
<!--</script>-->
<!--</body>-->
<!--</html>-->
