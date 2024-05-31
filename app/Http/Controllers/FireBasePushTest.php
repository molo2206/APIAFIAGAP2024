<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

class FireBasePushTest extends Controller
{

    public static function push_to($token_, $title, $description, $icon)
    {
        $url = 'https://fcm.googleapis.com/v1/projects/cosamed-1e86c/messages:send';
        $scope = 'https://www.googleapis.com/auth/firebase.messaging';

        $credentialsFilePath = public_path() . "/firebase/fcm.json";
        $creadentials = new ServiceAccountCredentials($scope, $credentialsFilePath);
        $token = $creadentials->fetchAuthToken(HttpHandlerFactory::build());
        $data = [
            'token' => $token_,
            'title' => $title,
            'body' => $description,
            'image' => $icon
        ];
        $headers = [
            'Authorization: Bearer ' . $token['access_token'],
            'Content-Type: application/json'
        ];

        $fields = [
            'message' => [
                'token' => $data['token'],
                'notification' => [
                    'title' => $data['title'],
                    'body' =>  $data['body'],
                    'image' => $data['image'],
                ]
            ]
        ];

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
