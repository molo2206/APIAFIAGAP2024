<?php

namespace App\Http\Controllers;

use App\Models\ActiviteProjetModel;
use App\Models\AffectationModel;
use App\Models\AffectationPermission;
use App\Models\BeneficeAtteintProjet;
use App\Models\BeneficeCibleProjet;
use App\Models\ConsultationCliniqueMobile;
use App\Models\ConsultationCliniqueMobileProjet;
use App\Models\ConsultationExterneFosaProjet;
use App\Models\indicateur;
use App\Models\IndicateurProjetModel;
use App\Models\AutreInfoProjets;
use App\Models\DetailProjetVaccines;
use App\Models\Organisation;
use App\Models\Permission;
use App\Models\ProjetModel;
use App\Models\RayonActionProjetModel;
use App\Models\TypeImpactModel;
use App\Models\TypeImpactprojetIndicateur;
use App\Models\TypeProjet;
use App\Models\TypeReponseProjet;
use App\Models\TypeVaccin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjetController extends Controller
{
    public function create_projet(Request $request)
    {
        $request->validate([
            "title_projet" => 'required',
            "struturesantes" => 'required',
            "orgid" => 'required'
        ]);

        $user = Auth::user();
        if ($user->checkPermissions('Project', 'create')) {
            $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
            if ($organisation) {
                $projet = ProjetModel::create([
                    'title_projet' => $request->title_projet,
                    'org_make_repport' => $request->org_make_repport,
                    'org_make_oeuvre' => $request->org_make_oeuvre,
                    'identifiant_project' => $request->identifiant_project,
                    'typeprojetid' => $request->typeprojetid,
                    'type_intervention' => $request->type_intervention,
                    'src_financement' => $request->src_financement,
                    'bailleur_de_fond' => $request->bailleur_de_fond,
                    'fond_louer_projet' => $request->fond_louer_projet,
                    'fond_operationel_disponible' => $request->fond_operationel_disponible,
                    'date_debut_projet' => $request->date_debut_projet,
                    'date_fin_projet' => $request->date_fin_projet,
                    'modalite' => $request->modalite,
                    'type_benef' => $request->type_benef,
                    'presence_physique' => $request->presence_physique,
                    'institu_app' => $request->institu_app,
                    'fourniture_info' => $request->fourniture_info,
                    'participation_com' => $request->participation_com,
                    'mecanisme_evaluation' => $request->mecanisme_evaluation,
                    'collect_gestion' => $request->collect_gestion,
                    'approche_app' => $request->approche_app,
                    'resultat_collectif' => $request->resultat_collectif,
                    'axe_strategique' => $request->axe_strategique,
                    'projet_vise' => $request->projet_vise,
                    'odd' => $request->odd,
                    'userid' => $user->id,
                    'orguserid' => $request->orgid,
                    'montant_assiste' => $request->montant_assiste,
                ]);

                $projet->struturesantes()->detach();
                foreach ($request->struturesantes as $item) {
                    $projet->struturesantes()->attach([$projet->id =>
                    [
                        'structureid' => $item
                    ]]);
                }

                //INSERTION INDICATEURS DU PROJET
                //$projet->typeimpact()->detach();
                foreach ($request->impacts as $item) {

                    $projet->typeimpact()->attach([$projet->id =>
                    [
                        'typeimpactid' => $item['typeimpactid'],
                    ]]);
                    $data = IndicateurProjetModel::where('projetid', $projet->id)->where('typeimpactid', $item['typeimpactid'])->first();
                    $data->indicateurs()->detach();
                    foreach ($item['indicateurid'] as $items) {
                        $data->indicateurs()->attach([$data->id =>
                        [
                            'indicateurid' => $items,
                        ]]);
                    }
                }

                return response()->json([
                    "message" => "Success",
                    "code" => 200,
                ], 200);
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
    public function update_projet(Request $request, $id)
    {
        $request->validate([
            "title_projet" => 'required',
            "orgid" => 'required'
        ]);

        $user = Auth::user();
        if ($user->checkPermissions('Project', 'update')) {
            $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
            if ($organisation) {
                if (ProjetModel::where('id', $id)->exists()) {
                    $projet = ProjetModel::where('id', $id)->first();
                    $projet->title_projet = $request->title_projet;
                    $projet->org_make_oeuvre = $request->org_make_oeuvre;
                    $projet->identifiant_project = $request->identifiant_project;
                    $projet->typeprojetid = $request->typeprojetid;
                    $projet->type_intervention = $request->type_intervention;
                    $projet->src_financement = $request->src_financement;
                    $projet->bailleur_de_fond = $request->bailleur_de_fond;
                    $projet->fond_louer_projet = $request->fond_louer_projet;
                    $projet->fond_operationel_disponible = $request->fond_operationel_disponible;
                    $projet->presence_physique = $request->presence_physique;
                    $projet->institu_app = $request->institu_app;
                    $projet->fourniture_info = $request->fourniture_info;
                    $projet->participation_com = $request->participation_com;
                    $projet->mecanisme_evaluation = $request->mecanisme_evaluation;
                    $projet->collect_gestion = $request->collect_gestion;
                    $projet->approche_app = $request->approche_app;
                    $projet->resultat_collectif = $request->resultat_collectif;
                    $projet->axe_strategique = $request->axe_strategique;
                    $projet->projet_vise = $request->projet_vise;
                    $projet->montant_assiste = $request->montant_assiste;
                    $projet->odd = $request->odd;
                    $projet->date_debut_projet = $request->date_debut_projet;
                    $projet->date_fin_projet = $request->date_fin_projet;
                    $projet->type_benef = $request->type_benef;
                    $projet->modalite = $request->modalite;
                    $projet->save();

                    $projet->struturesantes()->detach();
                    foreach ($request->struturesantes as $item) {
                        $projet->struturesantes()->attach([$projet->id =>
                        [
                            'structureid' => $item
                        ]]);
                    }

                    //INSERTION INDICATEURS DU PROJET
                    $projet->typeimpact()->detach();
                    foreach ($request->impacts as $item) {

                        $projet->typeimpact()->attach([$projet->id =>
                        [
                            'typeimpactid' => $item['typeimpactid'],
                        ]]);
                        $data = IndicateurProjetModel::where('projetid', $projet->id)->where('typeimpactid', $item['typeimpactid'])->first();
                        $data->indicateurs()->detach();
                        foreach ($item['indicateurid'] as $items) {
                            $data->indicateurs()->attach([$data->id =>
                            [
                                'indicateurid' => $items,
                            ]]);
                        }
                    }
                } else {
                    return response()->json([
                        "message" => "Cette identifiant n'existe pas!",
                        "code" => 402
                    ], 402);
                }
                return response()->json([
                    "message" => "Success",
                    "code" => 200,
                ]);
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
    public function getStructureByProjet($id)
    {
        $projet = ProjetModel::where('id', $id)->first();
        if ($projet) {
            return response([
                "message" => "success",
                "code" => 200,
                "data" => $projet->struturesantes()->get(),
            ]);
        } else {
            return response()->json([
                "message" => "Cette identifiant n'est pas reconnue dans le système!",
                "code" => 402
            ], 402);
        }
    }
    public function gettype_impact()
    {
        return response()->json([
            "message" => "Liste Type Impact",
            "code" => 200,
            "data" => TypeImpactModel::get(),
        ], 200);
    }

    public function getindicateur($id)
    {
        return response()->json([
            "message" => "Liste des indicateurs par inpact",
            "code" => 200,
            "data" => indicateur::where('type_reponseid', $id)->get(),
        ], 200);
    }

    public function getAll_activites()
    {
        $user = Auth::user();
        if ($user->checkPermissions('Activite', 'read')) {
            return response()->json([
                "message" => "Success",
                "code" => 200,
                "data" => ActiviteProjetModel::with(
                    "cohp_relais",
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
                    'databeneficeatteint',
                    'dataconsultationexterne',
                    'dataconsultationcliniquemobile',
                    'autresinfoprojet',
                    'infosVaccinations.Vaccination',
                )->where('deleted', 0)->orderBy('created_at', 'desc')->get(),
            ], 200);
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function getactivites($orgid)
    {
        $user = Auth::user();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        if ($user->checkPermissions('Project', 'read')) {
            if ($organisation) {
                return response()->json([
                    "message" => "Success",
                    "code" => 200,
                    "data" => ActiviteProjetModel::with(
                        "cohp_relais",
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
                    )->where('orgid', $orgid)->where('deleted', 0)->orderBy('created_at', 'desc')->get(),
                ], 200);
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


    public function getprojet($orgid)
    {
        $user = Auth::user();
        if ($user->checkPermissions('Project', 'read')) {
            $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
            if ($organisation) {
                return response()->json([
                    "message" => "Success",
                    "code" => 200,
                    "data" => ProjetModel::with(
                        'struturesantes.airesante.zonesante.territoir.province',
                        'data_organisation_make_rapport.type_org',
                        'data_organisation_mise_en_oeuvre.type_org',
                        'datatypeimpact.typeimpact',
                        'datatypeimpact.indicateur.indicateur',
                        'typeprojet',
                    )->where('orguserid', $orgid)->get(),
                ], 200);
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

    public function Get_One_project($id, $orgid)
    {
        $user = Auth::user();
        if ($user->checkPermissions('Activite', 'read')) {
            $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
            if ($organisation) {
                return response()->json([
                    "message" => "Success",
                    "code" => 200,
                    "data" => ProjetModel::with(
                        'struturesantes.airesante.zonesante.territoir.province',
                        'data_organisation_make_rapport.type_org',
                        'data_organisation_mise_en_oeuvre.type_org',
                        'datatypeimpact.typeimpact',
                        'datatypeimpact.indicateur.indicateur',
                        'typeprojet',
                    )->where('orguserid', $orgid)->where('id', $id)->first(),
                ], 200);
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

    public function get_all_activites($orgid)
    {
        $user = Auth::user();
        if ($user->checkPermissions('Activite', 'read')) {
            $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
            if ($organisation) {
                return response()->json([
                    "message" => "Success",
                    "code" => 200,
                    "data" => TypeImpactModel::with('datatypeimpact.indicateur.indicateur')->get(),
                ], 200);
            } else {
                return response()->json([
                    "message" => "Vous ne pouvez pas éffectuer cette action",
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

    public function create_rayon_action_projet(Request $request)
    {

        $request->validate([
            'struturesantes' => 'required',
            'orgid' => 'required',
            'projetid' => 'required',
        ]);

        $user = Auth::user();
        if ($user->checkPermissions('Activite', 'create')) {
            $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
            if ($organisation) {
                foreach ($request->struturesantes as $item) {
                    //$projet->struturesantes()->detach();
                    $projet = ProjetModel::where('id', $request->projetid)->first();
                    foreach ($request->struturesantes as $item) {
                        $projet->struturesantes()->attach([$projet->id =>
                        [
                            'structureid' => $item
                        ]]);
                    }
                }
                return response()->json([
                    "message" => "Success",
                    "data" => ProjetModel::with(
                        "projet",
                        "projet.datatypeimpact.typeimpact",
                        "projet.datatypeimpact.indicateur.indicateur",
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
                        'autresinfoprojet',
                        'autresinfoprojet.infosVaccinations.Vaccination',
                    )->where('org_make_repport', $request->orgid)->where('status', 1)->where('deleted', 0)->orderBy('created_at', 'desc')->get()
                ]);
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

    public function update_rayon_action_projet(Request $request, $id)
    {
        $request->validate([
            'pyramide_projet' => 'required',
            'orgid' => 'required',
        ]);
        $user = Auth::user();
        if ($user->checkPermissions('Activite', 'create')) {
            $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
            if ($organisation) {
                //UPDATE PYRAMIDE
                $rayon_action = RayonActionProjetModel::where('id', $id)->first();
                foreach ($request->struturesantes as $item) {

                    $projet = ProjetModel::where('id', $rayon_action->projetid)->first();
                    $rayon_action->struturesantes()->detach();
                    foreach ($request->struturesantes as $item) {
                        $projet->struturesantes()->attach([$projet->id =>
                        [
                            'structureid' => $item
                        ]]);
                    }
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

    public function gettype_projet(Request $request)
    {
        return response()->json([
            "message" => "Success",
            "code" => 200,
            "data" => TypeProjet::get(),
        ], 200);
    }

    public function create_detail_projet(Request $request, $idprojet)
    {
        $request->validate([
            "orgid" => 'required',
        ]);

        $user = Auth::user();
        if ($user->checkPermissions('Activite', 'create')) {
            $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
            $dataprojet = ProjetModel::where('id', $idprojet)->first();
            $typeimpactid = TypeReponseProjet::where('id', $request->typeimpactid)->first();
            if ($request->type === "5W") {
                if ($organisation) {
                    if ($dataprojet) {
                        if ($typeimpactid) {
                            $activity = ActiviteProjetModel::create([
                                'type' => $request->type,
                                "projetid" => $dataprojet->id,
                                "orgid" => $request->orgid,
                                "cohp_relais_id" => $request->cohp_relais,
                                "date_rapportage" => $request->date_rapportage,
                                "structureid" => $request->structureid,
                                "indicateurid" => $request->indicateurid,
                                "typeimpactid" => $typeimpactid->id,
                                "periode_rapportage" => $request->periode_rapportage
                            ]);

                            if ($dataprojet) {
                                BeneficeCibleProjet::create([
                                    'activiteid' => $activity->id,
                                    'orguserid' => $request->orgid,
                                    'homme_cible' => $request->homme_cible,
                                    'femme_cible' =>  $request->femme_cible,

                                    'enfant_garcon_moin_cinq' =>  $request->enfant_garcon_moin_cinq,
                                    'enfant_fille_moin_cinq'  =>  $request->enfant_fille_moin_cinq,
                                    'personne_cible_handicap' =>  $request->personne_cible_handicap,

                                    "garcon_cible_cinq_dix_septe" => $request->garcon_cible_cinq_dix_septe,
                                    "fille_cible_cinq_dix_septe" => $request->fille_cible_cinq_dix_septe,

                                    "homme_cible_dix_huit_cinquante_neuf" => $request->homme_cible_dix_huit_cinquante_neuf,
                                    "femme_cible_dix_huit_cinquante_neuf" => $request->femme_cible_dix_huit_cinquante_neuf,

                                    "homme_cible_plus_cinquante_neuf" => $request->homme_cible_plus_cinquante_neuf,
                                    "femme_cible_plus_cinquante_neuf" => $request->femme_cible_plus_cinquante_neuf,
                                    'total_cible' =>  $request->total_cible,
                                ]);

                                BeneficeAtteintProjet::create([
                                    'activiteid' => $activity->id,
                                    'orguserid' => $request->orgid,
                                    "homme_atteint" => $request->homme_atteint,
                                    "femme_atteint" =>  $request->femme_atteint,

                                    "enfant_garcon_moin_cinq" =>  $request->enfant_garcon_moin_cinq_atteint,
                                    "enfant_fille_moin_cinq" =>  $request->enfant_fille_moin_cinq_atteint,

                                    "personne_atteint_handicap" =>  $request->personne_atteint_handicap,
                                    "garcon_atteint_cinq_dix_septe" => $request->garcon_atteint_cinq_dix_septe,
                                    "fille_atteint_cinq_dix_septe" => $request->fille_atteint_cinq_dix_septe,
                                    "homme_atteint_dix_huit_cinquante_neuf" => $request->homme_atteint_dix_huit_cinquante_neuf,
                                    "femme_atteint_dix_huit_cinquante_neuf" => $request->femme_atteint_dix_huit_cinquante_neuf,
                                    "homme_atteint_plus_cinquante_neuf" => $request->homme_atteint_plus_cinquante_neuf,
                                    "femme_atteint_plus_cinquante_neuf" => $request->femme_atteint_plus_cinquante_neuf,
                                    "total_atteint" => $request->total_atteint
                                ]);

                                ConsultationExterneFosaProjet::create([
                                    'activiteid' => $activity->id,
                                    'orguserid' => $request->orgid,
                                    "consulte_moin_cinq_fosa" => $request->consulte_moin_cinq_fosa,
                                    "consulte_cinq_dix_sept_fosa" => $request->consulte_cinq_dix_sept_fosa,
                                    "homme_fosa_dix_huit_plus_fosa" => $request->homme_fosa_dix_huit_plus_fosa,
                                    "femme_fosa_dix_huit_plus_fosa" => $request->femme_fosa_dix_huit_plus_fosa,
                                ]);

                                ConsultationCliniqueMobileProjet::create([
                                    'activiteid' => $activity->id,
                                    'orguserid' => $request->orgid,
                                    "consulte_moin_cinq_mob" => $request->consulte_moin_cinq_mob,
                                    "consulte_cinq_dix_sept_mob" => $request->consulte_cinq_dix_sept_mob,
                                    "homme_dix_huit_plus_mob" => $request->homme_dix_huit_plus_mob,
                                    "femme_dix_huit_plus_mob" => $request->femme_dix_huit_plus_mob,
                                ]);

                                AutreInfoProjets::create([
                                    "activiteid" => $activity->id,
                                    'orguserid' => $request->orgid,
                                    'description_activite' => $request->description_activite,
                                    'statut_activite' => $request->statut_activite,
                                    "nbr_malnutrition" => $request->nbr_malnutrition,
                                    "remarque" => $request->remarque,
                                    'nbr_accouchement' => $request->nbr_accouchement,
                                    'email' => $request->email,
                                    'phone' => $request->phone,
                                    'date_rapportage' => $request->date_rapportage,
                                    'nbr_cpn' => $request->nbr_cpn,
                                ]);

                                $activity->infosVaccination()->detach();
                                foreach ($request->infosVaccination as $item) {
                                    $activity->infosVaccination()->attach([$activity->id =>
                                    [
                                        'typevaccinid' => $item['typevaccinid'],
                                        'nbr_vaccine' => $item['nbr_vaccine'],
                                    ]]);
                                }
                            } else {
                                return response()->json([
                                    "message" => "Project not found!",
                                    "code" => 402
                                ], 402);
                            }
                        } else {
                            return response()->json([
                                "message" => "Error type impact not found!",
                                "code" => 402
                            ], 402);
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
                if ($dataprojet) {
                    if ($typeimpactid) {
                        $activity = ActiviteProjetModel::create([
                            'type' => $request->type,
                            "projetid" => $dataprojet->id,
                            "orgid" => $request->orgid,
                            "cohp_relais_id" => $request->cohp_relais,
                            "date_rapportage" => $request->date_rapportage,
                            "structureid" => $request->structureid,
                            "indicateurid" => $request->indicateurid,
                            "typeimpactid" => $typeimpactid->id,
                            "periode_rapportage" => $request->periode_rapportage
                        ]);

                        if ($dataprojet) {
                            BeneficeCibleProjet::create([
                                'activiteid' => $activity->id,
                                'orguserid' => $request->orgid,
                                'homme_cible' => $request->homme_cible,
                                'femme_cible' =>  $request->femme_cible,

                                'enfant_garcon_moin_cinq' =>  $request->enfant_garcon_moin_cinq,
                                'enfant_fille_moin_cinq'  =>  $request->enfant_fille_moin_cinq,
                                'personne_cible_handicap' =>  $request->personne_cible_handicap,

                                "garcon_cible_cinq_dix_septe" => $request->garcon_cible_cinq_dix_septe,
                                "fille_cible_cinq_dix_septe" => $request->fille_cible_cinq_dix_septe,

                                "homme_cible_dix_huit_cinquante_neuf" => $request->homme_cible_dix_huit_cinquante_neuf,
                                "femme_cible_dix_huit_cinquante_neuf" => $request->femme_cible_dix_huit_cinquante_neuf,

                                "homme_cible_plus_cinquante_neuf" => $request->homme_cible_plus_cinquante_neuf,
                                "femme_cible_plus_cinquante_neuf" => $request->femme_cible_plus_cinquante_neuf,
                                'total_cible' =>  $request->total_cible,
                            ]);

                            BeneficeAtteintProjet::create([
                                'activiteid' => $activity->id,
                                'orguserid' => $request->orgid,
                                "homme_atteint" => $request->homme_atteint,
                                "femme_atteint" =>  $request->femme_atteint,

                                "enfant_garcon_moin_cinq" =>  $request->enfant_garcon_moin_cinq_atteint,
                                "enfant_fille_moin_cinq" =>  $request->enfant_fille_moin_cinq_atteint,

                                "personne_atteint_handicap" =>  $request->personne_atteint_handicap,
                                "garcon_atteint_cinq_dix_septe" => $request->garcon_atteint_cinq_dix_septe,
                                "fille_atteint_cinq_dix_septe" => $request->fille_atteint_cinq_dix_septe,
                                "homme_atteint_dix_huit_cinquante_neuf" => $request->homme_atteint_dix_huit_cinquante_neuf,
                                "femme_atteint_dix_huit_cinquante_neuf" => $request->femme_atteint_dix_huit_cinquante_neuf,
                                "homme_atteint_plus_cinquante_neuf" => $request->homme_atteint_plus_cinquante_neuf,
                                "femme_atteint_plus_cinquante_neuf" => $request->femme_atteint_plus_cinquante_neuf,
                                "total_atteint" => $request->total_atteint
                            ]);

                            AutreInfoProjets::create([
                                "activiteid" => $activity->id,
                                'orguserid' => $request->orgid,
                                'description_activite' => $request->description_activite,
                                'statut_activite' => $request->statut_activite,
                                "nbr_malnutrition" => $request->nbr_malnutrition,
                                "remarque" => $request->remarque,
                                'nbr_accouchement' => $request->nbr_accouchement,
                                'email' => $request->email,
                                'phone' => $request->phone,
                                'date_rapportage' => $request->date_rapportage,
                                'nbr_cpn' => $request->nbr_cpn,
                            ]);
                        } else {
                            return response()->json([
                                "message" => "Project not found!",
                                "code" => 402
                            ], 402);
                        }
                    } else {
                        return response()->json([
                            "message" => "Error type impact not found!",
                            "code" => 402
                        ], 402);
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
            }
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function update_detail_projet(Request $request, $idprojet)
    {
        $request->validate([
            "orgid" => 'required',
        ]);
        $user = Auth::user();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        if ($user->checkPermissions('Activite', 'update')) {
            if ($organisation) {
                if ($request->type == "5w") {
                    $datactivite = ActiviteProjetModel::where('id', $id)->first();
                    $datactivite->type = $request->type;
                    $datactivite->projetid = $request->projetid;
                    $datactivite->orgid = $request->orgid;
                    $datactivite->date_rapportage = $request->date_rapportage;
                    $datactivite->structureid = $request->structureid;
                    $datactivite->indicateurid = $request->indicateurid;
                    $datactivite->typeimpactid = $request->type_reponse;
                    $datactivite->cohp_relais_id = $request->cohp_relais;
                    $datactivite->periode_rapportage = $request->periode_rapportage;
                    $datactivite->save();

                    if ($datactivite) {
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
                    }
                } else {
                    $datactivite = ActiviteProjetModel::where('id', $id)->first();
                    $datactivite->type = $request->type;
                    $datactivite->projetid = $request->projetid;
                    $datactivite->orgid = $request->orgid;
                    $datactivite->date_rapportage = $request->date_rapportage;
                    $datactivite->structureid = $request->structureid;
                    $datactivite->indicateurid = $request->indicateurid;
                    $datactivite->typeimpactid = $request->type_reponse;
                    $datactivite->cohp_relais_id = $request->cohp_relais;
                    $datactivite->periode_rapportage = $request->periode_rapportage;
                    $datactivite->save();

                    if ($datactivite) {
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
                        return response()->json([
                            "message" => "Success",
                            "code" => 200
                        ], 200);
                    }
                }
            } else {
                return response()->json([
                    "message" => "cette organisationid" . $organisation->id . "n'existe pas",
                    "code" => 402,
                ], 402);
            }
        }
    }

    public function gettypevaccin()
    {
        return response()->json([
            "message" => "Liste des vaccins",
            "code" => 200,
            "data" => TypeVaccin::all()
        ]);
    }
}
