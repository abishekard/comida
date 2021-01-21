<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Monolog\Handler\SendGridHandler;

class CEmailController extends Controller
{
    public function emailOtpVerify()
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("abishek@androasu.in", "Comida");
        $email->setSubject("Comida otp for Email verification");
        $email->addTo("abishek.ard@gmail.com", "Customer Name");
        //  $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
        $email->addContent(
            "text/html",
            "<strong>1234 is your otp for verfication at Comida</strong>"
        );
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            // print $response->statusCode() . "\n";
            // print_r($response->headers());
            // print $response->body() . "\n";

              return response()->json([
                  'status'=>200,
                  'message'=>$response->statusCode()." mail sent"
              ]);
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }
}
