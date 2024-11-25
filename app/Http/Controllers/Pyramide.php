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
use Illuminate\Support\Facades\Auth;

class Pyramide extends Controller
{
    public function addprovince(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $user = Auth::user();
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

    public function updateprovince(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $province = province::find($id);
        if ($province) {
            $province->update([
                'name' => $request->name,
            ]);
            return response()->json([
                "message" => "Mise à jour avec succès!",
                "code" => 200,
                "data" => province::all(),
            ], 200);
        } else {
            return response()->json([
                "message" => "Province introuvable!",
                "data" => null,
                "code" => 404,
            ], 404);
        }
    }
    public function deleteProvince($id)
    {
        $province = province::find($id);
        if ($province) {
            $province->deleted = 1;
            $province->save();
            return response()->json([
                "message" => "Province supprimée avec succès!",
                "data" => province::all(),
                "code" => 200,
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
        $allprovince = province::where('status', 1)->where('deleted', 0)->get();
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
    public function updateTerritoir(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'provinceid' => 'required',
        ]);
        $territoir = territoir::find($id);
        if ($territoir) {
            $province = province::find($request->provinceid);
            if ($province) {
                $territoir->update([
                    'name' => $request->name,
                    'provinceid' => $request->provinceid
                ]);
                return response()->json([
                    "message" => "Territoir mise à jour avec succès!",
                    "code" => 200,
                ], 200);
            } else {
                return response()->json([
                    "message" => "Erreur!",
                    "code" => 404,
                    "data" => null,
                ], 404);
            }
        }
    }
    public function deleteTerritoir($id)
    {

        $territoir = territoir::find($id);
        if ($territoir) {
            $territoir->deleted = 1;
            $territoir->save();
            return response()->json([
                "message" => "Territoir supprimé avec succès!",
                "code" => 200,
            ], 200);
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
            $allter = territoir::where('provinceid', $oneprovince->id)
                ->where('status', 1)->where('deleted', 0)->get();
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
    public function updateZone(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'territoirid' => 'required',
        ]);
        $zone = zonesante::find($id);
        $territoir = territoir::find($request->territoirid);
        if ($zone) {
            if ($territoir) {
                $zone->update([
                    'name' => $request->name,
                    'territoirid' => $request->territoirid,
                ]);
                return response()->json([
                    "message" => "Mise à jour avec succès!",
                    "code" => 200,
                ], 200);
            } else {
                return response()->json([
                    "message" => "Ce territoire n'existe pas",
                    "code" => 404,
                    "data" => null,
                ], 404);
            }
        }
    }
    public function deleteZone($id)
    {
        $zone = zonesante::find($id);
        if ($zone) {
            $zone->deleted = 1;
            $zone->save();
            return response()->json([
                "message" => "Zone supprimée avec succès!",
                "code" => 200,
            ], 200);
        } else {
            return response()->json([
                "message" => "Cette zone n'existe pas",
                "code" => 404,
            ], 404);
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
    public function updateAire(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'zoneid' => 'required',
            'nbr_population' => 'required',
        ]);
        $aire = airesante::find($id);
        $zone = zonesante::find($request->zoneid);
        if ($aire) {
            if ($zone) {
                $aire->update([
                    'name' => $request->name,
                    'zoneid' => $request->zoneid,
                    'nbr_population' => $request->nbr_population,
                ]);
                return response()->json([
                    "message" => "Mise à jour avec succès!",
                    "code" => 200,
                ], 200);
            } else {
                return response()->json([
                    "message" => "Cette zone de santé n'existe pas!",
                    "code" => 404,
                ], 40);
            }
        } else {
            return response()->json([
                "message" => "Cette aire de santé n'existe pas!",
                "code" => 404,
            ], 404);
        }
    }
    public function deleteAire($id)
    {
        $aire = airesante::find($id);
        if ($aire) {
            $aire->deleted = 1;
            $aire->save();
            return response()->json([
                "message" => "Aire de santé supprimée avec succès!",
                "code" => 200,
            ], 200);
        } else {
            return response()->json([
                "message" => "Cette aire de santé n'existe pas!",
                "code" => 404,
            ], 404);
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
                $structure = structureSanteModel::create([
                    'name' => $request->name,
                    'aireid' => $request->aireid,
                    'contact' => $request->contact,
                    'type_id' => $request->type,
                ]);
                return response()->json([
                    "message" => "Enregistrement avec succès!",
                    "code" => 200,
                    "data" => structureSanteModel::with('typestructure')
                        ->where('id', $structure->id)->first(),
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
    public function updatestructure(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'aireid' => 'required',
            'contact' => 'required',
        ]);

        $structure = structureSanteModel::where('id', $id)->first();
        $airesante = airesante::find($request->aireid);
        if ($airesante) {
            if (!$structure) {
                return response()->json([
                    "message" => 'Cette structure existe déjà dans le système!',
                    "data" => null,
                    "code" => 422
                ], 422);
            } else {
                $structure->name = $request->name;
                $structure->aireid = $airesante->id;
                $structure->contact = $request->contact;
                $structure->type_id = $request->type;
                $structure->update();
                return response()->json([
                    "message" => "Modification réussie avec succès!",
                    "code" => 200,
                    "data" => structureSanteModel::with('typestructure')
                        ->where('id', $structure->id)->first(),
                ], 200);
            }
        } else {
            return response()->json([
                "message" => "Aire de santé n'existe pas!",
                "code" => 404,
                "data" => null,
            ], 404);
        }
    }

    public function deleteStructure($id)
    {
        $structure = structureSanteModel::find($id);
        if ($structure)
        {
            $structure->deleted = 1;
            $structure->save();

            return response()->json([
                "message" => "Structure supprimée avec succès!",
                "code" => 200,
            ], 200);
        } else {
            return response()->json([
                "message" => "Cette structure n'existe pas!",
                "code" => 404,
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

    public function create_site_deplace(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'aire_id' => 'required',
        ]);

        if (!SiteDeplaceModel::where('name', $request->name)->exists()) {
            SiteDeplaceModel::create([
                'name' => $request->name,
                'aire_id' => $request->aire_id,
            ]);
            return response()->json([
                "message" => "success",
                "data" =>  SiteDeplaceModel::orderBy('name', 'asc')->get(),
                "code" => 200,
            ], 200);
        } else {
            return response()->json([
                "message" => "Ce site de place existe déjà dans le system!",
                "code" => 422,
            ], 422);
        }
    }

    public function get_site_deplace()
    {
        return response()->json([
            "message" => "success",
            "data" =>  SiteDeplaceModel::orderBy('name', 'asc')->get(),
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
