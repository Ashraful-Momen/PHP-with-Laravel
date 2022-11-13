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
  <style>
    .myclass{
        background-image: linear-gradient(to left,rgb(133, 34, 111),pink);
    }
  </style>

</head>

<body>
  <div class="container-fluid ">
    <div class="row myclass justify-content-center">
        <h1 class="text-center">User Registration</h1>
        <form  class="from w-50 p-2 text-white" >
            <div class="mb-3">
                <label for="fname">User Name:</label>
                <input type="text"  class="form-control" id="fname">
            </div>
           
            <div class="mb-3">
                <label for="number">Phone:</label>
                <input type="number"  class="form-control" id="number">

            </div>
            

            <div class="mb-3 p-2 ">
                <button  class="btn btn-dark" id="submit">
                    submit
                </button>
            </div>
        </form>
    </div>
  </div>
  <div>
  <h1 class="text-center">User Data </h1>
              <table class="table w-100 bg-info table-striped" >
                <thead>
                  <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Phone</th>
                  </tr>
                </thead>
                <tbody class="p-5 m-4" id="tbody">
                  
                    
                
                </tbody>
              </table>
  </div>
    <script>
       var frist = document.querySelector("#fname");
       var phone = document.querySelector("#number");

       var submitbtn = document.querySelector("#submit");

        submitbtn.addEventListener('click',function(){
    if (localStorage.getItem('reg-user') == null) {
            localStorage.setItem('reg-user', '[]');
            var data = JSON.parse(localStorage.getItem('reg-user'));
            data.push(user = {
                'username' : frist.value,
                'phone' : phone.value,
                

            })
            localStorage.setItem('reg-user', JSON.stringify(data));
            show();
        }
        else{
            var data = JSON.parse(localStorage.getItem('reg-user'));
            data.push(user = {
                'username' : frist.value,
                'phone' : phone.value,
            })
            localStorage.setItem('reg-user', JSON.stringify(data));

            show();
        }
});

function show(){
          var form = JSON.parse(localStorage.getItem('reg-user'));

          var display='';
          for (todo in form){
            display += 
            `
            
                    <tr>
                        <td scope="row">
                            ${form[todo].username}
                        </td>
                        <td>
                            ${form[todo].phone}
                        </td>
                    </tr>
            
            
           `;
          }
          console.log(form);
          var tbody = document.querySelector('#tbody');
          tbody.innerHTML = display;
        }


       

        show();

    </script>
  
  <!-- Bootstrap JavaScript Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
    integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
  </script>
</body>

</html>