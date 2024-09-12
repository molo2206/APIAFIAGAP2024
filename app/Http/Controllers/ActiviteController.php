<?php

namespace App\Http\Controllers;

use App\Models\ActiviteModel;
use App\Models\ActiviteProjetModel;
use App\Models\AffectationModel;
use App\Models\AffectationPermission;
use App\Models\AutreInfoProjets;
use App\Models\BeneficeAtteint;
use App\Models\BeneficeAtteintProjet;
use App\Models\BeneficeCible;
use App\Models\BeneficeCibleProjet;
use App\Models\CohpModel;
use App\Models\ConsultationCliniqueMobile;
use App\Models\ConsultationCliniqueMobileProjet;
use App\Models\ConsultationExterneFosa;
use App\Models\ConsultationExterneFosaProjet;
use App\Models\IndicateurActivite;
use App\Models\Permission;
use App\Models\ProjetModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActiviteController extends Controller
{
    public function create_activite(Request $request)
    {
        $request->validate([
            "title_projet" => 'required',
            "provinceid" => 'required',
            "territoirid" => 'required',
            "zoneid" => 'required',
            "airid" => 'required',
            "structureid" => 'required',
            "orgid" => 'required'
        ]);

        $user = Auth::user();
        $permission = Permission::where('name', 'create_activite')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {
                $activite = ActiviteModel::create([
                    "title_projet" => $request->title_projet,
                    "provinceid" =>  $request->provinceid,
                    "territoirid" => $request->territoirid,
                    "zoneid" => $request->zoneid,
                    "airid" =>  $request->airid,
                    "structureid" => $request->structureid,
                    "org_make_repport" => $request->org_make_repport,
                    "org_make_oeuvre" =>  $request->org_make_oeuvre,
                    "date_rapportage" =>  $request->date_rapportage,
                    "identifiant_project" => $request->identifiant_project,
                    "typeprojetid" => $request->typeprojetid,
                    "type_intervention" => $request->type_intervention,
                    "axe_strategique" => $request->axe_strategique,
                    "odd" => $request->odd,
                    "description_activite" => $request->description_activite,
                    "statut_activite" => $request->statut_activite,
                    "modalite" => $request->modalite,
                    "src_financement" => $request->src_financement,
                    "vaccination" => $request->vaccination,
                    "malnutrition" => $request->malnutrition,
                    "remarque" => $request->remarque,
                    "date_debut_projet" => $request->date_debut_projet,
                    "date_fin_projet" => $request->date_fin_projet,
                    'cohp_relais' => $request->cohp_relais,
                    'type_reponse' => $request->type_reponse,
                    'type_benef' => $request->type_benef,
                    'phone' => $request->phone,
                    'email' => $request->email,
                ]);


                //INSERTION INDICATEURS
                if ($activite) {
                    $activite->indicataire()->detach();
                    foreach ($request->indicateuractivite as $item) {
                        $activite->indicataire()->attach([$activite->id =>
                        [
                            'indicateurid' => $item,
                        ]]);
                    }
                }

                BeneficeCible::create([
                    'activiteid' => $activite->id,
                    'homme_cible' => $request->homme_cible,
                    'femme_cible' =>  $request->femme_cible,
                    'enfant_garcon_moin_cinq' =>  $request->enfant_garcon_moin_cinq,
                    'enfant_fille_moin_cinq'  =>  $request->enfant_fille_moin_cinq,
                    'personne_cible_handicap' =>  $request->personne_cible_handicap,
                    'total_cible' =>  $request->total_cible,
                ]);

                BeneficeAtteint::create([
                    "activiteid" => $activite->id,
                    "homme_atteint" => $request->homme_atteint,
                    "femme_atteint" =>  $request->femme_atteint,
                    "enfant_garcon_moin_cinq" =>  $request->enfant_garcon_moin_cinq_atteint,
                    "enfant_fille_moin_cinq" =>  $request->enfant_fille_moin_cinq_atteint,
                    "personne_atteint_handicap" =>  $request->personne_atteint_handicap,
                    "total_atteint" => $request->total_atteint
                ]);

                ConsultationExterneFosa::create([
                    "activiteid" => $activite->id,
                    "homme_consulte_fosa" => $request->homme_consulte_fosa,
                    "femme_consulte_fosa" => $request->femme_consulte_fosa,
                    "consulte_moin_cinq_fosa" => $request->consulte_moin_cinq_fosa,
                    "consulte_cinq_plus_fosa" => $request->consulte_cinq_plus_fosa,
                ]);

                ConsultationCliniqueMobile::create([
                    "activiteid" => $activite->id,
                    "homme_consulte_mob" => $request->homme_consulte_mob,
                    "femme_consulte_mob" => $request->femme_consulte_mob,
                    "consulte_moin_cinq_mob" => $request->consulte_moin_cinq_mob,
                    "consulte_cinq_plus_mob" => $request->consulte_cinq_plus_mob,
                ]);

                return response()->json([
                    "message" => "Success",
                    "data" => ActiviteModel::with(
                        'dataprovince',
                        'dataterritoir',
                        'datazone',
                        'dataaire',
                        'datastructure',
                        'data_organisation_make_rapport.type_org',
                        'data_organisation_mise_en_oeuvre.type_org',
                        'databeneficecible',
                        'databeneficeatteint',
                        'dataconsultationexterne',
                        'dataconsultationcliniquemobile',
                        'paquetappui.indicateur'
                    )->where('org_make_repport', $request->org_make_repport)->orderBy('created_at', 'desc')->get()
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

    public function updateactivite(Request $request, $id)
    {
        $request->validate([
            "orgid" => 'required',
        ]);
        $user = Auth::user();
        $permission = Permission::where('name', 'create_activite')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_projet = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {

            if ($permission_projet)
            {
                $datactivite = ActiviteProjetModel::where('id', $id)->first();
                $datactivite->projetid = $request->projetid;
                $datactivite->orgid = $request->orgid;
                $datactivite->date_rapportage = $request->date_rapportage;
                $datactivite->structureid = $request->structureid;
                $datactivite->indicateurid = $request->indicateurid;
                $datactivite->typeimpactid = $request->type_reponse;
                $datactivite->periode_rapportage = $request->periode_rapportage;
                $datactivite->save();

                if ($datactivite)
                {
                    $activitebencible = BeneficeCibleProjet::where('activiteid', $datactivite->id)->first();
                    $activitebencible->orguserid = $request->orgid;
                    $activitebencible->homme_cible = $request->homme_cible;
                    $activitebencible->femme_cible =  $request->femme_cible;

                    $activitebencible->enfant_garcon_moin_cinq =  $request->enfant_garcon_moin_cinq;
                    $activitebencible->enfant_fille_moin_cinq  =  $request->enfant_fille_moin_cinq;
                    $activitebencible->personne_cible_handicap =  $request->personne_cible_handicap;

                    $activitebencible->garcon_cible_cinq_dix_septe = $request->garcon_cible_cinq_dix_septe;
                    $activitebencible->fille_cible_cinq_dix_septe = $request->fille_cible_cinq_dix_septe;

                    $activitebencible->homme_cible_dix_huit_cinquante_neuf = $request->homme_cible_dix_huit_cinquante_neuf;
                    $activitebencible->femme_cible_dix_huit_cinquante_neuf = $request->femme_cible_dix_huit_cinquante_neuf;

                    $activitebencible->homme_cible_plus_cinquante_neuf = $request->homme_cible_plus_cinquante_neuf;
                    $activitebencible->femme_cible_plus_cinquante_neuf = $request->femme_cible_plus_cinquante_neuf;
                    $activitebencible->total_cible =  $request->total_cible;
                    $activitebencible->save();

                    $activitebenatteint = BeneficeAtteintProjet::where('activiteid', $datactivite->id)->first();

                    $activitebenatteint->orguserid = $request->orgid;
                    $activitebenatteint->homme_atteint = $request->homme_atteint;
                    $activitebenatteint->femme_atteint =  $request->femme_atteint;
                    $activitebenatteint->enfant_garcon_moin_cinq =  $request->enfant_garcon_moin_cinq_atteint;
                    $activitebenatteint->enfant_fille_moin_cinq =  $request->enfant_fille_moin_cinq_atteint;
                    $activitebenatteint->personne_atteint_handicap = $request->personne_atteint_handicap;
                    $activitebenatteint->garcon_atteint_cinq_dix_septe = $request->garcon_atteint_cinq_dix_septe;
                    $activitebenatteint->fille_atteint_cinq_dix_septe = $request->fille_atteint_cinq_dix_septe;
                    $activitebenatteint->homme_atteint_dix_huit_cinquante_neuf = $request->homme_atteint_dix_huit_cinquante_neuf;
                    $activitebenatteint->femme_atteint_dix_huit_cinquante_neuf = $request->femme_atteint_dix_huit_cinquante_neuf;
                    $activitebenatteint->homme_atteint_plus_cinquante_neuf = $request->homme_atteint_plus_cinquante_neuf;
                    $activitebenatteint->femme_atteint_plus_cinquante_neuf = $request->femme_atteint_plus_cinquante_neuf;
                    $activitebenatteint->total_atteint = $request->total_atteint;
                    $activitebenatteint->save();

                    $consultationactivite = ConsultationExterneFosaProjet::where('activiteid', $datactivite->id)->first();

                    $consultationactivite->orguserid = $request->orgid;
                    $consultationactivite->consulte_moin_cinq_fosa = $request->consulte_moin_cinq_fosa;
                    $consultationactivite->consulte_cinq_dix_sept_fosa = $request->consulte_cinq_dix_sept_fosa;
                    $consultationactivite->homme_fosa_dix_huit_plus_fosa = $request->homme_fosa_dix_huit_plus_fosa;
                    $consultationactivite->femme_fosa_dix_huit_plus_fosa = $request->femme_fosa_dix_huit_plus_fosa;
                    $consultationactivite->save();

                    $consultationactivite = ConsultationCliniqueMobileProjet::where('activiteid', $datactivite->id)->first();

                    $consultationactivite->orguserid = $request->orgid;
                    $consultationactivite->consulte_moin_cinq_mob = $request->consulte_moin_cinq_mob;
                    $consultationactivite->consulte_cinq_dix_sept_mob = $request->consulte_cinq_dix_sept_mob;
                    $consultationactivite->homme_dix_huit_plus_mob = $request->homme_dix_huit_plus_mob;
                    $consultationactivite->femme_dix_huit_plus_mob = $request->femme_dix_huit_plus_mob;
                    $consultationactivite->save();

                    $autresinfoactivite = AutreInfoProjets::where('activiteid', $datactivite->id)->first();
                    $autresinfoactivite->orguserid = $request->orgid;
                    $autresinfoactivite->description_activite = $request->description_activite;
                    $autresinfoactivite->statut_activite = $request->statut_activite;
                    $autresinfoactivite->nbr_malnutrition = $request->nbr_malnutrition;
                    $autresinfoactivite->remarque = $request->remarque;
                    $autresinfoactivite->nbr_accouchement = $request->nbr_accouchement;
                    $autresinfoactivite->email = $request->email;
                    $autresinfoactivite->phone = $request->phone;
                    $autresinfoactivite->date_rapportage = $request->date_rapportage;
                    $autresinfoactivite->nbr_cpn = $request->nbr_cpn;
                    $autresinfoactivite->save();

                    $datactivite->infosVaccination()->detach();
                    foreach ($request->infosVaccination as $item) {
                        $datactivite->infosVaccination()->attach([$datactivite->id =>
                        [
                            'typevaccinid' => $item['typevaccinid'],
                            'nbr_vaccine' => $item['nbr_vaccine'],
                        ]]);
                    }


                    return response()->json([
                        "message" => "Success",
                        "code" => 200
                    ], 200);
                } else {
                    return response()->json([
                        "message" => "Cette id du projet n'est pas reconnue dans le système!",
                        "code" => 402
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
                "code" => 402,
            ], 402);
        }
    }

    public function get_activite($orgid)
    {
        $user = Auth::user();
        $permission = Permission::where('name', 'view_activite')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {
                return response()->json([
                    "message" => "Modification avec succès",
                    "data" => ActiviteModel::with(
                        'dataprovince',
                        'dataterritoir',
                        'datazone',
                        'dataaire',
                        'datastructure',
                        'data_organisation_make_rapport.type_org',
                        'data_organisation_mise_en_oeuvre.type_org',
                        'databeneficecible',
                        'databeneficeatteint',
                        'dataconsultationexterne',
                        'dataconsultationcliniquemobile',
                        'paquetappui.indicateur'
                    )->where('org_make_repport', $orgid)->where('status',1)->where('deleted',0)->orderBy('created_at', 'desc')->where('status', 0)->where('deleted', 0)->get()
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
    public function get_all_activite($orgid)
    {
        $user = Auth::user();
        $permission = Permission::where('name', 'view_activite')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {
                return response()->json([
                    "message" => "Modification avec succès",
                    "data" => ActiviteModel::with(
                        'dataprovince',
                        'dataterritoir',
                        'datazone',
                        'dataaire',
                        'datastructure',
                        'data_organisation_make_rapport.type_org',
                        'data_organisation_mise_en_oeuvre.type_org',
                        'databeneficecible',
                        'databeneficeatteint',
                        'dataconsultationexterne',
                        'dataconsultationcliniquemobile',
                        'paquetappui.indicateur'
                    )->orderBy('created_at', 'desc')->where('status', 1)->where('deleted', 0)->get()
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

    public function detailActivite($id)
    {
        $activite = ActiviteProjetModel::where('id', $id)->first();
        if ($activite) {
            return response()->json([
                "message" => "Success",
                "code" => 200,
                "message" => "Detail d'une activité",
                "data" => ActiviteProjetModel::with(
                    "projet.cohp_relais",
                    "projet.typeprojet",
                    "projet.datatypeimpact.typeimpact",
                    "projet.datatypeimpact.indicateur.indicateur",
                    "indicateur",
                    'struture.airesante.zonesante.territoir.province',
                    'struture.typestructure',
                    'projet.data_organisation_make_rapport.type_org',
                    'projet.data_organisation_mise_en_oeuvre.type_org',
                    "typeimpacts",
                    'databeneficecible',
                    'databeneficecible',
                    'databeneficecible',
                    'databeneficeatteint',
                    'databeneficeatteint',
                    'dataconsultationexterne',
                    'dataconsultationexterne',
                    'dataconsultationcliniquemobile',
                    'dataconsultationcliniquemobile',
                    'autresinfoprojet',
                    'infosVaccinations.Vaccination',
                )->where('id', $id)->where('status',1)->where('deleted',0)->orderBy('created_at', 'desc')->first(),
            ]);
        } else {
            return response()->json([
                "message" => "Id not found",
                "code" => 404,
            ], 404);
        }
    }
    public function getcohp()
    {
        return response()->json([
            "message" => "Liste des COHP_RELAIS_IMT OMS",
            "code" => 200,
            "data" => CohpModel::all()
        ], 200);
    }
}
