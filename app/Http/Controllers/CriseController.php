<?php

namespace App\Http\Controllers;

use App\Models\AffectationModel;
use App\Models\AffectationPermission;
use App\Models\Permission;
use App\Models\TypeCrise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CriseController extends Controller
{
    public function AddCrise(Request $request)
    {
        $request->validate([
            'name' => 'required',
            "orgid" => "required",
        ]);
        $user = Auth::user();
        $permission = Permission::where('name', 'create_crise')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {

                $datacrise = TypeCrise::where('name', $request->name)->first();
                if ($datacrise) {
                    return response()->json([
                        "message" => "Ce type de crise existe déjà dans le système!",
                        "code" => 402
                    ], 402);
                } else {
                    TypeCrise::create([
                        'name' => $request->name,
                    ]);
                    return response()->json([
                        "message" => 'Traitement réussi avec succès!',
                        "code" => 200
                    ], 200);
                }
            } else {

                return response()->json([
                    "message" => "Vous ne pouvez pas éffectuer cette action",
                    "code" => 402
                ], 402);
            }
        } else {
            return response()->json([
                "message" => "cette organisationid" . $organisation->id . "n'existe pas",
                "code" => 402
            ], 402);
        }
    }

    public function UpdateCrise(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            "orgid" => "required",
        ]);
        $user = Auth::user();
        $permission = Permission::where('name', 'update_crise')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {
                $type = TypeCrise::find($id);
                if ($type) {
                    $type->name = $request->name;
                    $type->save();
                    return response()->json([
                        "message" => "La modification réussie"
                    ], 200);
                } else {
                    return response()->json([
                        "message" => "Erreur de la modification avec" . $request->id,
                    ], 422);
                }
            } else {

                return response()->json([
                    "message" => "Vous ne pouvez pas éffectuer cette action",
                    "code" => 402
                ], 402);
            }
        } else {
            return response()->json([
                "message" => "cette organisationid" . $organisation->id . "n'existe pas",
                "code" => 402
            ], 402);
        }
    }

    public function ListeCrise(Request $request)
    {

                return response()->json([
                    "message" => "Liste des crises",
                    "code" => "200",
                    "data" => TypeCrise::all(),
                ]);
         
    }
}
