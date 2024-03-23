#jobs batching : "create multiple worker for handle the jobs tasks. 
----------------
laravel 7 does not support queue: batching , so , we have to migrate laravel 7 to 8. 

>>> composer require laravel/framework:^8.0 --with-all-dependencies
==================================================================================================

1. goto the jobs class and add "use Batchable in side the class as trait". 

2. now declear the batch , inside the controller before the foreach loop create the batch; 

    -> $batch = Bus::batch([])->dispatch(); // creating the empty batch. 
3. now in jobs add inside the batch ; $batch->add(new PresonCsvProcess_jobs($data,$header)); 
    last return the $batch; => localhost:8000/mycsv (get the job Id)=> id	"9ba2444c-6239-4cac-b15a-bfe4bdcf92bf";

     //batch function for now details about the jobs :

    public function batch(){

        $batchId = request('id');

        return Bus::findBatch($batchId);
    }




4. after writing the batch function => goto url=> http://127.0.0.1:8000/batch?id=9ba2485c-eb43-4f13-a9b4-43befb124814


# update of the jobs => 	
-----------------------
id	"9ba24b23-ae2e-4441-b7bb-4b320ab12230"
name	""
totalJobs	300
pendingJobs	0
processedJobs	300
progress	100
failedJobs	0
options	[]
createdAt	"2024-03-23T17:04:36.000000Z"
cancelledAt	null
finishedAt	"2024-03-23T17:05:26.000000Z"

======================================================================================================
=========================================Main Part form here=============================================================
======================================================================================================
Route: 
======
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


Route::get('/batch',[PersonController::class,'batch']);

//real time update about jobs: _____________________

Route::get('/batchInProgress',[PersonController::class,'batchInProgress']);




======================================================================================================
Controllers: 
============
<?php

namespace App\Http\Controllers;

use App\Jobs\PersonCsvProcess;
use App\PersonDB;
use Illuminate\Http\Request;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;

class PersonController extends Controller
{

    function jobs_queue(Request $request)
    {
        $data = file($request->mycsv);

        $header = $data[0];

        unset($data[0]);

        $chunks = array_chunk($data, 100);

        $header = [];

        //creating the batch first: ---------------------

        $batch = Bus::batch([])->dispatch(); //creating the empty batch .



        foreach ($chunks as $key => $chunk) {


            $data = array_map('str_getcsv', $chunk);



            if ($key === 0) {

                $header = $data[0];
                unset($data[0]);

            }

            //dispatch the jobs into the batch : _______________
            $batch->add(new PersonCsvProcess($data, $header));

            // PersonCsvProcess::dispatch($data,$header);



        }



        return $batch;
    }

    //batch function for now details about the jobs :

    public function batch(){

        $batchId = request('id');

        return Bus::findBatch($batchId);
    }


    //bathInProgress for real time update , (showing the parcentage):

    public function batchInProgress(){
        $batches = DB::table('job_batches')->where('pending_jobs','>',0)->get();

        if(count($batches)>0){
            return Bus::findBatch($batches->id);
        }

        return [];
    }




}

======================================================================================================
#jobs: 
=========
<?php

namespace App\Jobs;

use App\PersonDB;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class PersonCsvProcess implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data, $header; //from controller firt pass the $data , then $header


    public function __construct($data, $header)
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

======================================================================================================
======================================================================================================
