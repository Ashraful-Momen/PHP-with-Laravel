#laravel email send : 
-----------------------
#create the notification : app/Notification/
---------------------------
>>> php artisan make:notification HospitalCardEmailNotification


#send notification in 2 way : 
-----------------------------
1.Add Notifiable to the User Model because we use $user by default email sent from user->email form DB
-----------------------------
class User extends Authenticatable
{
    use Notifiable;
................
................
}


2. method: 
----------
use App\Notifiaction\HospitalCardEmailNotification;
$user->notify(new HospitalCardEmailNotification($varibale_others)); 

or user facade =>  Notification::send($user, new HospitalCardEmailNotification($name, $hdcCard));



-------------------------------
-------------------------------

-------------------------------
#Route: 
--------

  Route::get('/hdc-notification', function(){
        $user = Auth::user();

            $user = User::findOrFail($user->id);

            // $hdcCard = $user->hdc_card->first;
            //UserName, Email: 01712655156:mohammad.ali@dnsgroup.net

            //

            // dd($user);

            $name = "Mohammad Ali";
            $hdcCard = "123698547896325";

            //run the command inside here
            // >>> php artisan queue:work // this command run in here.



            Artisan::call('queue:work', [
                '--once' => true // Ensures it processes one job and then exits
            ]);

            $user->notify(new HospitalCardEmailNotification($name,$hdcCard));


            // ---------------------------------------------------------
            #send mail to the all users:
            // $users = User::all();

            // foreach($users as $user){

            //     Notification::send($users, new HospitalCardEmailNotification());
            // }
            // ---------------------------------------------------------

            return "Email sent Successfully!";

    })->name('hdc_notification');

    
===================================
#HospitalCardEmailNotification: 
----------------------------------
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Contracts\Queue\ShouldQueue;

class HospitalCardEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $name = "";
    public $hdcCard = "";
    public function __construct($name,$hdcCard)
    {
        $this->name = $name;
        $this->hdcCard = $hdcCard;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // return (new MailMessage)
        //             ->line('Congratulations! Your Hospital Card has been successfully created. ')
        //             ->action('Notification Action', url('/'))
        //             ->line('Thank you for using our Service!');


        //pass the hdc card value here :
        $hdcCard = $this->hdcCard;

        $hdc_name = Auth::user()->name;




        return (new MailMessage)->view('emails.hdc_email_notification',compact('hdc_name','hdcCard'))->subject('Hospital Card Notification');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}


===================================
#send mail with queue: 
-----------------------
>>> php artisan queue:table
===================================
# in HospitalCardEmailNotification class add implements shouldQueue. 


#consume the loading time when email send : by >>> php artisan queue:work
-----------------------------------
# for run the queue work
-----------------------------------
>>> php artisan queue:work
===================================
.env
=====
## SMTP Mail Configuration ##
MAIL_MAILER=smtp
MAIL_HOST=business90.web-hosting.com
MAIL_PORT=587
MAIL_USERNAME=ashraful@instasure.xyz
MAIL_PASSWORD=SKJ95896856
MAIL_ENCRYPTION=TLS
# MAIL_ENCRYPTION=STARTTLS

MAIL_FROM_ADDRESS="${MAIL_USERNAME}"
MAIL_FROM_NAME="${APP_NAME}"
