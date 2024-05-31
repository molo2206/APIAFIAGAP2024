<?php

namespace App\Http\Controllers;

use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotification()
    {
        $user = Auth::user();
        return response()->json([
            "message" => "Notifications",
            "code" => "200",
            "data" => Notifications::where('user_id', $user->id)->where('deleted', 0)->orderby('date', 'desc')->get(),
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $notif = Notifications::where('deleted', 0)->where('id', $id)->first();
        if ($notif) {
            $notif->deleted = 1;
            $notif->update();
            return response()->json([
                "message" => "Publications",
                "code" => "200",
                "data" => Notifications::where('user_id', $user->id)->where('deleted', 0)->orderby('date', 'desc')->get(),
            ]);
        } else {
            return response()->json([
                "message" => "Identifiant not found",
                "code" => "402"
            ], 402);
        }
    }
}
