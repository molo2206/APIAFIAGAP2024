<?php

namespace App\Http\Controllers;

use App\Models\AffectationModel;
use App\Models\AffectationPermission;
use App\Models\Maladie;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaladieController extends Controller
{
    public function AddMaladie(Request $request)
    {
        $request->validate([
            "name" => "required"
        ]);

        $user = Auth::user();
        //create_maladie
        if ($user->checkPermissions('Gap', 'create')) {
            if (!Maladie::where('name', $request->name)->exists()) {
                Maladie::create([
                    "name" => $request->name,
                ]);
                return response()->json([
                    "message" => 'Traitement réussi avec succès!',
                    "code" => 200
                ], 200);
            } else {
                return response()->json([
                    "message" => "Ce nom de maladie existe déjà",
                    "code" => 422,
                ], 422);
            }
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }
    public function updateMaladie(Request $request, $id)
    {
        $request->validate([
            'name' => 'required:',
            'orgid' => 'required',
        ]);
        $user = Auth::user();
        if ($user->checkPermissions('Gap', 'update')) {
            $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
            if ($organisation) {
                if ($user)
                {
                    if(!Maladie::where('name', $request->name)->exists())
                    {
                        $maladie = Maladie::find($id);
                        if ($maladie) {
                            $maladie->name = $request->name;
                            $maladie->save();
                            return response()->json([
                                "message" => "La modification réussie"
                            ], 200);
                        } else {
                            return response()->json([
                                "message" => "Erreur de la modification",
                            ], 422);
                        }
                    }else{
                        return response()->json([
                            "message" => "Cette maladie existe!",
                        ], 422);
                    }

                } else {
                    return response()->json([
                        "message" => "Identifiant not found",
                        "code" => "402"
                    ], 402);
                }
            } else {
                return response()->json([
                    "message" => "cette organisationid" . $organisation->id . "n'existe pas",
                    "code" => 402
                ], 402);
            }
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function listMaladie()
    {
        $maladie = Maladie::orderBy('name', 'DESC')->get();
        return response()->json([
            "message" => "Listes des maladies!",
            "data" => $maladie,
            "code" => 200,
        ], 200);
    }
}
