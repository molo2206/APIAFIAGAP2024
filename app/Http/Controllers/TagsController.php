<?php

namespace App\Http\Controllers;

use App\Models\AffectationModel;
use App\Models\AffectationPermission;
use App\Models\Permission;
use App\Models\Tags;
use App\Models\TokenUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagsController extends Controller
{
    public function getTag()
    {
        $user = Auth::user();
        return response()->json([
            "message" => "Tags",
            "code" => "200",
            "data" => Tags::where('userid', $user->id)->get(),
        ]);
    }

    public function permission(Request $request)
    {
        $affectation = AffectationModel::with('user', 'allpermission.permission')->where('orgid', $request->orgid)->get();
        $image = "https://apiafiagap.cosamed.org/public/uploads/gap/gap.PNG";
        $dataToken_for_user = TokenUsers::get();
        foreach ($dataToken_for_user as $item) {
            PushNotification::sendPushNotification($item->token, 'Nouveau Gap', 'dssdsds' . ' ' . 'dsdsd' . ' ' .'dvskhshdhj', $image);
        }
    }
}
