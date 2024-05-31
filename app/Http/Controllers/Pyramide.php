<?php

namespace App\Http\Controllers;

use App\Models\airesante;
use App\Models\commune;
use App\Models\province;
use App\Models\quartier;
use App\Models\SiteDeplaceModel;
use App\Models\territoir;
use App\Models\ville;
use App\Models\zonesante;
use Illuminate\Http\Request;
use App\Models\structureSanteModel;

class Pyramide extends Controller
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

    public function all_province_item()
    {
        return response()->json([
            "message" => "Liste des provinces!",
            "data" => province::with('territoir.zonesante.airesante')->get(),
            "code" => 200,
        ], 200);
    }

    public function territoirs_par_province($id)
    {
        $territoir = province::find($id);

        if ($territoir) {
            return response()->json([
                "message" => "territoir selon province!",
                "data" => province::with('territoir.province')->where('id', $territoir->id)->first(),
                "code" => 200,
            ], 200);
        } else {
            return response()->json([
                "message" => "Error",
                "data" => null,
                "code" => 404,
            ], 404);
        }
    }
    public function molo_up($id)
    {
        $airesante = airesante::find($id);

        if ($airesante) {
            return response()->json([
                "message" => "zone selon airesante!",
                "data" => airesante::with('zonesante.territoir.province')->where('id', $airesante->id)->first(),
                "code" => 200,
            ], 200);
        } else {
            return response()->json([
                "message" => "Error",
                "data" => null,
                "code" => 404,
            ], 404);
        }
    }

    public function listprovince()
    {
        $allprovince = province::all();
        return response()->json([
            "message" => "Liste des provinces!",
            "data" => $allprovince,
            "code" => 200,
        ], 200);
    }
    public function addterritoir(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'provinceid' => 'required',
        ]);

        $province = province::find($request->provinceid);
        if ($province) {
            if ($province->territoir()->where('name', $request->name)->exists()) {
                return response()->json([
                    "message" => 'Ce territoir existe déjà dans le système',
                    "data" => null,
                    "code" => 422
                ], 422);
            } else {
                $province->territoir()->create([
                    'name' => $request->name,
                    'provinceid' => $request->provinceid
                ]);
                return response()->json([
                    "message" => "Enregistrement avec succès!",
                    "code" => 200,
                    "data" => null,
                ], 200);
            }
        } else {
            return response()->json([
                "message" => "Erreur!",
                "code" => 404,
                "data" => null,
            ], 404);
        }
    }
    public function listterritoir($idpro)
    {
        $oneprovince = province::where('id', $idpro)->first();
        if ($oneprovince == null) {
            return response()->json([
                "message" => "Cette province n'existe  pas dans le système!",
                "data" => null,
                "code" => 422,
            ], 422);
        } else {
            $allter = territoir::where('provinceid', $oneprovince->id)->get();
            return response()->json([
                "message" => "Liste de territoirs!",
                "data" => $allter,
                "code" => 200,
            ], 200);
        }
    }
    public function addzone(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'territoirid' => 'required',
        ]);
        if (!zonesante::where('name', $request->name)->exists()) {
            $zone = zonesante::create([
                'name' => $request->name,
                'territoirid' => $request->territoirid,
            ]);
            return response()->json([
                "message" => "Liste zone",
                "code" => 200,
                "data" => zonesante::all(),
            ], 200);
        } else {
            return response()->json([
                "message" => "Cette zone existe déjà",
                "code" => 422,
                "data" => null,
            ], 422);
        }
    }

    public function listzone($id)
    {
        $oneterretoire = territoir::where('id', $id)->first();
        if ($oneterretoire == null) {
            return response()->json([
                "message" => "Ce territoire n'existe  pas dans le système!",
                "data" => null,
                "code" => 422,
            ], 422);
        } else {
            $allzone = zonesante::where('territoirid', $oneterretoire->id)->get();
            return response()->json([
                "message" => "Liste zone!",
                "data" => $allzone,
                "code" => 200,
            ], 200);
        }
    }

    public function addaire(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'zoneid' => 'required',

        ]);
        if (!airesante::where('name', $request->name)->exists()) {
            $zone = airesante::create([
                'name' => $request->name,
                'zoneid' => $request->zoneid,
                'nbr_population' => $request->nbr_population,
            ]);
            return response()->json([
                "message" => "Liste aires santé",
                "code" => 200,
                "data" => airesante::all(),
            ], 200);
        } else {
            return response()->json([
                "message" => "Cette information existe déjà!",
                "code" => 422,
                "data" => null,
            ], 422);
        }
    }
    public function listaire($id)
    {
        $idzone = zonesante::where('id', $id)->first();
        if ($idzone == null) {
            return response()->json([
                "message" => "Cette information n'existe  pas dans le système!",
                "data" => null,
                "code" => 422,
            ], 422);
        } else {
            $allaire = airesante::where('zoneid', $idzone->id)->get();
            return response()->json([
                "message" => "Liste aires santes!",
                "data" => $allaire,
                "code" => 200,
            ], 200);
        }
    }

    public function addstructure(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'aireid' => 'required',
            'contact' => 'required',
        ]);

        $airesante = airesante::find($request->aireid);
        if ($airesante) {
            if (structureSanteModel::where('name', $request->name)->exists()) {
                return response()->json([
                    "message" => 'Cette structure existe déjà dans le système',
                    "data" => null,
                    "code" => 422
                ], 422);
            } else {
                structureSanteModel::create([
                    'name' => $request->name,
                    'aireid' => $request->aireid,
                    'contact' => $request->contact,
                ]);
                return response()->json([
                    "message" => "Enregistrement avec succès!",
                    "code" => 200,
                    "data" => null,
                ], 200);
            }
        } else {
            return response()->json([
                "message" => "Erreur!",
                "code" => 404,
                "data" => null,
            ], 404);
        }
    }
    public function liststructure_par_aire($id)
    {
        $aire = airesante::where('id', $id)->first();
        if ($aire == null) {
            return response()->json([
                "message" => "Cette aire de santé n'existe  pas dans le système!",
                "data" => null,
                "code" => 422,
            ], 422);
        } else {
            $allstructure = structureSanteModel::where('aireid', $aire->id)->first();
            return response()->json([
                "message" => "Liste des structure de aire de santé :" . ($allstructure->name),
                "data" =>  structureSanteModel::where('aireid', $aire->id)->get(),
                "code" => 200,
            ], 200);
        }
    }

    public function create_site_deplace(Request $request){
           $request->validate([
                 'name' =>'required',
                 'aire_id' =>'required',
           ]);

           if(!SiteDeplaceModel::where('name', $request->name)->exists()){
            SiteDeplaceModel::create([
                'name' =>$request->name,
                'aire_id' =>$request->aire_id,
            ]);
            return response()->json([
                "message" => "success",
                "data" =>  SiteDeplaceModel::orderBy('name','asc')->get(),
                "code" => 200,
            ], 200);
           }else{
            return response()->json([
                "message" => "Ce site de place existe déjà dans le system!",
                "code" => 422,
            ], 422);
           }
    }

    public function get_site_deplace() {
        return response()->json([
            "message" => "success",
            "data" =>  SiteDeplaceModel::orderBy('name','asc')->get(),
            "code" => 200,
        ], 200);
    }
    public function All_structure()
    {
        return response()->json([
            "message" => "Liste des structures",
            "data" => structureSanteModel::all(),
            "code" => 200,
        ], 200);
    }
}
