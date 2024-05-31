<?php

namespace App\Http\Controllers;

use Google_Client;
use Illuminate\Http\Request;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;


class PushNotification extends Controller
{
    public static function sendPushNotification($token_, $title, $description,$icon)
    {
        $url = env('FIRE_BASE_URL');
        $scope = env('FIRE_BASE_URL_SCOPE');
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
