#jobs: is the background processes . multiple jobs together is define as Batch of Jobs . 
-------
fake data generator: https://extendsclass.com/csv-generator.html
--------------------

---------------------------------------------Model---------------------------------------------------------------
------------------------------------------------------------------------------------------------------------
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonDB extends Model
{
    protected $table= 'person_d_b_s';
    protected $guraded = '';
    protected $fillable = [
    "id",
	"firstname",
	"lastname",
	"email",
    ];
}

------------------------------------------------------------------------------------------------------------
----------------------------------------------Table--------------------------------------------------------------
------------------------------------------------------------------------------------------------------------
  public function up()
    {
        Schema::create('person_d_b_s', function (Blueprint $table) {
            $table->bigIncrements('id')->nullable();
            $table->string("firstname")->nullable();
            $table->string("lastname")->nullable();
            $table->string("email")->nullable();
            $table->timestamps();
        });
    }
------------------------------------------------------------------------------------------------------------
---------------------------------------------Route---------------------------------------------------------------
------------------------------------------------------------------------------------------------------------
<?php

// use GuzzleHttp\Psr7\Request;

use App\Http\Controllers\PersonController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return "This is the Laravel Jobs and Queue Project";
    return view('welcome');
});

Route::post('/mycsv',[PersonController::class,'jobs_queue'])->name('mycsv');

------------------------------------------------------------------------------------------------------------
--------------------------------------------Controller----------------------------------------------------------------
------------------------------------------------------------------------------------------------------------
<?php

namespace App\Http\Controllers;

use App\PersonDB;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    //
    function jobs_queue(Request $request)
    {

        // dd($request->file());
        // return file($request->mycsv); // return the file

        // return array_map('str_getcsv',file($request->mycsv)); // this function make the csv file content in associative array....
        $data =  array_map('str_getcsv', file($request->mycsv)); // this function make the csv file content in associative array....

        // return $data[0]; // header (id, first_name, last_name, email of column ). we get the column name in the csv file of array .
        //we want to save data to the database into the database without the header $data[0] so, we can use the unset();

        //check the header or first column name in the csv file :
        // ----------------------------------------------------------
        // return $data[0];
        // unset($data[0]); //remove the header $data[0]
        // return $data; // get all data without the header.

        // ----------------------------------------------------------------
        //we have to upload the data to the Database.

        $header = $data[0];

        unset($data[0]);

        foreach ($data as $key=>$value) {
            // dd(array_combine($header, $value));   //header_name = value

            //output: -------------------------------
            //   "id" => "100"
            //   "firstname" => "Marleah"
            //   "lastname" => "Monk"
            //   "email" => "Marleah.Monk@yopmail.com"
            //output: -------------------------------

            $person = array_combine($header, $value);   //header_name = value
            PersonDB::create($person); // insert the data to the database ;

            if($key ==100){
                break;
            }
        }

        return "DB insert the data is done";
    }
}

------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------
