
#make one function for data input with the jobs and queue
--------------------------------------------------------------------------------------------

Route : 
--------
<?php

// use GuzzleHttp\Psr7\Request;

use App\Http\Controllers\PersonController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::get('/', function () {
    // return "This is the Laravel Jobs and Queue Project";
    return view('welcome');
});

Route::post('/mycsv',[PersonController::class,'jobs_queue'])->name('mycsv');

-------------------------------------------------------------------------------------------
Controller: 
-------------
<?php

namespace App\Http\Controllers;

use App\Jobs\PersonCsvProcess;
use App\PersonDB;
use Illuminate\Http\Request;

class PersonController extends Controller
{

    function jobs_queue(Request $request)
    {
        $data = file($request->mycsv);

        $header = $data[0];

        unset($data[0]);

        $chunks = array_chunk($data, 100);

        $path = resource_path('temp');  // resuorce/temp/file_name....

        foreach ($chunks as $key => $chunk) {

            $name = "/tmp{$key}.csv";  //temp1.csv , ... create total 10, cause $chunks = 10.

            // ----------------------------------------------------
            // Define the path to the temp directory inside resources
            // $tempDirectory = resource_path('temp');

            // // Check if the directory doesn't exist, then create it: ------------
            // if (!file_exists($tempDirectory)) {
            //     mkdir($tempDirectory, 0755, true); // The third parameter true ensures recursive directory creation
            //     echo "Directory created successfully!";
            // } else {
            //     echo "Directory already exists!";
            // }
            // ----------------------------------------------------

            // return $path.$name; //  -> /home/ashraful/Laravel7/Larave_jobs_queue/laravel_jobs_queue/resources/temp/tmp0.csv

            file_put_contents($path . $name, $chunk);  //convert 1000 records into 10 csv file. with the file_put_contents().

        }



        //from store function code here : _______________________________________________________________
        $path = resource_path('temp'); // select the resource / temp , folder.

        $files = glob("$path/*.csv"); // select all the csv files form the directory.

        $header = [];

        foreach ($files as $key => $file) { // $files = 10 , so the loop iteration 10 times .

            $data = array_map('str_getcsv', file($file)); // get array form the csv1,csv2,.... file.

            if ($key === 0) {

                $header = $data[0]; // select the coloumn => first_name , last_name , email .

                unset($data[0]); //remove the header.

            }

            PersonCsvProcess::dispatch($data, $header);



            unlink($file); // delet the file .
        }


        return "file upload and create the csv file done";
    }



}

-------------------------------------------------------------------------------------------
Jobs : 
--------
<?php

namespace App\Jobs;

use App\PersonDB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PersonCsvProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data,$header; //from controller firt pass the $data , then $header


    public function __construct($data,$header)
    {
        //
        $this->data = $data;
        $this->header = $header;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // dd($this->data,$this->header);
        //copy form the store function ().

       //now store the data to DB : --------------------------------

       foreach ($this->data as $person) {

        $personData = array_combine($this->header, $person); //fix the error : from controller firt pass the $data , then $header

        PersonDB::create($personData);
    }

        // return "Data store successfully from multiple jobs . ";
    }
}

----------------------------------------------------------------------------------------------------------

-------------------------------------------Main Part form here---------------------------------------------------------------


route: 
----------
Route::get('/', function () {
    // return "This is the Laravel Jobs and Queue Project";
    return view('welcome');
});

Route::post('/mycsv',[PersonController::class,'jobs_queue'])->name('mycsv');

----------------------------------------------------------------------------------------------------------
#we work with data only , so no need file related codes : 
------------------------------------------------------------
controller: 
-------------
<?php

namespace App\Http\Controllers;

use App\Jobs\PersonCsvProcess;
use App\PersonDB;
use Illuminate\Http\Request;

class PersonController extends Controller
{

    function jobs_queue(Request $request)
    {
        $data = file($request->mycsv);

        $header = $data[0];

        unset($data[0]);

        $chunks = array_chunk($data, 100);

        $header = [];


        foreach ($chunks as $key => $chunk) {


            $data = array_map('str_getcsv', $chunk);

        

            if ($key === 0) {

                $header = $data[0];
                unset($data[0]);

            }

            PersonCsvProcess::dispatch($data, $header);

        }



        return "file upload and Data inseted done!";
    }



}




----------------------------------------------------------------------------------------------------------
jobs: 
-----------
<?php

namespace App\Jobs;

use App\PersonDB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PersonCsvProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data,$header; //from controller firt pass the $data , then $header


    public function __construct($data,$header)
    {
        //
        $this->data = $data;
        $this->header = $header;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

       //now store the data to DB : --------------------------------

       foreach ($this->data as $person) {

        $personData = array_combine($this->header, $person); //fix the error : from controller firt pass the $data , then $header

        PersonDB::create($personData);
    }


    }

      // if jobs is faild then here below function will be execute:_____________________________________________

    public function failed(Throwable $exceptions){

        //send your notification of your failur jobs, etc...
        

    }

}

----------------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------------
