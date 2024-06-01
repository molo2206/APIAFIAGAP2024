<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Slak extends Controller
{
    public function index()
    {

        // Log::channel(channel:'slack')->critical(message: 'Error');
        // dd(_war:"Log done");
        try {
            $user=User::all();
            Log::channel(channel:'slack')->critical(message:  $user);
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }



        // Log::info(message: "Test Default Info");
        // dd();

    }
}
