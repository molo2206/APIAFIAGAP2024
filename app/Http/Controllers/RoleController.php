<?php

namespace App\Http\Controllers;

use App\Models\AffectationModel;
use App\Models\AffectationPermission;
use App\Models\Permission;
use App\Models\RoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{

    public function create(Request $request, $orgid)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $user = Auth::user();
        $permission = Permission::where('name', 'create_role')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {
                if ($user) {
                    if (!RoleModel::where('name', $request->name)->exists()) {
                        $role = RoleModel::create([
                            "name" => $request->name,
                        ]);
                        return response()->json([
                            "message" => "Création du role réussie"
                            
                        ], 200);
                    } else {
                        return response()->json([
                            "message" => "Cette informationexiste déjà dans le système : " . $request->name,
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

    public function updaterole(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'orgid' => 'required',
        ]);
        $user = Auth::user();
        $permission = Permission::where('name', 'update_role')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {
                if ($user) {
                    $role = RoleModel::find($id);
                    if ($role) {
                        $role->name = $request->name;
                        $role->save();
                        return response()->json([
                            "message" => "La modification du role réussie"
                        ], 200);
                    } else {
                        return response()->json([
                            "message" => "Erreur de la modification du role",
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
    public function deleterole(Request $request, $id)
    {
        $request->validate([
            'orgid' => 'required',
        ]);
        $user = Auth::user();
        $permission = Permission::where('name', 'delete_role')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {
                if ($user) {

                    $role = RoleModel::find($id);
                    if ($role) {
                        $role->deleted = 1;
                        $role->save();
                        return response()->json([
                            "message" => "Suppression du role réussie"
                        ], 200);
                    } else {
                        return response()->json([
                            "message" => "Erreur de la suppresion du role",
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

    public function list_roles($orgid)
    {
       
                    $allrole = RoleModel::where('deleted',0)->get();
                    return response()->json([
                        "message" => "Liste des roles!",
                        "data" => $allrole,
                        "code" => 200,
                    ], 200);
               
           
    }
}
