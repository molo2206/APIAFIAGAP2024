<?php

namespace App\Http\Controllers;

use App\Models\AffectationModel;
use App\Models\AffectationPermission;
use App\Models\airesante;
use App\Models\AlertModel;
use App\Models\Bloc2Model;
use App\Models\Bloc3Model;
use App\Models\Crise_Gap;
use App\Models\GapAppuiModel;
use App\Models\GapsModel;
use App\Models\ImageGapModel;
use App\Models\MaladiedGap;
use App\Models\MedicamentRupture;
use App\Models\Notifications;
use App\Models\org_indicateur;
use App\Models\Organisation;
use App\Models\PartenairePresntModel;
use App\Models\Permission;
use App\Models\PersonnelGap;
use App\Models\PersonnelModel;
use App\Models\PopulationEloigne;
use App\Models\province;
use App\Models\PublicationsModel;
use App\Models\territoir;
use App\Models\TypeCrise;
use App\Models\zonesante;
use App\Models\structureSanteModel;
use App\Models\Tags;
use App\Models\TokenUsers;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GapsController extends Controller
{
    public function AddGap(Request $request)
    {

        $request->validate([
            // Bloc1
            'provinceid' => 'required',
            'territoirid' => 'required',
            'zoneid' => 'required',
            'airid' => 'required',
            'structureid' => 'required',
            'dateadd' => 'required',
            'orgid' => 'required',
        ]);
        $datagap = GapsModel::where('orgid', $request->structureid)->orderBy('dateadd', 'desc')->first();
        if ($datagap == null) {
            $province = province::find($request->provinceid);
            $territoir = territoir::find($request->territoirid);
            $zone = zonesante::find($request->zoneid);
            $aire = airesante::find($request->airid);
            $structure = structureSanteModel::find($request->structureid);

            $date = date('d/m/y');
            $timestamp = date('H:i:s');

            $user = Auth::user();

            $permission = Permission::where('name', 'create_gap')->first();
            $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
            if ($organisation) {
                if ($permission) {
                    $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
                    $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
                        ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
                    if ($permission_gap) {
                        if ($province) {
                            if ($territoir) {
                                if ($zone) {
                                    if ($aire) {
                                        if ($structure) {
                                            $bloc1 = GapsModel::create([
                                                'title' => $date . ' ' .
                                                    $timestamp . '/' . $province->name . '/' . $territoir->name . '/' . $zone->name . '/' . $aire->name . '/' . $structure->name,
                                                'provinceid' => $request->provinceid,
                                                'territoirid' => $request->territoirid,
                                                'zoneid' => $request->zoneid,
                                                'airid' => $request->airid,
                                                'orgid' => $request->structureid,
                                                'population' => $request->population,
                                                'pop_deplace' => $request->pop_deplace,
                                                'pop_retourne' => $request->pop_retourne,
                                                'pop_site' => $request->pop_site,
                                                'userid' => $user->id,
                                                'semaine_epid' => $request->semaine_epid,
                                                'annee_epid' => $request->annee_epid,
                                                "dateadd" => $request->dateadd,
                                                "orguserid" => $request->orgid
                                            ]);

                                            $bloc2 = Bloc2Model::create([
                                                'bloc1id' => $bloc1->id,
                                                'etat_infra' => $request->etat_infra,
                                                'equipement' => $request->equipement,
                                                'nbr_lit' => $request->nbr_lit,
                                                'taux_occupation' => $request->taux_occupation,
                                                'nbr_reco' => $request->nbr_reco,
                                                'pop_eloigne' => $request->pop_eloigne,
                                                'pop_vulnerable' => $request->pop_vulnerable,
                                            ]);

                                            Bloc3Model::create([
                                                'bloc2id' => $bloc2->id,
                                                'cout_ambulatoire' => $request->cout_ambulatoire,
                                                'cout_hospitalisation' => $request->cout_hospitalisation,
                                                'cout_accouchement' => $request->cout_accouchement,
                                                'cout_cesarienne' => $request->cout_cesarienne,
                                                'barriere' => $request->barriere,
                                                'pop_handicap' => $request->pop_handicap,
                                                'couvertureDtc3' => $request->couvertureDtc3,
                                                'mortaliteLessfiveyear' => $request->mortaliteLessfiveyear,
                                                'covid19_nbrcas' => $request->covid19_nbrcas,
                                                'covid19_nbrdeces' => $request->covid19_nbrdeces,
                                                'covid19_nbrtest' => $request->covid19_nbrtest,
                                                'covid19_vacciDispo' => $request->covid19_vacciDispo,
                                                'pourcentCleanWater' => $request->pourcentCleanWater,
                                                'malnutrition' => $request->malnutrition,
                                            ]);

                                            //INSERTION DE CAS DE MALADIES
                                            $gap = GapsModel::where('id', $bloc1->id)->first();
                                            if ($gap) {
                                                $gap->maladiegap()->detach();
                                                foreach ($request->datamaladie as $item) {
                                                    $gap->maladiegap()->attach([$bloc1->id =>
                                                    [
                                                        'maladieid' => $item['maladieid'],
                                                        'nbrCas' => $item['nbrCas'],
                                                        'nbrDeces' => $item['nbrDeces'],
                                                    ]]);
                                                }
                                            }

                                            // INSERTION MEDICAMENT EN RUPTURE
                                            if ($gap) {
                                                $gap->medicamentrupture()->detach();
                                                foreach ($request->datamedocid as $item) {
                                                    $gap->medicamentrupture()->attach([$bloc1->id =>
                                                    [
                                                        'medocid' => $item,
                                                    ]]);
                                                }
                                            }

                                            //INSERTION PARTENAIRE PRESENT

                                            if ($gap) {
                                                $gap->partenairegap()->detach();
                                                foreach ($request->datapartenaireid as $item) {
                                                    $gap->partenairegap()->attach([$bloc1->id =>
                                                    [
                                                        'orgid' => $item['orgid'],
                                                        'contact_point_facal' => $item['email'],
                                                        'date_debut' => $item['date_debut'],
                                                        'date_fin' => $item['date_fin'],
                                                    ]]);
                                                }
                                            }

                                            //INSERTION INDICATEURS PARTENAIRE PRESENT
                                            if ($gap) {
                                                $gap->indicateurgap()->detach();
                                                foreach ($request->datapartenaireid as $item) {
                                                    foreach ($item["datatindicateur"] as $items) {
                                                        $gap->indicateurgap()->attach([$bloc1->id =>
                                                        [
                                                            'orgid' => $item['orgid'],
                                                            'indicateurid' => $items,
                                                        ]]);
                                                    }
                                                }
                                            }


                                            //INSERTION TYPE PERSONNELS
                                            if ($gap) {
                                                $gap->typepersonnelgap()->detach();
                                                foreach ($request->datatypepersonnel as $item) {
                                                    $gap->typepersonnelgap()->attach([$bloc1->id =>
                                                    [
                                                        'personnelid' => $item['typepersonnelid'],
                                                        'nbr' => $item['nbr'],
                                                    ]]);
                                                }
                                            }

                                            //INSERTION CRISE GAP
                                            if ($gap) {
                                                $gap->crisegap()->detach();
                                                foreach ($request->datacriseid as $item) {
                                                    $gap->crisegap()->attach([$bloc1->id =>
                                                    [
                                                        'criseid' => $item,
                                                    ]]);
                                                }
                                            }

                                            //INSERTION POPULATION ELOIGNE GAP
                                            if ($gap) {
                                                $gap->populationeloignegap()->detach();
                                                foreach ($request->datapopulationeloigne as $item) {
                                                    $gap->populationeloignegap()->attach([$bloc1->id =>
                                                    [
                                                        'localite' => $item['localite'],
                                                        'nbr' => $item['nbr'],
                                                    ]]);
                                                }
                                            }
                                            //INSERTION IMAGES GAP
                                            if ($bloc1) {

                                                if ($request->images) {
                                                    foreach ($request->images as $item) {
                                                        $image = UtilController::uploadMultipleImage($item, '/uploads/gap/');
                                                        $bloc1->imagesgap()->attach([$bloc1->id =>
                                                        [
                                                            'image' => $image,
                                                        ]]);
                                                    }
                                                }
                                            }
                                            // if ($user->checkPermission('create_gap')) {

                                            // }
                                            // $dataToken_for_user = TokenUsers::all();
                                            // foreach ($dataToken_for_user as $item) {
                                            //     PushNotification::sendPushNotification($item->token, $request->title, $request->content, $image);
                                            // }

                                            return response()->json([
                                                "message" => 'Traitement réussi avec succès!',
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
                                                )->where('id', $bloc1->id)->orderBy('created_at', 'desc')->where('userid', $user->id)->where('orguserid', $request->orgid)->where('status', 0)->where('deleted', 0)->first(),
                                            ], 200);
                                        } else {
                                            return response()->json([
                                                "message" => "structureid not found "
                                            ], 402);
                                        }
                                    } else {
                                        return response()->json([
                                            "message" => "aireid not found "
                                        ], 402);
                                    }
                                } else {
                                    return response()->json([
                                        "message" => "zoneid not found "
                                    ], 402);
                                }
                            } else {
                                return response()->json([
                                    "message" => "territoirid not found "
                                ], 402);
                            }
                        } else {
                            return response()->json([
                                "message" => "provinceid not found "
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
                        "message" => "cette permission" . $permission->name . "n'existe pas",
                        "code" => 402
                    ], 402);
                }
            } else {
                return response()->json([
                    "message" => "cette organisationid" . $organisation->id . "n'existe pas",
                    "code" => 402
                ], 402);
            }
        } else {
            $fdate = $request->dateadd;
            $tdate = $datagap->dateadd;
            $datetime1 = new DateTime($fdate);
            $datetime2 = new DateTime($tdate);
            $interval = $datetime1->diff($datetime2);
            $days = $interval->format('%a');

            if ($days > 30) {
                $province = province::find($request->provinceid);
                $territoir = territoir::find($request->territoirid);
                $zone = zonesante::find($request->zoneid);
                $aire = airesante::find($request->airid);
                $structure = structureSanteModel::find($request->structureid);

                $date = date('d/m/y');
                $timestamp = date('H:i:s');

                $user = Auth::user();

                $permission = Permission::where('name', 'create_gap')->first();
                $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
                if ($organisation) {
                    if ($permission) {
                        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
                        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
                            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
                        if ($permission_gap) {
                            if ($province) {
                                if ($territoir) {
                                    if ($zone) {
                                        if ($aire) {
                                            if ($structure) {
                                                $bloc1 = GapsModel::create([
                                                    'title' => $date . ' ' .
                                                        $timestamp . '/' . $province->name . '/' . $territoir->name . '/' . $zone->name . '/' . $aire->name . '/' . $structure->name,
                                                    'provinceid' => $request->provinceid,
                                                    'territoirid' => $request->territoirid,
                                                    'zoneid' => $request->zoneid,
                                                    'airid' => $request->airid,
                                                    'orgid' => $request->structureid,
                                                    'population' => $request->population,
                                                    'pop_deplace' => $request->pop_deplace,
                                                    'pop_retourne' => $request->pop_retourne,
                                                    'pop_site' => $request->pop_site,
                                                    'userid' => $user->id,
                                                    'semaine_epid' => $request->semaine_epid,
                                                    'annee_epid' => $request->annee_epid,
                                                    "dateadd" => $request->dateadd,
                                                    "orguserid" => $request->orgid
                                                ]);

                                                $bloc2 = Bloc2Model::create([
                                                    'bloc1id' => $bloc1->id,
                                                    'etat_infra' => $request->etat_infra,
                                                    'equipement' => $request->equipement,
                                                    'nbr_lit' => $request->nbr_lit,
                                                    'taux_occupation' => $request->taux_occupation,
                                                    'nbr_reco' => $request->nbr_reco,
                                                    'pop_eloigne' => $request->pop_eloigne,
                                                    'pop_vulnerable' => $request->pop_vulnerable,
                                                ]);

                                                Bloc3Model::create([
                                                    'bloc2id' => $bloc2->id,
                                                    'cout_ambulatoire' => $request->cout_ambulatoire,
                                                    'cout_hospitalisation' => $request->cout_hospitalisation,
                                                    'cout_accouchement' => $request->cout_accouchement,
                                                    'cout_cesarienne' => $request->cout_cesarienne,
                                                    'barriere' => $request->barriere,
                                                    'pop_handicap' => $request->pop_handicap,
                                                    'couvertureDtc3' => $request->couvertureDtc3,
                                                    'mortaliteLessfiveyear' => $request->mortaliteLessfiveyear,
                                                    'covid19_nbrcas' => $request->covid19_nbrcas,
                                                    'covid19_nbrdeces' => $request->covid19_nbrdeces,
                                                    'covid19_nbrtest' => $request->covid19_nbrtest,
                                                    'covid19_vacciDispo' => $request->covid19_vacciDispo,
                                                    'pourcentCleanWater' => $request->pourcentCleanWater,
                                                    'malnutrition' => $request->malnutrition,
                                                ]);

                                                $gap = GapsModel::where('id', $bloc1->id)->first();
                                                //INSERTION CRISE GAP
                                                if ($gap) {
                                                    $gap->crisegap()->detach();
                                                    foreach ($request->datacriseid as $item) {
                                                        $gap->crisegap()->attach([$bloc1->id =>
                                                        [
                                                            'criseid' => $item,
                                                        ]]);
                                                    }
                                                }

                                                // INSERTION DE CAS DE MALADIES
                                                if ($gap) {
                                                    $gap->maladiegap()->detach();
                                                    foreach ($request->datamaladie as $item) {
                                                        $gap->maladiegap()->attach([$bloc1->id =>
                                                        [
                                                            'maladieid' => $item['maladieid'],
                                                            'nbrCas' => $item['nbrCas'],
                                                            'nbrDeces' => $item['nbrDeces'],
                                                        ]]);
                                                    }
                                                }

                                                // INSERTION MEDICAMENT EN RUPTURE
                                                if ($gap) {
                                                    $gap->medicamentrupture()->detach();
                                                    foreach ($request->datamedocid as $item) {
                                                        $gap->medicamentrupture()->attach([$bloc1->id =>
                                                        [
                                                            'medocid' => $item,
                                                        ]]);
                                                    }
                                                }

                                                //INSERTION PARTENAIRE PRESENT

                                                if ($gap) {
                                                    $gap->partenairegap()->detach();
                                                    foreach ($request->datapartenaireid as $item) {
                                                        $gap->partenairegap()->attach([$bloc1->id =>
                                                        [
                                                            'orgid' => $item['orgid'],
                                                            'contact_point_facal' => $item['email'],
                                                            'date_debut' => $item['date_debut'],
                                                            'date_fin' => $item['date_fin'],
                                                        ]]);
                                                    }
                                                }

                                                //INSERTION INDICATEURS PARTENAIRE PRESENT
                                                if ($gap) {
                                                    $gap->indicateurgap()->detach();
                                                    foreach ($request->datapartenaireid as $item) {
                                                        foreach ($item["datatindicateur"] as $items) {
                                                            $gap->indicateurgap()->attach([$bloc1->id =>
                                                            [
                                                                'orgid' => $item['orgid'],
                                                                'indicateurid' => $items,
                                                            ]]);
                                                        }
                                                    }
                                                }


                                                //INSERTION TYPE PERSONNELS
                                                if ($gap) {
                                                    $gap->typepersonnelgap()->detach();
                                                    foreach ($request->datatypepersonnel as $item) {
                                                        $gap->typepersonnelgap()->attach([$bloc1->id =>
                                                        [
                                                            'personnelid' => $item['typepersonnelid'],
                                                            'nbr' => $item['nbr'],
                                                        ]]);
                                                    }
                                                }



                                                //INSERTION POPULATION ELOIGNE GAP
                                                if ($gap) {
                                                    $gap->populationeloignegap()->detach();
                                                    foreach ($request->datapopulationeloigne as $item) {
                                                        $gap->populationeloignegap()->attach([$bloc1->id =>
                                                        [
                                                            'localite' => $item['localite'],
                                                            'nbr' => $item['nbr'],
                                                        ]]);
                                                    }
                                                }
                                                if ($bloc1) {

                                                    if ($request->images) {
                                                        foreach ($request->images as $item) {
                                                            $image = UtilController::uploadMultipleImage($item, '/uploads/gap/');
                                                            $bloc1->imagesgap()->attach([$bloc1->id =>
                                                            [
                                                                'image' => $image,
                                                            ]]);
                                                        }
                                                    }
                                                }

                                                return response()->json([
                                                    "message" => 'Traitement réussi avec succès!',
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
                                                    )->where('id', $bloc1->id)->orderBy('created_at', 'desc')->where('userid', $user->id)->where('orguserid', $request->orgid)->where('status', 0)->where('deleted', 0)->first(),
                                                ], 200);
                                            } else {
                                                return response()->json([
                                                    "message" => "structureid not found "
                                                ], 402);
                                            }
                                        } else {
                                            return response()->json([
                                                "message" => "aireid not found "
                                            ], 402);
                                        }
                                    } else {
                                        return response()->json([
                                            "message" => "zoneid not found "
                                        ], 402);
                                    }
                                } else {
                                    return response()->json([
                                        "message" => "territoirid not found "
                                    ], 402);
                                }
                            } else {
                                return response()->json([
                                    "message" => "provinceid not found "
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
                            "message" => "cette permission" . $permission->name . "n'existe pas",
                            "code" => 402
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
                    "message" => "Le dernier gap doit atteindre 30 jours pour envoyer un nouveau gap",
                    "code" => 402
                ], 402);
            }
        }
    }
    //update gap
    public function UpdateGap(Request $request, $gapid)
    {
        $request->validate([
            // Bloc1
            'provinceid' => 'required',
            'territoirid' => 'required',
            'zoneid' => 'required',
            'airid' => 'required',
            'structureid' => 'required',
            'orgid' => 'required',
        ]);
        $datagap = GapsModel::where('deleted', 0)
            ->where('id', $gapid)->where('children', null)->first();
        $province = province::find($request->provinceid);
        $territoir = territoir::find($request->territoirid);
        $zone = zonesante::find($request->zoneid);
        $aire = airesante::find($request->airid);
        $structure = structureSanteModel::find($request->structureid);
        $user = Auth::user();
        $namepermission = 'update_gap';
        $permission = Permission::where('name', $namepermission)->first();
        $organisation = Organisation::find($request->orgid);
        $date = date('d/m/y');
        $timestamp = date('H:i:s');
        if ($organisation) {
            if ($permission) {
                $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
                $permission_valide_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
                    ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();

                if ($permission_valide_gap) {
                    $datavalide = GapsModel::where('id', $gapid)->whereNotNull('children')->where('deleted', 0)->first();
                    if ($datavalide) {
                        return response()->json([
                            "message" => "Ce gap est déjà validé, on ne peut le modifier pour l'instant!",
                            "code" => 422,
                        ], 422);
                    } else {
                        if ($datagap) {
                            if ($province) {
                                if ($territoir) {
                                    if ($zone) {
                                        if ($aire) {
                                            if ($structure) {
                                                $datagap->provinceid = $request->provinceid;
                                                $datagap->territoirid = $request->territoirid;
                                                $datagap->zoneid = $request->zoneid;
                                                $datagap->airid = $request->airid;
                                                $datagap->orgid = $request->structureid;
                                                $datagap->population = $request->population;
                                                $datagap->pop_deplace = $request->pop_deplace;
                                                $datagap->pop_retourne = $request->pop_retourne;
                                                $datagap->pop_site = $request->pop_site;
                                                $datagap->userid = $user->id;
                                                $datagap->semaine_epid = $request->semaine_epid;
                                                $datagap->annee_epid = $request->annee_epid;
                                                $datagap->title = $date . ' ' .
                                                    $timestamp . '/' . $province->name . '/' . $territoir->name . '/' . $zone->name . '/' . $aire->name . '/' . $structure->name;
                                                $datagap->save();

                                                $datagap_bloc2 = Bloc2Model::where('deleted', 0)
                                                    ->where('bloc1id', $datagap->id)->first();

                                                $datagap_bloc2->etat_infra = $request->etat_infra;
                                                $datagap_bloc2->equipement = $request->equipement;
                                                $datagap_bloc2->nbr_lit = $request->nbr_lit;
                                                $datagap_bloc2->taux_occupation = $request->taux_occupation;
                                                $datagap_bloc2->nbr_reco = $request->nbr_reco;
                                                $datagap_bloc2->pop_eloigne = $request->pop_eloigne;
                                                $datagap_bloc2->pop_vulnerable = $request->pop_vulnerable;
                                                $datagap_bloc2->save();

                                                $datagap_bloc3 = Bloc3Model::where('deleted', 0)
                                                    ->where('bloc2id', $datagap_bloc2->id)->first();
                                                $datagap_bloc3->cout_ambulatoire = $request->cout_ambulatoire;
                                                $datagap_bloc3->cout_hospitalisation = $request->cout_hospitalisation;
                                                $datagap_bloc3->cout_accouchement = $request->cout_accouchement;
                                                $datagap_bloc3->cout_cesarienne = $request->cout_cesarienne;
                                                $datagap_bloc3->barriere = $request->barriere;
                                                $datagap_bloc3->pop_handicap = $request->pop_handicap;
                                                $datagap_bloc3->couvertureDtc3 = $request->couvertureDtc3;
                                                $datagap_bloc3->mortaliteLessfiveyear = $request->mortaliteLessfiveyear;
                                                $datagap_bloc3->covid19_nbrcas = $request->covid19_nbrcas;
                                                $datagap_bloc3->covid19_nbrdeces = $request->covid19_nbrdeces;
                                                $datagap_bloc3->covid19_nbrtest = $request->covid19_nbrtest;
                                                $datagap_bloc3->covid19_vacciDispo = $request->covid19_vacciDispo;
                                                $datagap_bloc3->pourcentCleanWater = $request->pourcentCleanWater;
                                                $datagap_bloc3->malnutrition = $request->malnutrition;
                                                $datagap_bloc3->save();

                                                if ($datagap) {
                                                    $datagap->maladiegap()->detach();
                                                    foreach ($request->datamaladie as $item) {
                                                        $datagap->maladiegap()->attach([$datagap->id =>
                                                        [
                                                            'maladieid' => $item['maladieid'],
                                                            'nbrCas' => $item['nbrCas'],
                                                            'nbrDeces' => $item['nbrDeces'],
                                                        ]]);
                                                    }
                                                }

                                                // INSERTION MEDICAMENT EN RUPTURE
                                                if ($datagap) {
                                                    $datagap->medicamentrupture()->detach();
                                                    foreach ($request->datamedocid as $item) {
                                                        $datagap->medicamentrupture()->attach([$datagap->id =>
                                                        [
                                                            'medocid' => $item,
                                                        ]]);
                                                    }
                                                }

                                                //INSERTION PARTENAIRE PRESENT

                                                if ($datagap) {
                                                    $datagap->partenairegap()->detach();
                                                    foreach ($request->datapartenaireid as $item) {
                                                        $datagap->partenairegap()->attach([$datagap->id =>
                                                        [
                                                            'orgid' => $item['orgid'],
                                                            'contact_point_facal' => $item['email'],
                                                            'date_debut' => $item['date_debut'],
                                                            'date_fin' => $item['date_fin'],
                                                        ]]);
                                                    }
                                                }

                                                //INSERTION INDICATEURS PARTENAIRE PRESENT
                                                if ($datagap) {
                                                    $datagap->indicateurgap()->detach();
                                                    foreach ($request->datapartenaireid as $item) {
                                                        foreach ($item["datatindicateur"] as $items) {
                                                            $datagap->indicateurgap()->attach([$datagap->id =>
                                                            [
                                                                'orgid' => $item['orgid'],
                                                                'indicateurid' => $items,
                                                            ]]);
                                                        }
                                                    }
                                                }


                                                //INSERTION TYPE PERSONNELS
                                                if ($datagap) {
                                                    $datagap->typepersonnelgap()->detach();
                                                    foreach ($request->datatypepersonnel as $item) {
                                                        $datagap->typepersonnelgap()->attach([$datagap->id =>
                                                        [
                                                            'personnelid' => $item['typepersonnelid'],
                                                            'nbr' => $item['nbr'],
                                                        ]]);
                                                    }
                                                }

                                                //INSERTION CRISE GAP
                                                if ($datagap) {
                                                    $datagap->crisegap()->detach();
                                                    foreach ($request->datacriseid as $item) {
                                                        $datagap->crisegap()->attach([$datagap->id =>
                                                        [
                                                            'criseid' => $item,
                                                        ]]);
                                                    }
                                                }

                                                //INSERTION POPULATION ELOIGNE GAP
                                                if ($datagap) {
                                                    $datagap->populationeloignegap()->detach();
                                                    foreach ($request->datapopulationeloigne as $item) {
                                                        $datagap->populationeloignegap()->attach([$datagap->id =>
                                                        [
                                                            'localite' => $item['localite'],
                                                            'nbr' => $item['nbr'],
                                                        ]]);
                                                    }
                                                }

                                                return response()->json([
                                                    "message" => 'Modification réussie avec succès!',
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
                                                    )->where('id', $gapid)->orderBy('created_at', 'desc')->where('userid', $user->id)->where('orguserid', $request->orgid)->where('status', 0)->where('deleted', 0)->first(),
                                                ], 200);
                                            } else {
                                                return response()->json([
                                                    "message" => "structureid not found "
                                                ], 402);
                                            }
                                        } else {
                                            return response()->json([
                                                "message" => "aireid not found "
                                            ], 402);
                                        }
                                    } else {
                                        return response()->json([
                                            "message" => "zoneid not found "
                                        ], 402);
                                    }
                                } else {
                                    return response()->json([
                                        "message" => "territoirid not found "
                                    ], 402);
                                }
                            } else {
                                return response()->json([
                                    "message" => "provinceid not found "
                                ], 402);
                            }
                        } else {
                            return response()->json([
                                "message" => "Ce gap est déjà validé on ne peut le modifier pour l'instant!",
                                "code" => 422,
                            ], 422);
                        }
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

    //Delete gap
    public function deletegap(Request $request, $id)
    {

        $request->validate([
            "orgid" => 'required'
        ]);

        $user = Auth::user();
        $permission = Permission::where('name', 'delete_gap')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {
                $gap = GapsModel::where('id', $id)->where('orguserid', $request->orgid)->where('status', 0)->where('deleted', 0)->first();
                if ($gap) {
                    $gap->deleted = 1;
                    $gap->save();
                    return response()->json([
                        "message" => 'Liste des gaps',
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
                        )->orderBy('created_at', 'desc')->where('orguserid', $request->orgid)->where('status', 0)->where('deleted', 0)->where('children', null)->get()
                    ]);
                } else {
                    return response()->json([
                        "message" => 'Cette identifiant est erronné dans le système!',
                        "code" => 402,
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
    //validation du gap
    public function valideGap(Request $request, $gapid)
    {
        $request->validate([
            'provinceid' => 'required',
            'territoirid' => 'required',
            'zoneid' => 'required',
            'airid' => 'required',
            'structureid' => 'required',
        ]);

        $province = province::find($request->provinceid);
        $territoir = territoir::find($request->territoirid);
        $zone = zonesante::find($request->zoneid);
        $aire = airesante::find($request->airid);
        $structure = structureSanteModel::find($request->structureid);

        $user = Auth::user();

        $date = date('y-m-d');
        $timestamp = date('H:i:s');
        $namepermission = 'valide_gap';

        $permission = Permission::where('name', $namepermission)->first();
        $organisation = Organisation::find($request->orgid);
        if ($organisation) {
            if ($permission) {
                $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
                $permission_valide_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
                    ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();

                if ($permission_valide_gap) {
                    $datagap = GapsModel::where('id', $gapid)->where('status', 0)->first();
                    $datavalide = GapsModel::where('id', $gapid)->where('status', 1)->where('deleted', 0)->first();
                    if ($datavalide) {
                        return response()->json([
                            "message" => "Ce gap est déjà validé",
                            "code" => 422,
                        ], 422);
                    } else {
                        if ($datagap) {
                            $datagap->status = 1;
                            $datagap->save();

                            if ($province) {
                                if ($territoir) {
                                    if ($zone) {
                                        if ($aire) {
                                            if ($structure) {
                                                $bloc1 = GapsModel::create([
                                                    'title' => $date . ' ' .
                                                        $timestamp . '/' . $province->name . '/' . $territoir->name . '/' . $zone->name . '/' . $aire->name . '/' . $structure->name,
                                                    'provinceid' => $request->provinceid,
                                                    'territoirid' => $request->territoirid,
                                                    'zoneid' => $request->zoneid,
                                                    'airid' => $request->airid,
                                                    'orgid' => $request->structureid,
                                                    'population' => $request->population,
                                                    'pop_deplace' => $request->pop_deplace,
                                                    'pop_retourne' => $request->pop_retourne,
                                                    'pop_site' => $request->pop_site,
                                                    'userid' => $user->id,
                                                    'children' => $gapid,
                                                    'status' => 1,
                                                    'semaine_epid' => $request->semaine_epid,
                                                    'annee_epid' => $request->annee_epid,
                                                    'dateadd' => $request->dateadd,
                                                    "orguserid" => $request->orgid
                                                ]);

                                                $bloc2 = Bloc2Model::create([
                                                    'bloc1id' => $bloc1->id,
                                                    'etat_infra' => $request->etat_infra,
                                                    'equipement' => $request->equipement,
                                                    'nbr_lit' => $request->nbr_lit,
                                                    'taux_occupation' => $request->taux_occupation,
                                                    'nbr_reco' => $request->nbr_reco,
                                                    'pop_eloigne' => $request->pop_eloigne,
                                                    'pop_vulnerable' => $request->pop_vulnerable,
                                                ]);

                                                Bloc3Model::create([
                                                    'bloc2id' => $bloc2->id,
                                                    'cout_ambulatoire' => $request->cout_ambulatoire,
                                                    'cout_hospitalisation' => $request->cout_hospitalisation,
                                                    'cout_accouchement' => $request->cout_accouchement,
                                                    'cout_cesarienne' => $request->cout_cesarienne,
                                                    'barriere' => $request->barriere,
                                                    'pop_handicap' => $request->pop_handicap,
                                                    'couvertureDtc3' => $request->couvertureDtc3,
                                                    'mortaliteLessfiveyear' => $request->mortaliteLessfiveyear,
                                                    'covid19_nbrcas' => $request->covid19_nbrcas,
                                                    'covid19_nbrdeces' => $request->covid19_nbrdeces,
                                                    'covid19_nbrtest' => $request->covid19_nbrtest,
                                                    'covid19_vacciDispo' => $request->covid19_vacciDispo,
                                                    'pourcentCleanWater' => $request->pourcentCleanWater,
                                                    'malnutrition' => $request->malnutrition,
                                                ]);

                                                // INSERTION DE CAS DE MALADIES
                                                $gap = GapsModel::where('id', $bloc1->id)->first();
                                                if ($gap) {
                                                    $gap->maladiegap()->detach();
                                                    foreach ($request->datamaladie as $item) {
                                                        $gap->maladiegap()->attach([$bloc1->id =>
                                                        [
                                                            'maladieid' => $item['maladieid'],
                                                            'nbrCas' => $item['nbrCas'],
                                                            'nbrDeces' => $item['nbrDeces'],
                                                        ]]);
                                                    }
                                                }

                                                // INSERTION MEDICAMENT EN RUPTURE
                                                if ($gap) {
                                                    $gap->medicamentrupture()->detach();
                                                    foreach ($request->datamedocid as $item) {
                                                        $gap->medicamentrupture()->attach([$bloc1->id =>
                                                        [
                                                            'medocid' => $item,
                                                        ]]);
                                                    }
                                                }

                                                //INSERTION PARTENAIRE PRESENT

                                                if ($gap) {
                                                    $gap->partenairegap()->detach();
                                                    foreach ($request->datapartenaireid as $item) {
                                                        $gap->partenairegap()->attach([$bloc1->id =>
                                                        [
                                                            'orgid' => $item['orgid'],
                                                            'contact_point_facal' => $item['email'],
                                                            'date_debut' => $item['date_debut'],
                                                            'date_fin' => $item['date_fin'],
                                                        ]]);
                                                    }
                                                }

                                                //INSERTION INDICATEURS PARTENAIRE PRESENT
                                                if ($gap) {
                                                    $gap->indicateurgap()->detach();
                                                    foreach ($request->datapartenaireid as $item) {
                                                        foreach ($item["datatindicateur"] as $items) {
                                                            $gap->indicateurgap()->attach([$bloc1->id =>
                                                            [
                                                                'orgid' => $item['orgid'],
                                                                'indicateurid' => $items,
                                                            ]]);
                                                        }
                                                    }
                                                }


                                                //INSERTION TYPE PERSONNELS
                                                if ($gap) {
                                                    $gap->typepersonnelgap()->detach();
                                                    foreach ($request->datatypepersonnel as $item) {
                                                        $gap->typepersonnelgap()->attach([$bloc1->id =>
                                                        [
                                                            'personnelid' => $item['typepersonnelid'],
                                                            'nbr' => $item['nbr'],
                                                        ]]);
                                                    }
                                                }

                                                //INSERTION TYPE PERSONNELS
                                                if ($gap) {
                                                    $gap->crisegap()->detach();
                                                    foreach ($request->datacriseid as $item) {
                                                        $gap->crisegap()->attach([$bloc1->id =>
                                                        [
                                                            'criseid' => $item,
                                                        ]]);
                                                    }
                                                }


                                                if ($gap) {
                                                    $gap->populationeloignegap()->detach();
                                                    foreach ($request->datapopulationeloigne as $item) {
                                                        $gap->populationeloignegap()->attach([$bloc1->id =>
                                                        [
                                                            'localite' => $item['localite'],
                                                            'nbr' => $item['nbr'],
                                                        ]]);
                                                    }
                                                }

                                                //INSERTION IMAGES GAP
                                                if ($datagap->images) {
                                                    foreach ($datagap->images()->get() as $item) {
                                                        $bloc1->imagesgap()->attach([$datagap->id =>
                                                        [
                                                            'image' => $item->image,
                                                        ]]);
                                                    }
                                                }

                                                // $image = env('IMAGE_GAP');
                                                // $dataToken_for_user = TokenUsers::all();
                                                // foreach ($dataToken_for_user as $item) {
                                                //     PushNotification::sendPushNotification($item->token, "Un gap a été mise en ligne par Cosamed", $bloc1->title, $image);
                                                // }

                                                // foreach (User::get() as $key => $value) {
                                                //     Notifications::create([
                                                //         "user_id" => $value->id,
                                                //         "title" => "Un gap a été mise en ligne par Cosamed",
                                                //         "description" => "Un gap appuyé a été mise en ligne par Cosamed." . '/' . $bloc1->title,
                                                //         "id_type" => $bloc1->id,
                                                //         "type" => "gap"
                                                //     ]);
                                                // }

                                                return response()->json([
                                                    "message" => 'Traitement réussi avec succès!',
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
                                                    )->where('userid', $user->id)->where('id', $bloc1->id)->where('orguserid', $request->orgid)->where('status', 1)->where('deleted', 0)->first(),
                                                ], 200);
                                            } else {
                                                return response()->json([
                                                    "message" => "structureid not found "
                                                ], 402);
                                            }
                                        } else {
                                            return response()->json([
                                                "message" => "aireid not found "
                                            ], 402);
                                        }
                                    } else {
                                        return response()->json([
                                            "message" => "zoneid not found "
                                        ], 402);
                                    }
                                } else {
                                    return response()->json([
                                        "message" => "territoirid not found "
                                    ], 402);
                                }
                            } else {
                                return response()->json([
                                    "message" => "provinceid not found "
                                ], 402);
                            }
                        } else {
                            return response()->json([
                                "message" => "Ce gap est déjà validé",
                                "code" => 422,
                            ], 422);
                        }
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

    public function listGap($orgid)
    {
        $user = Auth::user();
        $permission = Permission::where('name', 'view_gap')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {
                return response()->json([
                    "message" => 'Liste des gaps!',
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
                    )->orderBy('created_at', 'desc')->where('status', 0)->where('deleted', 0)->where('children', null)->get(),
                ]);
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

    public function listgap1()
    {
        return response()->json([
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
            )->where('deleted', 0)->get(),
            "code" => 200,
        ], 200);
    }
    public function listGapByuser($orgid)
    {
        $user = Auth::user();
        if (GapsModel::where('userid', $user->id)->where('orguserid', $orgid)->where('status', 0)->exists()) {
            return response()->json([
                "message" => 'Liste des gaps!',
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
                )->orderBy('created_at', 'desc')->where('userid', $user->id)->where('orguserid', $orgid)->where('status', 0)->where('deleted', 0)->get()
            ]);
        } else {
            return response()->json([
                "message" => "Not data"
            ], 402);
        }
    }
    public function listGapValide($orgid)
    {
        $user = Auth::user();
        $permission = Permission::where('name', 'view_gap_valide')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {
                if (GapsModel::where('status', 1)->whereNot('children', null)->exists()) {
                    return response()->json([
                        "message" => 'Liste des gaps validés',
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
                        )->orderBy('created_at', 'desc')->where('status', 1)->where('deleted', 0)->whereNot('children', null)->get()
                    ]);
                } else {
                    return response()->json([
                        "message" => "Not data"
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
    public function listGapValideByuser($orgid)
    {
        $user = Auth::user();
        $permission = Permission::where('name', 'view_gap_valide')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {
                if (GapsModel::where('userid', $user->id)->where('orguserid', $orgid)->where('deleted', 0)->where('status', 1)->exists()) {
                    return response()->json([
                        "message" => 'Liste des gaps validés',
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
                        )->orderBy('created_at', 'desc')->where('userid', $user->id)->where('orguserid', $orgid)->where('deleted', 0)->where('status', 1)->get()
                    ]);
                } else {
                    return response()->json([
                        "message" => "Not data"
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
    public function listGapValideRepondu($orgid)
    {
        $user = Auth::user();
        $permission = Permission::where('name', 'view_gap_repondu')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {
                if (GapsModel::where('status', 2)->whereNot('children', null)->exists()) {
                    return response()->json([
                        "message" => 'Liste des gaps validés',
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
                        )->orderBy('created_at', 'desc')->where('status', 2)->where('deleted', 0)->whereNot('children', null)->get()
                    ]);
                } else {
                    return response()->json([
                        "message" => "Not data"
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
    public function DetailGaps($id)
    {
        $gap = GapsModel::find($id);
        if ($gap) {
            return response()->json([
                "message" => 'Detail Gap!',
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
                )->where('id', $id)->where('deleted', 0)->first(),
            ]);
        } else {
            return response()->json([
                "message" => "id not found "
            ], 402);
        }
    }

    public function listGapProvince($provinceid)
    {
        $province = province::find($provinceid);
        if ($province) {
            return response()->json([
                "message" => 'Liste des gaps!',
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
                )->where('provinceid', $province->id)->where('deleted', 0)->get(),
            ]);
        } else {
            return response()->json([
                "message" => "provinceid not found "
            ], 402);
        }
    }
    public function listGapTerritoir($territoirid)
    {
        $territoir = territoir::find($territoirid);
        if ($territoir) {
            return response()->json([
                "message" => 'Liste des gaps!',
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
                )->where('territoirid', $territoir->id)->where('deleted', 0)->get(),
            ]);
        } else {
            return response()->json([
                "message" => "territoirid not found "
            ], 402);
        }
    }
    public function listGapZone($zoneid)
    {
        $zone = territoir::find($zoneid);
        if ($zone) {
            return response()->json([
                "message" => 'Liste des gaps!',
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
                )->where('zoneid', $zone->id)->where('deleted', 0)->get(),
            ]);
        } else {
            return response()->json([
                "message" => "zoneid not found "
            ], 402);
        }
    }
    public function listGapAire($airid)
    {
        $aire = territoir::find($airid);
        if ($aire) {
            return response()->json([
                "message" => 'Liste des gaps!',
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
                    'images'
                )->where('airid', $aire->id)->where('deleted', 0)->get(),
            ]);
        } else {
            return response()->json([
                "message" => "zoneid not found "
            ], 402);
        }
    }

    // public function getlastgapvalide()
    // {
    //     return response()->json([
    //         "message" => 'Derniers gap validés par structure',
    //         "code" => 200,
    //         "data" => GapsModel::with(
    //             'datauser',
    //         )->orderby('dateadd', 'desc')->where('deleted', 0)->whereNot('children', null)->get()
    //     ]);
    // }
    public function getlastgapvalide()
    {

        $dt = new DateTime();
        $startDate = $dt->format('Y-m-d');

        return response()->json([
            "message" => 'Derniers gap validés par structure',
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
            )->orderby('dateadd', 'desc')->where('deleted', 0)->whereNot('children', null)->get()
        ]);
    }
    public function deleteImageGap($id)
    {
        $images = ImageGapModel::where('id', $id)->where('deleted', 0)->first();
        if ($images) {
            UtilController::removeImageUrl($images->image);
            $images->deleted = 1;
            $images->save();
            return response()->json([
                'code' => 200,
                'message' => 'Image deleted successfully',
            ]);
        }
    }
    public function Imagegap(Request $request, $id)
    {
        $datagap = GapsModel::where('id', $id)->first();
        if ($datagap) {
            if (count($request->images) > 0) {
                $image = UtilController::uploadMultipleImage($request->images, '/uploads/gap/');
                foreach ($image as $item) {
                    $datagap->imagesgap()->attach([$datagap->id =>
                    [
                        'image' => $item,
                    ]]);
                }
                return response()->json([
                    "message" => 'Traitement réussi avec succès!',
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
                    )->where('id', $id)->where('deleted', 0)->first(),
                ], 200);
            } else {
                return response()->json([
                    "message" => 'Traitement réussi avec succès!',
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
                    )->where('id', $id)->where('deleted', 0)->first(),
                ], 200);
            }
        } else {
            return response()->json([
                "message" => "C'est identifiant de gap n'existe pas!",
                "code" => 402,
            ], 402);
        }
    }
    public function search_all(Request $request)
    {
        $request->validate([
            "keyword" => "required"
        ]);

        $user = Auth::user();

        $data_gap = GapsModel::with(
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
        )->where('title', 'like', '%' . $request->keyword . '%');

        $data_alert = AlertModel::with(
            'datauser',
            'dataaire.zonesante.territoir.province',
            'maladie',
            'images'
        )->where('name_point_focal', 'like', '%' . $request->keyword . '%');
        // $data_pub = PublicationsModel::with('category')->where('deleted', 0)->orderby('date_post', 'desc');
        // $data_pub->where('title', 'like', '%' . $request->keyword . '%')
        //     ->orwhere('content', 'like', '%' . $request->keyword)
        //     ->orwhere('auteur', 'like', '%' . $request->keyword)
        //     ->select(
        //         't_publications.title',
        //         't_publications.content',
        //         't_publications.auteur',
        //     );

        $alldatagap = $data_gap->whereNotNull('t_gaps_bloc1.children')->get()->toArray();
        $alldataalert = $data_alert->whereNotNull('children')->get()->toArray();
        // $alldatapub = $data_pub->get()->toArray();
        $array = array_merge($alldatagap, $alldataalert);
        if (count($alldatagap) > 0 || count($alldataalert) > 0) {
            $user->tags()->UpdateOrCreate([
                'name' => $request->keyword,
            ], [
                'name' => $request->keyword,
            ]);
        }
        return response([
            "message" => "Success",
            "code" => 200,
            "data" =>  $array
        ], 200);
    }
    public function dataaire($id)
    {
        $gap_appuis = AlertModel::where('gapid', $id)->get();
        return response([
            "message" => "Success",
            "code" => 200,
            "data" => $gap_appuis
        ], 200);
    }
    public function gap_appuis($id)
    {
        $gap_appuis = GapAppuiModel::where('gapid', $id)->get();
        return response([
            "message" => "Success",
            "code" => 200,
            "data" => $gap_appuis
        ], 200);
    }

    public function pin(Request $request)
    {
        $province = $request->get('province') ? $request->get('province') : "all";
        $territoir = $request->get('territoir') ? $request->get('territoir') : "all";
        $zone = $request->get('zone') ? $request->get('zone') : "all";
        $aire = $request->get('aire') ? $request->get('aire') : "all";
        $structure = $request->get('structure') ? $request->get('structure') :  "all";
        $type = $request->get('type') ? $request->get('type') : "all";

        if ($province == "all") {
            $allgap = GapsModel::with('datapopulationEloigne')->where('status', 1)->where('deleted', 0)->whereNot('children', null)->getQuery();
            $gap = GapsModel::with('datapopulationEloigne')->where('status', 1)->where('deleted', 0)->whereNot('children', null)->get();
        } else {
            $allgap = GapsModel::with('datapopulationEloigne')->where('status', 1)->where('deleted', 0)
                ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)->whereNot('children', null)->getQuery();
            $gap = GapsModel::with('datapopulationEloigne')->where('status', 1)->where('deleted', 0)->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)->whereNot('children', null)->get();
            if ($territoir == "all") {
                $allgap = $allgap;
                $gap = GapsModel::with('datapopulationEloigne')->where('status', 1)->where('deleted', 0)->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)->whereNot('children', null)->get();
            } else {
                $allgap = GapsModel::with('datapopulationEloigne')->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)
                    ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)->whereNot('children', null)->getQuery();
                $gap = GapsModel::with('datapopulationEloigne')->where('status', 1)->where('deleted', 0)->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                    ->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)->whereNot('children', null)->get();
                if ($zone == "all") {
                    $allgap =  $allgap;
                    $gap = GapsModel::with('datapopulationEloigne')->where('status', 1)->where('deleted', 0)
                        ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                        ->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)->whereNot('children', null)->get();
                } else {
                    $allgap = GapsModel::with('datapopulationEloigne')->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)
                        ->whereRelation('dataaire.zonesante', 'id', $zone)
                        ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                        ->whereNot('children', null)->getQuery();
                    $gap = GapsModel::with('datapopulationEloigne')->where('status', 1)->where('deleted', 0)
                        ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                        ->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)
                        ->whereRelation('dataaire.zonesante', 'id', $zone)
                        ->whereNot('children', null)->get();
                    if ($aire == "all") {
                        $allgap =  $allgap;
                        $gap = GapsModel::with('datapopulationEloigne')->where('status', 1)->where('deleted', 0)
                            ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                            ->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)
                            ->whereRelation('dataaire.zonesante', 'id', $zone)
                            ->whereNot('children', null)->get();
                    } else {
                        $allgap =  GapsModel::with('datapopulationEloigne')->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)
                            ->whereRelation('dataaire.zonesante', 'id', $zone)
                            ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                            ->whereRelation('dataaire', 'id', $aire)->whereNot('children', null)
                            ->getQuery();
                        $gap = GapsModel::with('datapopulationEloigne')->where('status', 1)->where('deleted', 0)
                            ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                            ->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)
                            ->whereRelation('dataaire.zonesante', 'id', $zone)
                            ->whereRelation('dataaire', 'id', $aire)->whereNot('children', null)->get();
                    }
                }
            }
        }
        $startOfLastWeek = date("Y-m-d");
        $endOfLastWeek = date_create($startOfLastWeek)->modify('-7 days')->format('Y-m-d');

        if ($type === "all") {

            $data = [
                "data" => [
                    "population" => $allgap->get()->sum("population"),
                    "deplace" =>  $allgap->get()->sum("pop_deplace"),
                    "retourne" => $allgap->get()->sum("pop_retourne"),
                    "eloigne" =>  $gap,
                ],
                "total_by_month" => $allgap->orderBy('dateadd', 'asc')->whereYear('dateadd', date('Y'))->get()
            ];
        }

        if ($type === "population") {
            $weeks = [];
            for ($i = 1; $i <= 52; $i++) {
                array_push($weeks, [
                    "week" => $i,
                    "total" => $allgap->get()->where('semaine_epid',$i)->sum("population")
                ]);
            }
            $data = [
                "data" => [
                    "population" => $allgap->get()->sum("population"),
                ],
                "total_by_month" => $allgap->orderBy('dateadd', 'asc')->whereYear('dateadd', date('Y'))->get(),
                "weeks" => $weeks
            ];
        }

        if ($type === "deplace") {
            $weeks = [];
            for ($i = 1; $i <= 52; $i++) {
                array_push($weeks, [
                    "week" => $i,
                    "total" => $allgap->get()->where('semaine_epid',$i)->sum("pop_deplace")
                ]);
            }
            $data = [
                "data" => [
                    "deplace" => $allgap->get()->sum("pop_deplace"),
                ],
                "total_by_month" => $allgap->orderBy('dateadd', 'asc')->whereYear('dateadd', date('Y'))->get(),
                "weeks" => $weeks
            ];
        }

        if ($type === "retourne")
        {
            $weeks = [];
            for ($i = 1; $i <= 52; $i++) {
                array_push($weeks, [
                    "week" => $i,
                    "total" => $allgap->get()->where('semaine_epid',$i)->sum("pop_retourne")
                ]);
            }
            $data = [
                "data" => [
                    "retourne" => $allgap->get()->sum("pop_retourne"),
                ],
                "total_by_month" => $allgap->orderBy('dateadd', 'asc')->whereYear('dateadd', date('Y'))->get(),
                "weeks" => $weeks
            ];
        }

        if ($type === "eloigne") {
            $data = [
                "data" => [
                    "eloigne" =>  $gap,
                ],
                "total_by_month" => [],
            ];
        }

        return response([
            "message" => "Success",
            "code" => 200,
            "data" => $data
        ], 200);
    }
}
