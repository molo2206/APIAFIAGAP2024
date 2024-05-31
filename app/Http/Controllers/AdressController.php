<?php

namespace App\Http\Controllers;

use App\Models\commune;
use App\Models\province;
use App\Models\quartier;
use App\Models\ville;
use Illuminate\Http\Request;

class AdressController extends Controller
{
    public function addprovince(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        if (province::where('name', $request->name)->exists()) {
            return response()->json([
                "message" => 'Cette province existe déjà dans le système',
                "data" => null,
                "code" => 422
            ], 422);
        } else {
            $user = province::create([
                'name' => $request->name,
            ]);
            return response()->json([
                "message" => "Enregistrement avec succès!",
                "code" => 200,
                "data" => province::all(),
            ], 200);
        }
    }
    public function listprovince(){
           $allprovince = province::all();
           return response()->json([
            "message" => "Liste des provinces!",
            "data" => $allprovince,
            "code" => 200,
        ], 200);
    }
    public function addville(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'provinceid' => 'required',
        ]);

        if (ville::where('name', $request->name)->exists()) {
            return response()->json([
                "message" => 'Cette ville existe déjà dans le système',
                "data" => null,
                "code" => 422
            ], 422);
        } else {
            $ville = ville::create([
                'name' => $request->name,
                'provinceid' => $request->provinceid,
            ]);
            return response()->json([
                "message" => "Enregistrement avec succès!",
                "code" => 200,
                "data" =>ville::all(),
            ], 200);
        }
    }
    public function listvile($idpro){
        $oneprovince = province::where('id',$idpro)->first();
        if($oneprovince == null){
            return response()->json([
                "message" => "Cette province n'existe  pas dans le système!",
                "data" => null,
                "code" => 422,
            ], 422);
        }else{
            $allville = ville::where('provinceid',$oneprovince->id)->first();
            return response()->json([
             "message" => "Liste des villes!",
             "data" => $allville,
             "code" => 200,
         ], 200);
        }
 }
 public function addcommune(Request $request)
 {
     $request->validate([
         'name' => 'required',
         'villeid' => 'required',
     ]);

     if (commune::where('name', $request->name)->exists()) {
         return response()->json([
             "message" => 'Cette commune existe déjà dans le système',
             "data" => null,
             "code" => 422
         ], 422);
     } else {
         $ville = commune::create([
             'name' => $request->name,
             'villeid' => $request->villeid,
         ]);
         return response()->json([
             "message" => "Enregistrement avec succès!",
             "code" => 200,
             "data" =>commune::all(),
         ], 200);
     }
 }
 public function listcommune($idville){
    $oneville = ville::where('id',$idville)->first();
    if($oneville == null){
        return response()->json([
            "message" => "Cette commune n'existe  pas dans le système!",
            "data" => null,
            "code" => 422,
        ], 422);
    }else{
        $allcommune = commune::where('villeid',$oneville->id)->first();
        return response()->json([
         "message" => "Liste des communes!",
         "data" => $allcommune,
         "code" => 200,
     ], 200);
    }
}

public function addquartier(Request $request)
 {
     $request->validate([
         'name' => 'required',
         'communeid' => 'required',
     ]);

     if (quartier::where('name', $request->name)->exists()) {
         return response()->json([
             "message" => 'Ce quartier existe déjà dans le système',
             "data" => null,
             "code" => 422
         ], 422);
     } else {
         $quartier = quartier::create([
             'name' => $request->name,
             'communeid' => $request->communeid,
         ]);
         return response()->json([
             "message" => "Enregistrement avec succès!",
             "code" => 200,
             "data" =>quartier::all(),
         ], 200);
     }
 }
 public function listquartier($idcom){
    $onecom = commune::where('id',$idcom)->first();
    if($onecom == null){
        return response()->json([
            "message" => "Ce quartier n'existe pas dans le système!",
            "data" => null,
            "code" => 422,
        ], 422);
    }else{
        $allcommune = quartier::where('communeid',$onecom->id)->first();
        return response()->json([
         "message" => "Liste des communes!",
         "data" => $allcommune,
         "code" => 200,
     ], 200);
    }
}
}
