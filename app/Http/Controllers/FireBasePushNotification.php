<?php

namespace App\CustomClass;

use App\Models\Configuration;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Illuminate\Http\Request;

class FireBasePushNotification
{

    private $url = 'https://fcm.googleapis.com/v1/projects/cosamed-1e86c/messages:send';
    private $scope = 'https://www.googleapis.com/auth/firebase.messaging';
    private $token;

    public function __construct()
    {
        $credentialsFilePath = public_path() . "/firebase/fcm.json";
        $creadentials = new ServiceAccountCredentials($this->scope, $credentialsFilePath);
        $this->token = $creadentials->fetchAuthToken(HttpHandlerFactory::build());
    }

    public function to(Request $request)
    {
        $data = [
            'token' => $request->token,
            'title' => 'test',
            'body' => 'jgsgjgjdsj'
        ];

        return $this->send($data);
    }

    public function send($data)
    {
        $headers = [
            'Authorization: Bearer ' . $this->token['access_token'],
            'Content-Type: application/json'
        ];

        $fields = [
            'message' => [
                'token' => $data['token'],
                'notification' => [
                    'title' => $data['title'],
                    'body' => $data['body']
                ]
            ]
        ];

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
