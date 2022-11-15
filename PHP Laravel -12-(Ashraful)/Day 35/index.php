<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <style>
        .myclass {
            background-image: linear-gradient(to left, rgb(133, 34, 111), pink);
        }
    </style>

</head>

<body class="myclass">
    <div class="container-fluid ">
        <div class="row  justify-content-center">
            <h1 class="text-center">User Registration</h1>
            <form class="from w-50 p-2 text-white">
                <div class="mb-3">
                    <label for="username">User Name:</label>
                    <input type="text" class="form-control" id="username">
                </div>
                <div class="mb-3">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" required>
                </div>

                <div class="mb-3">
                    <label for="phone">Phone:</label>
                    <input type="number" class="form-control" id="phone" min="12" max='15' required>

                </div>


                <div class="mb-3 p-2 update">
                    <button type='button' class="btn btn-dark" id="submit">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div>
        <div class="container">
            <div class="row">
                <h1 class="text-center">User Data </h1>
                <table class="table w-100 bg-info table-striped text-center">
                    <thead>
                        <tr class="">
                            <div>
                                <th scope="col">Name</th>
                                <th scope="col">email</th>
                                <th scope="col">Phone</th>
                            </div>

                            <div class="me-auto ">
                                <th>Action</th>
                            </div>
                        </tr>
                    </thead>
                    <tr class="d-flex justify-content-around">
                        <tbody class="p-5 m-4 " id="tbody">



                        </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        var frist = document.querySelector("#username");
        var email = document.querySelector("#email");
        var phone = document.querySelector("#phone");

        var submitbtn = document.querySelector("#submit");

        submitbtn.addEventListener('click', function() {
            if (localStorage.getItem('reg-user') == null) {
                localStorage.setItem('reg-user', '[]');
                var data = JSON.parse(localStorage.getItem('reg-user'));
                data.push({
                    'username': username.value,
                    'phone': phone.value,
                    'email': email.value,


                })
                localStorage.setItem('reg-user', JSON.stringify(data));
                show();
            } else {
                var data = JSON.parse(localStorage.getItem('reg-user'));
                data.push({
                    'username': username.value,
                    'email': email.value,
                    'phone': phone.value
                })
                localStorage.setItem('reg-user', JSON.stringify(data));

                show();
            }
        });

        function show() {
            var form = JSON.parse(localStorage.getItem('reg-user'));

            var display = '';
            for (index in form) {
                display +=
                    `
            
                    <tr>
                        <td scope="row">
                            ${form[index].username}
                        </td>
                        <td>
                            ${form[index].email}
                        </td>
                        <td>
                            ${form[index].phone}
                        </td>
                        <td>
                        <button class="btn btn-primary" onClick="editWork(${index})">Update</button> 
                        <button class="btn btn-danger" onClick="deleteWork(${index})">Delete</button>
                        </td>
                    </tr>
            
            
           `;
            }
            //   console.log(form);
            var tbody = document.querySelector('#tbody');
            tbody.innerHTML = display;
        }

        //delete
        function deleteWork(index) {

            var data = JSON.parse(localStorage.getItem('reg-user'));
            console.log(data[index]);
            data.splice(index, 1);
            console.log(data);
            if (index == 0) {
                data = 0;
                return data;
            }
            localStorage.setItem('reg-user', JSON.stringify(data));
            show();
        }
        //Edit
        function editWork(index) {
            var data = JSON.parse(localStorage.getItem('reg-user'));
            console.log(data[index]);
            username.value = data[index].username;
            email.vaule = data[index].email;
            phone.value = data[index].phone;
            var update = document.querySelector('.update');
            update.innerHTML = `
            <button type='button' class="btn btn-dark" id="submit" onclick="update(${index})">
                        Update
                    </button>
            
            `;
        }

        function update(index) {
            var data = JSON.parse(localStorage.getItem('reg-user'));
            // console.log(data[index]);
            data[index].username = username.value;
            data[index].email = email.value;
            data[index].phone = phone.value;

            // console.log(data[index]);
            localStorage.setItem('reg-user', JSON.stringify(data));
            show();
        }




        show();
    </script>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js" integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
    </script>
</body>

</html>