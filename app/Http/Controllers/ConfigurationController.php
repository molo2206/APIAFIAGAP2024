<?php

namespace App\Http\Controllers;

use App\Models\ConfigurationModel;
use Illuminate\Http\Request;
use League\Config\Configuration;
use App\Http\Controllers\UtilController;

class ConfigurationController extends Controller
{
    public function create_infos_app(Request $request)
    {
        $request->validate([
            'organisation_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'politique' => 'required',
            'condition' => 'required',
            'apropos' => 'required',
            'description' => 'required',
            'adresse' => '',
        ]);

        $conf = ConfigurationModel::first();

        if ($conf == null) {
            ConfigurationModel::create([
                'organisation_name' => $request->organisation_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'politique'  => $request->politique,
                'condition' => $request->condition,
                'apropos' => $request->apropos,
                'version' => $request->version,
                'app_store' => $request->app_store,
                'google_play' => $request->google_play,
                'description' => $request->description,
                'adresse' => $request->description,
            ]);
            return response()->json([
                "message" => "success",
                "code" => 200,
                "data" => ConfigurationModel::where('organisation_name', $request->organisation_name)->first(),
            ], 200);
        } else {
            $conf->organisation_name = $request->organisation_name;
            $conf->email = $request->email;
            $conf->phone = $request->phone;
            $conf->politique  = $request->politique;
            $conf->condition = $request->condition;
            $conf->apropos = $request->apropos;
            $conf->version = $request->version;
            $conf->app_store = $request->app_store;
            $conf->google_play = $request->google_play;
            $conf->description = $request->description;
            $conf->update();
            return response()->json([
                "message" => "success",
                "code" => 200,
                "data" => ConfigurationModel::where('id', $conf->id)->first(),
            ], 200);
        }
    }

    public function get_infos_organisation()
    {
        return response()->json([
            "message" => "success",
            "code" => 200,
            "data" => ConfigurationModel::first(),
        ], 200);
    }


    public function create_logo_fiveicon(Request $request)
    {
        $logo = UtilController::uploadImageUrl($request->logo, '/uploads/user/');
        $fiveicone = UtilController::uploadImageUrl($request->fiveicone, '/uploads/user/');
        $conf = ConfigurationModel::first();
        if ($conf == null) {
            ConfigurationModel::create([
                'logo' => $logo,
                'fiveicone' => $fiveicone,
            ]);
            return response()->json([
                "message" => "success",
                "code" => 200,
                "data" => ConfigurationModel::where('id', $request->id)->first(),
            ], 200);
        } else {
            if ($logo == null) {
                $conf ->logo = $conf->logo;
            } else {
                $conf->logo = $logo;
            }
            if ($fiveicone == null) {
                $conf ->fiveicone = $conf->fiveicone;
            } else {
                $conf->fiveicone = $fiveicone;
            }
            $conf->update();
            return response()->json([
                "message" => "success",
                "code" => 200,
                "data" => ConfigurationModel::where('id', $conf->id)->first(),
            ], 200);
        }
    }

    public function create_blog()
    {

    }

    public function update_blog()
    {

    }

    public function list_blog()
    {

    }

    public function detail_blog()
    {

    }

}
