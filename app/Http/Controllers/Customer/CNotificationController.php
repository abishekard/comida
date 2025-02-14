<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CNotificationController extends Controller
{


    public function sendNotification(Request $request)
    {
       // $firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->all();

       $firebaseToken=['foCTg8kzSF6ZD5iipLepwn:APA91bFp6KvF4k7xxZYZlF1fLUbhaTms6cWo3ITMQQAdChooTYN-SGtP5Sq2gbZH_6JjKyGqJFjsIxVI1jPIGpEgHStUujmeT_3wMyc6A8LvL54eY8vPQt9HyaFvc1GSpO0N3G2lS4UT'];
        $SERVER_API_KEY = 'AAAApuyCRGc:APA91bG-LcvN10PhgxV2CueE5BeI13x44IJBM_tLfGmuDlER3JV7H-gutccVv8Rh6NNJ2MD7plMlWbSmv-Ebs0rhssbqmyFH2DH94Cj-0UBFy-o90YejKubFc6SPlASXpIYjY0BilOzV';

        $data = [
            "registration_ids" => $firebaseToken,
            "data" => [
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

    public function sendNotificationToTopic(Request $request)
    {
       // $firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->all();

       $firebaseToken=['foCTg8kzSF6ZD5iipLepwn:APA91bFp6KvF4k7xxZYZlF1fLUbhaTms6cWo3ITMQQAdChooTYN-SGtP5Sq2gbZH_6JjKyGqJFjsIxVI1jPIGpEgHStUujmeT_3wMyc6A8LvL54eY8vPQt9HyaFvc1GSpO0N3G2lS4UT'];
        $SERVER_API_KEY = 'AAAApuyCRGc:APA91bG-LcvN10PhgxV2CueE5BeI13x44IJBM_tLfGmuDlER3JV7H-gutccVv8Rh6NNJ2MD7plMlWbSmv-Ebs0rhssbqmyFH2DH94Cj-0UBFy-o90YejKubFc6SPlASXpIYjY0BilOzV';

        $data = [
            "to" => 'topics/all',
            "data" => [
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
