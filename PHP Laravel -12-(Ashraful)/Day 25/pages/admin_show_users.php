<?php
  // mysqli_connect('host','username','password','db_name');
  $dbconnection = mysqli_connect('localhost','root','','e_com');

  $sql = "SELECT * FROM users";

  
  
  $read= mysqli_query($dbconnection, $sql);
//   while($users=mysqli_fetch_array($read,MYSQLI_ASSOC)){ #if while Loop use in here then below while loop not be worked!
//     print_r($users);
//   }
//   $users=mysqli_fetch_array($read,MYSQLI_ASSOC); #mysqli_assoc : converted data into associte array!
//   echo "<pre>";
//   var_dump($users);
//   echo "</pre>";
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
<section>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Features</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Pricing</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown link
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <li><a class="dropdown-item" href="#">Admin show user data </a></li>
            <li><a class="dropdown-item" href="#">Sign UP</a></li>
            <li><a class="dropdown-item" href="#">LogIN</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
  </section>
  <section>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
            <table class="table">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">UserName</th>
      <th scope="col">Email</th>
      <th scope="col">Action</th>
      <th scope="col">status</th>
    </tr>
  </thead>
  <tbody>

  <?php
         while($users=mysqli_fetch_assoc($read)){?>
           
      <tr>
        <th scope="row"><?php echo $users["ID"]?></th>
        <td><?php echo $users["user_name"]?></td>
        <td><?php echo $users["email"]?></td>
        <td><?php echo "Edit/Delete"?></td>
        <td><?php echo "status"?></td>
        
     
      </tr>
           
         <?php }
        
        ?>
   
    
  </tbody>
</table>
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