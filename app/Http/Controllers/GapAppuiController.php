<?php

namespace App\Http\Controllers;

use App\Mail\PositionCustomer;
use App\Mail\PositionnementGap;
use App\Models\AffectationModel;
use App\Models\AffectationPermission;
use App\Models\GapAppuiModel;
use App\Models\GapsModel;
use App\Models\Organisation;
use App\Models\Permission;
use App\Models\PositiontionPartenaireModel;
use App\Models\structureSanteModel;
use App\Models\TypeGapModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
class GapAppuiController extends Controller
{
     public function PositionnementPartenaire(Request $request)
    {
        $request->validate([
            'gap_apuis_id' => 'required',
            'date_approxitive_debut_appui' => 'required',
            'message' => 'required',
            'type_paiement' => 'required',
        ]);

        $user = Auth::user();
        foreach ($request->gap_apuis_id as $item) {
            if (PositiontionPartenaireModel::where('gap_apuis_id', $item)->where('userid', $user->id)->where('status',1)->first() == null) {
                $gap_appui = GapAppuiModel::find($item['id_appui']);
                $gap = GapsModel::find($gap_appui->gapid);
                $structure = structureSanteModel::find($gap->orgid);
                $position = PositiontionPartenaireModel::create([
                    'gap_apuis_id' => $item['id_appui'],
                    'date_approxitive_debut_appui' => $request->date_approxitive_debut_appui,
                    'budget_disponible' => $request->budget_disponible,
                    'userid' => $user->id,
                    'type_paiement' => $request->type_paiement,
                    'message' => $request->message,
                    'devise' => $request->devise,
                    'qte' => $item['qte'],
                    'status' => 1
                ]);
            } else {
                return response()->json([
                    "message" => "Vous avez déjà séléctionner cette information dans cette structure de santé,c'est encours de traitement. Sinon vous pouvez séléctionner une autre!",
                    "code" => 402
                ], 402);
            }
        }

        $datagap_appui = PositiontionPartenaireModel::with('gap_appuis.gap.datastructure')
            ->where('userid', $user->id)->get();
        $email = "info@cosamed.org";
        Mail::to($user->email)->send(new PositionCustomer($user->email,$user->full_name));
        Mail::to($email)->send(new PositionnementGap(
            $user->email,
            $user->phone,
            $user->full_name,
            $request->qte,
            $structure->name,
            $gap_appui->gapid,
            $gap->id,
            $datagap_appui,
            $request->budget_disponible,
            $request->devise,
            $request->date_approxitive_debut_appui
        ));
        return response()->json([
            "message" => 'Prise de positionnement avec succès!',
            "status" => 200,
            "data" => $datagap_appui
        ], 200);
    }
    public function create_gap_appui(Request $request, $gapid)
    {

        $request->validate([
            'gap_appuis' => 'required',
            'orgid' => 'required',
        ]);

        $user = Auth::user();
        $namepermission = 'valide_gap';
        $permission = Permission::where('name', $namepermission)->first();
        $organisation = Organisation::find($request->orgid);
        if ($organisation) {
            if ($permission) {
                $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
                $permission_valide_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
                    ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
                if ($permission_valide_gap) {
                    $datagap = GapsModel::where('id', $gapid)->where('status', 1)->first();
                    if ($datagap) {
                        $datagap->gap_appui()->detach();
                        foreach ($request->gap_appuis as $item) {
                            if (TypeGapModel::where('name', $item['key'])) {
                                TypeGapModel::create([
                                    'name' => $item['key'],
                                ]);
                                $datagap->gap_appui()->attach([$datagap->id =>
                                [
                                    'key' => $item['key'],
                                    'value' => $item['value'],
                                ]]);
                            } else {
                                $datagap->gap_appui()->attach([$datagap->id =>
                                [
                                    'key' => $item['key'],
                                    'value' => $item['value'],
                                ]]);
                            }
                        }
                        return response()->json([
                            "message" => "success",
                            "code" => 200,
                            "data" => GapsModel::with(
                                'datauser',
                                'suite1.suite2',
                                'dataprovince',
                                'dataterritoir',
                                'datazone',
                                'dataaire',
                                'datastructure',
                                'datapopulationEloigne',
                                'datamaladie.maladie',
                                'allcrise.crise',
                                'datamedicament.medicament',
                                'datapartenaire.partenaire.allindicateur.paquetappui',
                                'datatypepersonnel.typepersonnel',
                                'datascorecard.dataquestion.datarubrique',
                                'images',
                                'gap_appuis'
                            )->where('userid', $user->id)->where('id', $datagap->id)->where('orguserid', $request->orgid)->where('status', 1)->first(),
                        ], 200);
                    } else {
                        return response()->json([
                            "message" => "Ce gapid n'est pas reconnue dans le système!",
                            "code" => 402,
                        ], 402);
                    }
                } else {
                    return response()->json([
                        "message" => "Vous ne pouvez pas éffectuer cette action"
                    ], 402);
                }
            } else {
                return response()->json([
                    "message" => "cette permission" . $permission->name . "n'existe pas",
                    "code" => 402
                ], 402);
            }
        } else {
            return response()->json([
                "message" => "cette organisation" . $organisation->name . "n'existe pas",
                "code" => 402
            ], 402);
        }
    }

    public function get_type_gap()
    {
        return response()->json([
            "message" => "Liste type gaps",
            "code" => 200,
            "data" => TypeGapModel::get(),
        ]);
    }
    public function add_type_gap(Request $request)
    {

        $request->validate([
            "name" => 'required'
        ]);

        if (TypeGapModel::where('name', $request->name)->exists()) {
            return response()->json([
                "message" => "cette type de gap n'est pas reconnue dans le système",
                "code" => 402
            ], 402);
        } else {
            TypeGapModel::create([
                "name" => $request->name,
            ]);
            return response()->json([
                "message" => "success",
                "code" => 200,
                "data" => TypeGapModel::get(),
            ], 200);
        }
    }
}
