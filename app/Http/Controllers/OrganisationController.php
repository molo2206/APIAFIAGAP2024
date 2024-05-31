<?php

namespace App\Http\Controllers;

use App\Mail\Creationorg;
use App\Models\airesante;
use App\Models\indicateur;
use App\Models\org_indicateur;
use App\Models\Organisation;
use App\Models\typeorg;
use App\Models\zonesante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrganisationController extends Controller
{
    public  function AddOrg(Request $request)
    {
        $request->validate([
            "name"  => "required|string",
            "email" => "required|email",
            "phone" => "required|string",
            "description" => "required|string",
            "adresse" => "required|string",
            "sigle" => "required|string",
            "typeorgid" => "required|string",
        ]);
        if (typeorg::find($request->typeorgid)) {
            if (!Organisation::where('email', $request->email)->exists()) {
                if (!Organisation::where('phone', $request->phone)->exists()) {
                    if (!Organisation::where('name', $request->name)->exists()) {
                        $logo = UtilController::uploadImageUrl($request->logo, '/uploads/user/');

                        Organisation::create([
                            "name"  => $request->name, "email"  => $request->email,
                            "phone" => $request->phone, "description"  => $request->description,
                            "logo"  => $logo,
                            "adresse"  => $request->adresse,
                            "sigle" => $request->sigle,
                            "activite"  => null,
                            "pointfocal"  => null,
                            "typeorgid"  => $request->typeorgid,
                            "status" => 0,
                            "delete" => 0,
                        ]);
                        Mail::to($request->email)->send(new Creationorg($request->email, $request->name));
                        return response()->json([
                            "message" => "Cette organisation à été créée avec succès.",
                            "code" => 200,
                            "code" => 200,
                            "data" => Organisation::with('type_org')->orderBy('name', 'asc')->get(),
                        ], 200);
                    } else {
                        return response([
                            "message" => "Le nom de cette organisation existe dans le système!",
                            "code" => 422,
                            "data" => null,
                        ], 422);
                    }
                } else {
                    return response([
                        "message" => "Ce numèro phone existe dans le système!",
                        "code" => 422,
                        "data" => null,
                    ], 422);
                }
            } else {
                return response([
                    "message" => "Cette adresse email existe dans le système!",
                    "code" => 422,
                    "data" => null,
                ], 422);
            }
        } else {
            return response([
                "message" => "Ce type d'organisation n'existe pas dans le système!",
                "code" => 422,
                "data" => null,
            ], 422);
        }
    }
    public function updateorganisation(Request $request, $id)
    {
        $request->validate([
            "name"  => "required|string",
            "email" => "required|email",
            "phone" => "required|string",
            "description" => "required|string",
            "adresse" => "required|string",
            "sigle" => "required|string",
            "typeorgid" => "required|string",
            "logo" => "required",
        ]);
        $org = Organisation::where('id', $id)->first();
        if (!$org) {
            return response()->json([
                "message" => "Cette organisation n'existe pas!",
                "code" => 402,
            ], 402);
        } else {
            if ($request->logo) {
                $logo = UtilController::uploadImageUrl($request->logo, '/uploads/organ/');
                $org->name  = $request->name;
                $org->email = $request->email;
                $org->phone = $request->phone;
                $org->description = $request->description;
                $org->logo  = $logo;
                $org->adresse = $request->adresse;
                $org->sigle = $request->sigle;
                $org->typeorgid  = $request->typeorgid;
                $org->update();
                Mail::to($request->email)->send(new Creationorg($request->email, $request->name));
                return response()->json([
                    "message" => "Cette organisation à été modifier avec succès.",
                    "code" => 200,
                    "data" => Organisation::with('type_org')->orderBy('name', 'asc')->get(),
                ], 200);
            } else {
                return response()->json([
                    "message" => "Inserer le logo svp!",
                    "code" => 402,
                ], 402);
            }
        }
    }
    public function list_organisation()
    {
        return response([
            "message" => "Liste des organisations",
            "code" => 200,
            "data" => Organisation::with('type_org')->orderBy('name', 'asc')->get(),
        ]);
    }
    public function addindicateur(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'value' => 'required',
        ]);
        if (indicateur::where('name', $request->name)->exists()) {
            return response([
                "message" => "Cet indicateur existe déjà dans le système!",
                "code" => 422,
                "data" => null,
            ], 422);
        } else {
            $addind = indicateur::create([
                'name' => $request->name,
                'value' => $request->value,
                'status' => 0
            ]);
            return response()->json([
                "message" => "Enregistrement avec succès!",
                "code" => 200,
                "data" => indicateur::all(),
            ], 200);
        }
    }
    public function listeIndicateur()
    {
        $allindicateur = indicateur::all();
        return response()->json([
            "code" => 200,
            "message" => "Liste des indicateurs!",
            "data" => $allindicateur,

        ], 200);
    }

    public function org_indicateur(Request $request)
    {
        $request->validate([
            "orgid" => 'required',
            "indicateurid" => 'required',
        ]);
        if (indicateur::where('id', $request->indicateurid)->exists()) {
            if (Organisation::where('id', $request->indicateurid)->exists()) {
                $addorg_ind = org_indicateur::create([
                    'orgid' => $request->orgid,
                    'indicateurid' => $request->indicateurid,
                    'status' => 0,
                    'delete' => 0
                ]);
                return response()->json([
                    "message" => "Enregistrement avec succès!",
                    "code" => 200,
                    "data" => org_indicateur::all(),
                ], 200);
            } else {
                return response([
                    "message" => "Cette organisation n'existe pas dans le système!",
                    "code" => 422,
                    "data" => null,
                ]);
            }
        } else {
            return response([
                "message" => "Cette indicateur n'existe pas dans le système!",
                "code" => 422,
                "data" => null,
            ]);
        }
    }

    public function addtypeorg(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        if (typeorg::where('name', $request->name)->exists()) {
            return response([
                "message" => "Ce type d'organisation existe dans le système!",
                "code" => 422,
                "data" => null,
            ]);
        } else {
            $addtype = typeorg::create([
                'name' => $request->name,
            ]);
            return response()->json([
                "message" => "Enregistrement avec succès!",
                "code" => 200,
                "data" => typeorg::all(),
            ], 200);
        }
    }

    public function listtype()
    {
        return response()->json([
            "message" => "Liste type org",
            "code" => 200,
            "data" => typeorg::all(),
        ], 200);
    }
}
