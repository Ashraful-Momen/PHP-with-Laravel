<!doctype html>
<html lang="en">

<head>
  <title>::Calculator::</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS v5.2.1 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="./day22.css">

</head>

<body>

    <section >
        <div class="container-fluid myclass " style="height: 100vh;">
            <div class="row  ">
                <div class="col ">
                    <div class="card   col-8 m-auto my-5 text-center" >
                        <h2>Calculator</h2>
                        <hr>
                        <form action="index.php" method="post" class=" text-center formcolor" >
                            <input type="number" class="form-control text-center" name="number1" placeholder="input Number1">
                            <br>
                            <input type="number" class="form-control text-center" name="number1" placeholder="input Number2">
                            <br>
                            <select class="form-select mb-3" name="operator">
                                <option value="+">+</option>
                                <option value="-">-</option>
                                <option value="*">*</option>
                                <option value="/">/</option>
                                <option value="%">%</option>

                            </select>
                            <!-- <input type="submit">Calculator -->
                            <button type="submit" class="btn btn-outline-success">Calculate</button>
                        </form>
                        <?php
                            // echo "Hello Ready for Calculate"."<br>";
                            // $number1 = $_POST["number1"]."<br>"; 
                            // $number2 = $_POST["number1"]."<br>"; 
                            // $select1 = $_POST['operator'];
                           
                            // if(isset($_POST['operator'])&& isset($number1) && isset($number2)){
                            //     echo "Hello Ready for Calculate"."<br>";
                            //     $number1 = $_POST["number1"]."<br>"; 
                            //     $number2 = $_POST["number1"]."<br>"; 
                            //     $select1 = $_POST['operator'];
                            //     switch ($select1) {
                            //         case "+":
                            //             echo $number1+$number2;
                            //             break;
                            //         case '-':
                            //             echo $number1-$number2;;
                            //             break;
                            //         case '*':
                            //             echo $number1*$number2;;
                            //             break;
                            //         case '/':
                            //             echo $number1/$number2;;
                            //             break;
                            //         case '%':
                            //             echo $number1%$number2;
                            //             break;
                                   
                            //     }
                            // }
                           
                                    if(isset($_POST['sub'])){
                                        $num1=$_POST['n1'];
                                        $num2=$_POST['n2'];
                                        $oprnd=$_POST['sub'];
                                        if($oprnd=="+")
                                            $ans=$num1+$num2;
                                        else if($oprnd=="-")
                                            $ans=$num1-$num2;
                                        else if($oprnd=="x")
                                            $ans=$num1*$num2;
                                        else if($oprnd=="/")
                                            $ans=$num1/$num2;
                                    }?>
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>


  <!-- Bootstrap JavaScript Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
    integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
  </script>
</body>

</html>