<?php
  // $_POST[
  //   "f_name" => "rahim",
  //   "l_name" => "Ali"
  // ]
  // echo $_POST["f_name"];
  // echo $_POST["l_name"];
    if ($_POST["f_name"] && $_POST["l_name"] == NULL)
    {
      echo "the value is null";
    }
    else{
      echo $_POST["f_name"];
      echo "<br>";
      echo $_POST["l_name"];
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

</head>

<body>
    <form action="" method="post">
         <input type="text" placeholder="First_name" name="f_name" value=""> <!--here name="variable" => value=>"value"   -->
        <br>
        <br>
        <input type="text" placeholder="Last_name" name="l_name" value="">
        <br/>
        <input type="submit" value="submit for data">
    </form>
  <!-- Bootstrap JavaScript Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
    integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
  </script>
</body>

</html>