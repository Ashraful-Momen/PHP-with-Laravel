<main>
    <div class="container h-100">
        <div class="row d-flex justify-content-center align-items-center h-100 py-3">
        <div class="col-lg-12 col-xl-11">
            <div class="card text-black" style="border-radius: 25px;">
            <div class="card-body p-md-5">
                <div class="row justify-content-center">
                <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">
 
                    <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Upload Files</p>
 
                    <form class="mx-1 mx-md-4" method="POST" action="login.php">
 
                    <div class="d-flex flex-row align-items-center mb-4">
                        <!-- <i class="fas fa-user fa-lg me-3 fa-fw"></i> -->
                        <div class="form-outline flex-fill mb-0">
                        <input type="text" name="title" class="form-control" required/>
                        <label class="form-label" for="form3Example1c">Slider Title</label>
                        </div>
                    </div>
 
                    <div class="d-flex flex-row align-items-center mb-4">
                        <!-- <i class="fas fa-envelope fa-lg me-3 fa-fw"></i> -->
                        <div class="form-outline flex-fill mb-0">
                        <input type="text" name="desc" class="form-control" required/>
                        <label class="form-label" for="form3Example3c">Slider Description</label>
                        </div>
                    </div>
 
                    <div class="d-flex flex-row align-items-center mb-4">
                        <!-- <i class="fas fa-lock fa-lg me-3 fa-fw"></i> -->
                        <div class="form-outline flex-fill mb-0">
                        <input type="file" name="files" class="form-control" required/>
                        <!-- <label class="form-label" for="form3Example4c">Password</label> -->
                        </div>
                    </div>
 
                    <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                        <button type="submit" name="submit" class="btn btn-primary btn-lg">Uploads</button>
                    </div>
 
                    </form>
 
                </div>
                <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2">
 
                    <img src="./media/images/register.jpg"
                    class="img-fluid" alt="Sample image">
 
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
   </main>