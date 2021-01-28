<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CNotificationController extends Controller
{


    public function sendNotification(Request $request)
    {
       // $firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->all();

       $firebaseToken=['egaY5KUITzuNrvdSLDThrz:APA91bHiNhZrqdmK2QNY4QJqo9oX5WQQytfth1qFRDZXJQtSexjkl8Rk9Ow8Ubc3lwt1Ty2Xr2uTTBO4-L2xspLTYIRnpwy7uNJaWcLOm3YkJ8J9yVlpQrDFBvyLUZJCvwEubGVXi_y4'];
        $SERVER_API_KEY = 'AAAApuyCRGc:APA91bG-LcvN10PhgxV2CueE5BeI13x44IJBM_tLfGmuDlER3JV7H-gutccVv8Rh6NNJ2MD7plMlWbSmv-Ebs0rhssbqmyFH2DH94Cj-0UBFy-o90YejKubFc6SPlASXpIYjY0BilOzV';

        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,
                "image"=>""
            ]
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);

        dd($response);
    }
}
