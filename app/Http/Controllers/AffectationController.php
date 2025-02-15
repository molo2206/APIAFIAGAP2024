<?php

namespace App\Http\Controllers;

use App\Models\AffectationModel;
use App\Models\AffectationPermission;
use App\Models\Organisation;
use App\Models\Permission;
use App\Models\Type_users;
use App\Models\User;
use App\Models\User_has_Type;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AffectationController extends Controller
{
    public function Affectation(Request $request)
    {
        $request->validate([
            'userid' => 'required',
            'orgid' => 'required',
            'orgid_affect' => 'required',
            'permissionid' => 'required',
        ]);
        $user = Auth::user();
        if ($user->checkPermissions('Affectation', 'create')) {
            $affectation = AffectationModel::where('userid', $request->userid)
                ->where('orgid', $request->orgid_affect)->first();
            if ($affectation) {
                $affectation->orgid = $request->orgid_affect;
                $affectation->userid = $request->userid;
                $affectation->save();
                $affectation->permission()->detach();
                foreach ($request->permissions as $item) {
                    $affectation->permission()->attach([$item['permissionid'] =>
                    [
                        'create' => $item['create'],
                        'update' => $item['update'],
                        'delete' => $item['delete'],
                        'read'   => $item['read'],
                        'status' => $item['status'],
                    ]]);
                }
                Log::channel(channel: 'slack')->critical($affectation);
                return response()->json([
                    "message" => "Affctation réussie avec succèss",
                    "data" => AffectationModel::with('user.typeUser', 'organisation')->where('userid', $request->userid)->where('orgid', $request->orgid_affect)->first()
                ], 200);
            } else {
                if (Organisation::where('id', $request->orgid_affect)->first()) {
                    if (User::where('id', $request->userid)->first()) {
                        $aff = AffectationModel::create([
                            'orgid' => $request->orgid_affect,
                            'userid' => $request->userid,
                        ]);
                        $aff->permission()->detach();
                        foreach ($request->permissions as $item) {
                            $aff->permission()->attach([$item['permissionid'] =>
                            [
                                'create' => $item['create'],
                                'update' => $item['update'],
                                'delete' => $item['delete'],
                                'read'   => $item['read'],
                                'status' => $item['status'],
                            ]]);
                        }
                        Log::channel(channel: 'slack')->critical($aff);
                        $type =  Type_users::where('name', 'admin')->first();
                        $user = User::where('id', $aff->userid)->first();
                        if (!$user->typeUser()->where('name', 'admin')->exists()) {
                            User_has_Type::create([
                                'userid' => $aff->userid,
                                'typeid' => $type->id,
                            ]);
                        }

                        return response()->json([
                            "message" => "Traitement réussie avec succès",
                            "data" => AffectationModel::with('user.typeUser', 'organisation')->where('userid', $request->userid)->where('orgid', $request->orgid_affect)->first()
                        ], 200);
                    } else {
                        return response()->json([
                            "message" => "C'est utilisateur n'existe pas dans le système",
                        ], 422);
                    }
                } else {
                    return response()->json([
                        "message" => "Cette organisation n'existe pas dans le système ",
                    ], 422);
                }
            }
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function create_permission(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'orgid' => 'required',
        ]);

        $user = Auth::user();
        if (Permission::where('name', $request->name)->exists()) {
            return response()->json([
                "message" => "Cette permission existe déjà dans le système!",
            ], 422);
        } else {
            Permission::create([
                'name' => $request->name,
            ]);
            return response()->json([
                "message" => "Création réussie avec succès!",
                "code" => 200,
                "data" => Permission::where('deleted', 0)->where('status', 1)->orderBy('name', 'asc')->get(),
            ], 200);
        }
    }

    public function update_permission(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'orgid' => 'required',
        ]);
        $user = Auth::user();
        $permission = Permission::find($id);
        if ($permission) {
            $permission->name = $request->name;
            $permission->save();
            return response()->json([
                "message" => "La modification de la permission réussie!",
                "code" => 200,
                "data" => Permission::where('deleted', 0)->where('status', 1)->orderBy('name', 'asc')->get(),
            ], 200);
        } else {
            return response()->json([
                "message" => "Erreur de la modification permission",
            ], 422);
        }
    }

    public function delete_permission(Request $request, $id)
    {
        $request->validate([
            "orgid" => 'required'
        ]);
        $user = Auth::user();

        $perm = Permission::where('id', $id)->where('status', 1)->where('deleted', 0)->first();
        if ($perm) {
            $perm->deleted = 1;
            $perm->save();
            return response()->json([
                "message" => 'Liste des permissions',
                "code" => 200,
                "data" => Permission::where('deleted', 0)->orderBy('name', 'asc')->get(),
            ]);
        } else {
            return response()->json([
                "message" => 'Cette identifiant est erronné dans le système!',
                "code" => 402,
            ], 402);
        }
    }

    public function list_permissions()
    {
        return response()->json([
            "message" => "Liste des permissions",
            "data" => Permission::where('status', 1)->orderBy('name', 'asc')->where('deleted', 0)->get(),
        ], 200);
    }

    public function RetirerAcces(Request $request)
    {
        $request->validate([
            'idaffect_perm' => 'required',
            'orgid' => 'required'
        ]);

        $user = Auth::user();
        $permission = Permission::where('name', 'retire_acces')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {
                foreach ($request->idaffect_perm as $item) {
                    $affectationpermission = AffectationPermission::find($item);
                    if ($affectationpermission) {
                        $affectationpermission->deleted = 1;
                        $affectationpermission->delete();
                    }
                }
                return response()->json([
                    "message" => "Permission rétirée avec succès",
                ], 200);
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

    public function affecterPermission(Request $request)
    {
        $request->validate([
            'affectationid' => 'required',
            'permissionid' => 'required',
            'orgid' => 'required',
        ]);
        $user = Auth::user();

        $affectation = AffectationModel::where('id', $request->affectationid)->first();
        if ($affectation) {
            $affectation->affectationpermission()->detach();
            foreach ($request->permissionid as $item) {
                $affectation->affectationpermission()->attach([
                    $affectation->id => [
                        'affectationid' => $request->affectationid,
                        'permissionid' => $item,
                    ]
                ]);
            }
            Log::channel(channel: 'slack')->critical($affectation);
            $type =  Type_users::where('name', 'admin')->first();
            $user = User::where('id', $affectation->userid)->first();

            if (!$user->typeUser()->where('name', 'admin')->exists()) {
                User_has_Type::create([
                    'userid' => $user->id,
                    'typeid' => $type->id,
                ]);
            }

            return response()->json([
                "message" => "Permission accordée",
            ], 200);
        } else {

            return response()->json([
                "message" => "Vous devez d'abord etre affecter",
            ], 422);
        }
    }

    public function List_PermissionsAccordees($orgid)
    {
        $user = Auth::user();
        $permission = Permission::where('name', 'view_permission')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {
                return response()->json([
                    "code" => "200",
                    "message" => "Liste des permissions",
                    "data" => AffectationPermission::with('permission')->get(),
                ], 200);
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

    public function permission(Request $request)
    {
        $request->validate([
            'userid' => 'required',
            'orgid' => 'required',
            'orgid_affect' => 'required',
            'permissions' => 'required',
        ]);

        $user = Auth::user();
        if ($user->checkPermissions('Affectation', 'create')) {
            $affectation = AffectationModel::where('userid', $request->userid)
                ->where('orgid', $request->orgid_affect)->first();
            if ($affectation) {
                $affectation->orgid = $request->orgid_affect;
                $affectation->userid = $request->userid;
                $affectation->save();
                $affectation->permission()->detach();
                foreach ($request->permissions as $item) {
                    $affectation->permission()->attach([$item['permissionid'] =>
                    [
                        'create' => $item['create'],
                        'update' => $item['update'],
                        'delete' => $item['delete'],
                        'read'   => $item['read'],
                        'status' => $item['status'],
                    ]]);
                }
                Log::channel(channel: 'slack')->critical($affectation);
                return response()->json([
                    "message" => "Permission accordée avec succès!",
                    "code" => 200,
                ], 200);
            } else {
                if (Organisation::where('id', $request->orgid_affect)->first()) {
                    if (User::where('id', $request->userid)->first()) {
                        $aff = AffectationModel::create([
                            'orgid' => $request->orgid_affect,
                            'userid' => $request->userid,
                        ]);
                        $aff->permission()->detach();
                        foreach ($request->permissions as $item) {
                            $aff->permission()->attach([$item['permissionid'] =>
                            [
                                'create' => $item['create'],
                                'update' => $item['update'],
                                'delete' => $item['delete'],
                                'read'   => $item['read'],
                                'status' => $item['status'],
                            ]]);
                        }
                        Log::channel(channel: 'slack')->critical($aff);
                        $type =  Type_users::where('name', 'admin')->first();
                        $user = User::where('id', $aff->userid)->first();
                        if (!$user->typeUser()->where('name', 'admin')->exists()) {
                            User_has_Type::create([
                                'userid' => $aff->userid,
                                'typeid' => $type->id,
                            ]);
                        }

                        return response()->json([
                            "message" => "Permission accordée avec succès!",
                            "code" => 200,
                        ], 200);
                    } else {
                        return response()->json([
                            "message" => "C'est utilisateur n'existe pas dans le système",
                        ], 422);
                    }
                } else {
                    return response()->json([
                        "message" => "Cette organisation n'existe pas dans le système ",
                    ], 422);
                }
            }
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function updatepermission(Request $request,$id)
    {
        $request->validate([
            'userid' => 'required',
            'orgid' => 'required',
            'orgid_affect' => 'required',
            'permissions' => 'required',
        ]);

        $user = Auth::user();
        if ($user->checkPermissions('Affectation', 'create')) {
            $affectation = AffectationModel::where('id',$id)->first();
            if ($affectation) {
                $affectation->orgid = $request->orgid_affect;
                $affectation->save();
                $affectation->permission()->detach();
                foreach ($request->permissions as $item) {
                    $affectation->permission()->attach([$item['permissionid'] =>
                    [
                        'create' => $item['create'],
                        'update' => $item['update'],
                        'delete' => $item['delete'],
                        'read'   => $item['read'],
                        'status' => $item['status'],
                    ]]);
                }
                Log::channel(channel: 'slack')->critical($affectation);
                return response()->json([
                    "message" => "Permission accordée avec succès!",
                    "code" => 200,
                ], 200);
            }
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }
}
