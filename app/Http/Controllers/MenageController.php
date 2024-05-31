<?php

namespace App\Http\Controllers;

use App\Models\AffectationModel;
use App\Models\AffectationPermission;
use App\Models\CalendrierVaccinModel;
use App\Models\CritereMenageModel;
use App\Models\CritereVulModel;
use App\Models\MenageModel;
use App\Models\Permission;
use App\Models\PersonnelModel;
use App\Models\PersonnesModel;
use App\Models\QuestionEnceinteModel;
use App\Models\ReponseEnceinteModel;
use App\Models\RoleMenageModel;
use App\Models\RoleModel;
use App\Models\SiteDeplaceModel;
use App\Models\TypePersonneModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenageController extends Controller
{
    public function create_menage(Request $request)
    {

        $request->validate([
            "site_id" => 'required',
            "taille" => 'required',
            'habitation' => 'required',
            'origine' => 'required',
            'orgid' => 'required',
        ]);

        $user = Auth::user();
        $permission = Permission::where('name', 'create_menage')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        $code = mt_rand(1, 9999999999);
        $codemenage = 'MEN-' . $code;
        $datamenage = MenageModel::where('code_menage', $codemenage)->exists();

        if ($organisation) {
            if ($permission_gap) {
                if ($datamenage) {
                    $code = mt_rand(1, 9999999999);
                    $codemenage = 'MEN-' . $code;
                    $datamenage = MenageModel::create([
                        "code_menage" => $codemenage,
                        "site_id" => $request->site_id,
                        "taille" => $request->taille,
                        'habitation' => $request->habitation,
                        'origine' => $request->origine,
                        'userid' => $user->id,
                    ]);

                    return response()->json([
                        "message" => "Traitement reussi avec succès",
                        "code" => 200,
                        "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $datamenage->id)->first()
                    ], 200);
                } else {
                    $datamenage = MenageModel::create([
                        "code_menage" => $codemenage,
                        "site_id" => $request->site_id,
                        "taille" => $request->taille,
                        'habitation' => $request->habitation,
                        'origine' => $request->origine,
                        'userid' => $user->id,
                    ]);

                    return response()->json([
                        "message" => "Traitement reussi avec succès",
                        "code" => 200,
                        "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $datamenage->id)->first()
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

    public function create_menage_desktop(Request $request)
    {

        $request->validate([
            "site_id" => 'required',
            "taille" => 'required',
            'habitation' => 'required',
            'origine' => 'required',
            'orgid' => 'required',
        ]);

        $user = Auth::user();
        $permission = Permission::where('name', 'create_menage')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        $code = mt_rand(1, 9999999999);
        $codemenage = 'MEN-' . $code;
        $datamenage = MenageModel::where('code_menage', $codemenage)->exists();

        $site = SiteDeplaceModel::where('name', $request->site_id)->first();
        if ($organisation) {
            if ($permission_gap) {
                if ($datamenage) {
                    $code = mt_rand(1, 9999999999);
                    $codemenage = 'MEN-' . $code;
                    $datamenage = MenageModel::create([
                        "code_menage" => $codemenage,
                        "site_id" => $site->id,
                        "taille" => $request->taille,
                        'habitation' => $request->habitation,
                        'origine' => $request->origine,
                        'userid' => $user->id,
                    ]);

                    return response()->json([
                        "message" => "Traitement reussi avec succès",
                        "code" => 200,
                        "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $datamenage->id)->first()
                    ], 200);
                } else {
                    $datamenage = MenageModel::create([
                        "code_menage" => $codemenage,
                        "site_id" => $site->id,
                        "taille" => $request->taille,
                        'habitation' => $request->habitation,
                        'origine' => $request->origine,
                        'userid' => $user->id,
                    ]);

                    return response()->json([
                        "message" => "Traitement reussi avec succès",
                        "code" => 200,
                        "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $datamenage->id)->first()
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
    public function listmenage()
    {
        return response()->json([
            "message" => "Liste des menages",
            "code" => 200,
            "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')
                ->where('status', 0)->orderBy('created_at', 'DESC')->get()
        ], 200);
    }

    public function delete_menage(Request $request)
    {
        $user = Auth::user();
        $permission = Permission::where('name', 'create_menage')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        $datamenage = MenageModel::where('id', $request->id)->first();
        if ($organisation) {
            if ($permission_gap) {
                if ($datamenage) {
                    $datamenage->deleted = 1;
                    $datamenage->save();
                    return response()->json([
                        "message" => "Traitement reussi avec succès",
                        "code" => 200,
                        "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')
                            ->where('id', $datamenage->id)->where('deleted', 0)->first()
                    ], 200);
                } else {
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

    public function updatemenage(Request $request, $id)
    {
        $request->validate([
            "site_id" => 'required',
            "taille" => 'required',
            'habitation' => 'required',
            'origine' => 'required',
            'orgid' => 'required',
        ]);
        $user = Auth::user();
        $permission = Permission::where('name', 'update_menage')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        $datamenage = MenageModel::where('id', $id)->first();
        if ($organisation) {
            if ($permission_gap) {
                if ($datamenage) {

                    $datamenage->taille = $request->taille;
                    $datamenage->habitation = $request->habitation;
                    $datamenage->origine = $request->origine;
                    $datamenage->userid = $request->userid;
                    $datamenage->site_id = $request->site_id;
                    $datamenage->save();

                    return response()->json([
                        "message" => "Traitement reussi avec succès",
                        "code" => 200,
                        "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $datamenage->id)->first()
                    ], 200);
                } else {
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

    public function updatemenage_desktop(Request $request, $id)
    {
        $request->validate([
            "site_id" => 'required',
            "taille" => 'required',
            'habitation' => 'required',
            'origine' => 'required',
            'orgid' => 'required',
        ]);

        $user = Auth::user();
        $permission = Permission::where('name', 'update_menage')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        $datamenage = MenageModel::where('id', $id)->first();
        $datasite = SiteDeplaceModel::where('name', $request->site_id)->first();
        if ($organisation) {
            if ($permission_gap) {
                if ($datamenage) {
                    $datamenage->taille = $request->taille;
                    $datamenage->habitation = $request->habitation;
                    $datamenage->origine = $request->origine;
                    $datamenage->userid = $request->userid;
                    $datamenage->site_id = $datasite->id;
                    $datamenage->save();
                    return response()->json([
                        "message" => "Modification reussie avec succès",
                        "code" => 200,
                        "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $datamenage->id)->first()
                    ], 200);
                } else {
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

    public function listcritere()
    {
        return response()->json([
            "message" => "Liste des critères de vulnerable",
            "code" => 200,
            "data" => CritereVulModel::all()
        ], 200);
    }

    public function listtypepersonne()
    {
        return response()->json([
            "message" => "Liste type personne dans un menage",
            "code" => 200,
            "data" => TypePersonneModel::all()
        ], 200);
    }

    public function listerolemenage()
    {
        return response()->json([
            "message" => "Liste de roles dans un menage",
            "code" => 200,
            "data" => RoleMenageModel::all()
        ], 200);
    }
    public function listequestion()
    {
        return response()->json([
            "message" => "Liste des questions (Si la personne est une femme)",
            "code" => 200,
            "data" => QuestionEnceinteModel::all()
        ], 200);
    }

    public function updatepersonne(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required',
            'postnom' => 'required',
            'prenom' => 'required',
            'sexe' => 'required',
            'roleid' => 'required',
            'typepersonneid' => 'required',
            'nom_pere' => 'required',
            'lieu_naissance' => 'required',
            'datenaiss' => 'required',
            "menageid" => 'required',
            "nom_mere" => 'required',
            "orgid" => 'required',
        ]);

        $user = Auth::user();
        $image = UtilController::uploadImageUrl($request->photo, '/uploads/vulnerable/');
        $permission = Permission::where('name', 'update_personne')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();

        $datamenage = MenageModel::where('id', $request->menageid)->first();
        $datapersonne = PersonnesModel::where('id', $id)->where('manageid', $request->menageid)->first();
        if ($organisation) {
            if ($permission_gap) {
                if ($datamenage) {
                    if ($datapersonne) {
                        if ($image) {
                            $datapersonne->nom = $request->nom;
                            $datapersonne->postnom = $request->postnom;
                            $datapersonne->prenom = $request->prenom;
                            $datapersonne->sexe = $request->sexe;
                            $datapersonne->roleid = $request->roleid;
                            $datapersonne->typepersonneid = $request->typepersonneid;
                            $datapersonne->nom_pere = $request->nom_pere;
                            $datapersonne->probleme_sante = $request->probleme_sante;
                            $datapersonne->lieu_naissance = $request->lieu_naissance;
                            $datapersonne->datenaiss = $request->datenaiss;
                            $datapersonne->sous_moustiquaire = $request->sous_moustiquaire;
                            $datapersonne->photo = $image;
                            $datapersonne->calendrier = $request->calendrier;
                            $datapersonne->nom_mere = $request->nom_mere;
                            $datapersonne->manageid = $request->menageid;
                            $datapersonne->femme_enceinte = $request->femme_enceint;
                            $datapersonne->femme_allaitante = $request->femme_allaitante;
                            $datapersonne->save();

                            if (now()->diffInDays($request->datenaiss, true) < 1825) {
                                $reponse = CalendrierVaccinModel::where('personneid', $id);
                                $reponse->name = $request->calendrier;
                                $reponse->save();
                            }

                            $personne = PersonnesModel::where('id', $datapersonne->id)->first();
                            //INSERTION CRISE GAP
                            if ($personne) {
                                $personne->dataallcritere()->detach();
                                foreach ($request->critere as $item) {
                                    $personne->dataallcritere()->attach([$datapersonne->id =>
                                    [
                                        'cretereid' => $item,
                                    ]]);
                                }
                            }


                            return response()->json([
                                "message" => "La modification réussie avec succès",
                                "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $request->menageid)->get()
                            ], 200);
                        } else {
                            $datapersonne->nom = $request->nom;
                            $datapersonne->postnom = $request->postnom;
                            $datapersonne->prenom = $request->prenom;
                            $datapersonne->sexe = $request->sexe;
                            $datapersonne->roleid = $request->roleid;
                            $datapersonne->typepersonneid = $request->typepersonneid;
                            $datapersonne->nom_pere = $request->nom_pere;
                            $datapersonne->probleme_sante = $request->probleme_sante;
                            $datapersonne->lieu_naissance = $request->lieu_naissance;
                            $datapersonne->datenaiss = $request->datenaiss;
                            $datapersonne->calendrier = $request->calendrier;
                            $datapersonne->sous_moustiquaire = $request->sous_moustiquaire;
                            $datapersonne->nom_mere = $request->nom_mere;
                            $datapersonne->manageid = $request->menageid;
                            $datapersonne->femme_enceinte = $request->femme_enceint;
                            $datapersonne->femme_allaitante = $request->femme_allaitante;
                            $datapersonne->save();

                            $personne = PersonnesModel::where('id', $datapersonne->id)->first();
                            //INSERTION CRISE GAP
                            if ($personne) {
                                $personne->dataallcritere()->detach();
                                foreach ($request->critere as $item) {
                                    $personne->dataallcritere()->attach([$datapersonne->id =>
                                    [
                                        'cretereid' => $item,
                                    ]]);
                                }
                            }

                            return response()->json([
                                "message" => "La modification réussie avec succès",
                                "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $request->menageid)->get()
                            ], 200);
                        }
                    } else {
                        return response()->json([
                            "message" => "Erreur de la modification avec cette id :" . $id,
                        ], 422);
                    }
                } else {
                    return response()->json([
                        "message" => "cette personne (" .  $datapersonne->nom = $request->nom . " " .
                            $datapersonne->postnom = $request->postnom . " ) n'existe pas",
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
                "code" => 402
            ], 402);
        }
    }

    public function updatepersonne_empreinte_digital(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required',
            'postnom' => 'required',
            'prenom' => 'required',
            'sexe' => 'required',
            'roleid' => 'required',
            'typepersonneid' => 'required',
            'nom_pere' => 'required',
            'lieu_naissance' => 'required',
            'datenaiss' => 'required',
            "manageid" => 'required',
            "nom_mere" => 'required',
            "orgid" => 'required',
        ]);

        $user = Auth::user();
        $image = UtilController::uploadImageUrl($request->photo, '/uploads/vulnerable/');
        $permission = Permission::where('name', 'update_personne')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();

        $datamenage = MenageModel::where('id', $request->manageid)->first();
        $datapersonne = PersonnesModel::where('id', $id)->where('manageid', $datamenage->id)->first();
        $roleid = RoleMenageModel::where('name', $request->roleid)->first();
        $typepersonneid = TypePersonneModel::where('name', $request->typepersonneid)->first();

        if ($organisation) {
            if ($permission_gap) {
                if ($datamenage) {
                    if ($datapersonne) {
                        if ($image) {
                            $datapersonne->nom = $request->nom;
                            $datapersonne->postnom = $request->postnom;
                            $datapersonne->prenom = $request->prenom;
                            $datapersonne->sexe = $request->sexe;
                            $datapersonne->roleid = $roleid->id;
                            $datapersonne->typepersonneid = $typepersonneid->id;
                            $datapersonne->nom_pere = $request->nom_pere;
                            $datapersonne->probleme_sante = $request->probleme_sante;
                            $datapersonne->lieu_naissance = $request->lieu_naissance;
                            $datapersonne->calendrier = $request->calendrier;
                            $datapersonne->datenaiss = $request->datenaiss;
                            $datapersonne->sous_moustiquaire = $request->sous_moustiquaire;
                            $datapersonne->empreinte_digital = $request->empreinte_digital;
                            //$datapersonne->photo = "https://apiafiagap.cosamed.org/public/uploads/user/a01f3ca6e3e4ece8e1a30696f52844bc.png";
                            $datapersonne->nom_mere = $request->nom_mere;
                            $datapersonne->manageid = $datamenage->id;
                            $datapersonne->save();

                            $personne = PersonnesModel::where('id', $datapersonne->id)->first();
                            //INSERTION CRISE GAP
                            if ($personne) {
                                $personne->dataallcritere()->detach();
                                foreach ($request->critere as $item) {
                                    $personne->dataallcritere()->attach([$datapersonne->id =>
                                    [
                                        'cretereid' => $item,
                                    ]]);
                                }
                            }
                            return response()->json([
                                "message" => "success",
                                "code" => 200,
                                "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $datamenage->id)->get()
                            ], 200);
                        } else {
                            $datapersonne->nom = $request->nom;
                            $datapersonne->postnom = $request->postnom;
                            $datapersonne->prenom = $request->prenom;
                            $datapersonne->sexe = $request->sexe;
                            $datapersonne->roleid = $roleid->id;
                            $datapersonne->typepersonneid = $typepersonneid->id;
                            $datapersonne->nom_pere = $request->nom_pere;
                            $datapersonne->probleme_sante = $request->probleme_sante;
                            $datapersonne->lieu_naissance = $request->lieu_naissance;
                            $datapersonne->datenaiss = $request->datenaiss;
                            $datapersonne->calendrier = $request->calendrier;
                            $datapersonne->sous_moustiquaire = $request->sous_moustiquaire;
                            $datapersonne->empreinte_digital = $request->empreinte_digital;
                            $datapersonne->nom_mere = $request->nom_mere;
                            $datapersonne->manageid = $datamenage->id;
                            $datapersonne->photo = "https://apiafiagap.cosamed.org/public/uploads/user/a01f3ca6e3e4ece8e1a30696f52844bc.png";
                            $datapersonne->save();

                            // $personne = PersonnesModel::where('id', $datapersonne->id)->first();
                            // //INSERTION CRISE GAP
                            // if ($personne) {
                            //     $personne->dataallcritere()->detach();
                            //     foreach ($request->critere as $item) {
                            //         $personne->dataallcritere()->attach([$datapersonne->id =>
                            //         [
                            //             'cretereid' => $item,
                            //         ]]);
                            //     }
                            // }
                            return response()->json([
                                "message" => "success",
                                "code" => 200,
                                "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $datamenage->id)->get()
                            ], 200);
                        }
                    } else {
                        return response()->json([
                            "message" => "Erreur de la modification avec cette id :" . $id,
                            "code" => 422,
                        ], 422);
                    }
                } else {
                    return response()->json([
                        "code" => 422,
                        "message" => "cette personne (" .  $datapersonne->nom = $request->nom . " " .
                            $datapersonne->postnom = $request->postnom . " ) n'existe pas",
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
                "code" => 402
            ], 402);
        }
    }

    public function create_personne(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'postnom' => 'required',
            'prenom' => 'required',
            'sexe' => 'required',
            'roleid' => 'required',
            'typepersonneid' => 'required',
            'nom_pere' => 'required',
            'lieu_naissance' => 'required',
            'datenaiss' => 'required',

            "menageid" => 'required',
            "nom_mere" => 'required',
            "orgid" => 'required',
            "critere" => 'required',
        ]);

        $image = UtilController::uploadImageUrl($request->photo, '/uploads/vulnerable/');
        $user = Auth::user();
        $permission = Permission::where('name', 'create_personne')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {

                $datarole = RoleMenageModel::where('id', $request->roleid)->first();
                $datamenage_role = PersonnesModel::where('roleid', $request->roleid)
                    ->where('manageid', $request->menageid)->first();

                $datatype = TypePersonneModel::where('id', $request->typepersonneid)->first();
                $datamenage = PersonnesModel::where('typepersonneid', $request->typepersonneid)
                    ->where('manageid', $request->menageid)->first();


                $menage = MenageModel::where('id', $request->menageid)->first();
                $personne = PersonnesModel::where('manageid', $menage->id)->get();

                if ($menage->taille > (count($personne))) {

                    if (!PersonnesModel::find($request->phone_1)) {
                        if (!PersonnesModel::find($request->phone_2)) {
                            if (
                                $datarole->name == "grand mére"
                                || $datarole->name == "grand père"
                                || $datarole->name == "grand frère"
                                || $datarole->name == "grand soeur"
                                || $datarole->name == "petite soeur"
                                || $datarole->name == "petit frère"
                                || $datarole->name == "Neveux"
                                || $datarole->name == "Nièce"
                            ) {
                                if ($datatype->name == "Non") {

                                    $datapersonne = PersonnesModel::create([
                                        'nom' => $request->nom,
                                        'postnom' => $request->postnom,
                                        'prenom' => $request->prenom,
                                        'sexe' => $request->sexe,
                                        'phone_1' => $request->phone_1,
                                        'phone_2' => $request->phone_2,
                                        'roleid' => $request->roleid,
                                        'typepersonneid' => $request->typepersonneid,
                                        'calendrier' => $request->calendrier,
                                        'nom_pere' => $request->nom_pere,
                                        'probleme_sante' => $request->probleme_sante,
                                        'lieu_naissance' => $request->lieu_naissance,
                                        'datenaiss' => $request->datenaiss,
                                        'sous_moustiquaire' => $request->sous_moustiquaire,
                                        'photo' =>  $image,
                                        'nom_mere' => $request->nom_mere,
                                        'manageid' => $request->menageid,
                                        'femme_enceinte' => $request->femme_enceinte,
                                        'femme_allaitante' => $request->femme_allaitante,
                                    ]);


                                    if (now()->diffInDays($request->datenaiss, true) < 1825) {
                                        CalendrierVaccinModel::create([
                                            'name' => $request->calendrier,
                                            'personneid' => $datapersonne->id,
                                        ]);
                                    }

                                    $personne = PersonnesModel::where('id', $datapersonne->id)->first();
                                    //INSERTION CRISE GAP
                                    if ($personne) {
                                        $personne->dataallcritere()->detach();
                                        foreach ($request->critere as $item) {
                                            $personne->dataallcritere()->attach([$datapersonne->id =>
                                            [
                                                'cretereid' => $item,
                                            ]]);
                                        }
                                    }
                                    return response()->json([
                                        "message" => "Traitement reussi avec succès",
                                        "code" => 200,
                                        "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $request->menageid)->get()
                                    ], 200);
                                } else {
                                    if ($datamenage) {
                                        return response()->json([
                                            "message" => "Le" . " " . $datatype->name . " " . "existe déjà dans ce menage!",
                                            "code" => 402,
                                            "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')
                                                ->where('id', $request->menageid)->get()
                                        ], 402);
                                    } else {
                                        $datapersonne = PersonnesModel::create([
                                            'nom' => $request->nom,
                                            'postnom' => $request->postnom,
                                            'prenom' => $request->prenom,
                                            'sexe' => $request->sexe,
                                            'phone_1' => $request->phone_1,
                                            'phone_2' => $request->phone_2,
                                            'roleid' => $request->roleid,
                                            'typepersonneid' => $request->typepersonneid,
                                            'nom_pere' => $request->nom_pere,
                                            'probleme_sante' => $request->probleme_sante,
                                            'lieu_naissance' => $request->lieu_naissance,
                                            'datenaiss' => $request->datenaiss,
                                            'calendrier' => $request->calendrier,
                                            'sous_moustiquaire' => $request->sous_moustiquaire,
                                            'photo' => $image,
                                            'nom_mere' => $request->nom_mere,
                                            'manageid' => $request->menageid,
                                            'femme_enceinte' => $request->femme_enceinte,
                                            'femme_allaitante' => $request->femme_allaitante,
                                        ]);
                                        if (now()->diffInDays($request->datenaiss, true) < 1825) {
                                            CalendrierVaccinModel::create([
                                                'name' => $request->calendrier,
                                                'personneid' => $datapersonne->id,
                                            ]);
                                        }
                                        $personne = PersonnesModel::where('id', $datapersonne->id)->first();
                                        //INSERTION CRISE GAP
                                        if ($personne) {
                                            $personne->dataallcritere()->detach();
                                            foreach ($request->critere as $item) {
                                                $personne->dataallcritere()->attach([$datapersonne->id =>
                                                [
                                                    'cretereid' => $item,
                                                ]]);
                                            }
                                        }
                                        return response()->json([
                                            "message" => "Traitement reussi avec succès",
                                            "code" => 200,
                                            "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $request->menageid)->get()
                                        ], 200);
                                    }
                                }
                            } else {
                                if ($datamenage_role) {
                                    return response()->json([
                                        "message" => "Le" . " " . $datarole->name . " " . "existe déjà dans ce menage!",
                                        "code" => 402,
                                        "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')
                                            ->where('id', $request->menageid)->get()
                                    ], 402);
                                } else {
                                    if ($datatype->name == "Non") {

                                        $datapersonne = PersonnesModel::create([
                                            'nom' => $request->nom,
                                            'postnom' => $request->postnom,
                                            'prenom' => $request->prenom,
                                            'sexe' => $request->sexe,
                                            'phone_1' => $request->phone_1,
                                            'phone_2' => $request->phone_2,
                                            'roleid' => $request->roleid,
                                            'typepersonneid' => $request->typepersonneid,
                                            'nom_pere' => $request->nom_pere,
                                            'probleme_sante' => $request->probleme_sante,
                                            'lieu_naissance' => $request->lieu_naissance,
                                            'datenaiss' => $request->datenaiss,
                                            'calendrier' => $request->calendrier,
                                            'sous_moustiquaire' => $request->sous_moustiquaire,
                                            'photo' => $image,
                                            'nom_mere' => $request->nom_mere,
                                            'manageid' => $request->menageid,
                                            'femme_enceinte' => $request->femme_enceinte,
                                            'femme_allaitante' => $request->femme_allaitante,
                                        ]);


                                        if (now()->diffInDays($request->datenaiss, true) < 1825) {
                                            CalendrierVaccinModel::create([
                                                'name' => $request->calendrier,
                                                'personneid' => $datapersonne->id,
                                            ]);
                                        }

                                        $personne = PersonnesModel::where('id', $datapersonne->id)->first();
                                        //INSERTION CRISE GAP
                                        if ($personne) {
                                            $personne->dataallcritere()->detach();
                                            foreach ($request->critere as $item) {
                                                $personne->dataallcritere()->attach([$datapersonne->id =>
                                                [
                                                    'cretereid' => $item,
                                                ]]);
                                            }
                                        }
                                        return response()->json([
                                            "message" => "Traitement reussi avec succès",
                                            "code" => 200,
                                            "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $request->menageid)->get()
                                        ], 200);
                                    } else {
                                        if ($datamenage) {
                                            return response()->json([
                                                "message" => "Le" . " " . $datatype->name . " " . "existe déjà dans ce menage!",
                                                "code" => 402,
                                                "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')
                                                    ->where('id', $request->menageid)->get()
                                            ], 402);
                                        } else {
                                            $datapersonne = PersonnesModel::create([
                                                'nom' => $request->nom,
                                                'postnom' => $request->postnom,
                                                'prenom' => $request->prenom,
                                                'sexe' => $request->sexe,
                                                'phone_1' => $request->phone_1,
                                                'phone_2' => $request->phone_2,
                                                'roleid' => $request->roleid,
                                                'typepersonneid' => $request->typepersonneid,
                                                'nom_pere' => $request->nom_pere,
                                                'probleme_sante' => $request->probleme_sante,
                                                'lieu_naissance' => $request->lieu_naissance,
                                                'datenaiss' => $request->datenaiss,
                                                'calendrier' => $request->calendrier,
                                                'sous_moustiquaire' => $request->sous_moustiquaire,
                                                'photo' => $image,
                                                'nom_mere' => $request->nom_mere,
                                                'manageid' => $request->menageid,
                                                'femme_enceinte' => $request->femme_enceinte,
                                                'femme_allaitante' => $request->femme_allaitante,
                                            ]);


                                            if (now()->diffInDays($request->datenaiss, true) < 1825) {
                                                CalendrierVaccinModel::create([
                                                    'name' => $request->calendrier,
                                                    'personneid' => $datapersonne->id,
                                                ]);
                                            }

                                            $personne = PersonnesModel::where('id', $datapersonne->id)->first();
                                            //INSERTION CRISE GAP
                                            if ($personne) {
                                                $personne->dataallcritere()->detach();
                                                foreach ($request->critere as $item) {
                                                    $personne->dataallcritere()->attach([$datapersonne->id =>
                                                    [
                                                        'cretereid' => $item,
                                                    ]]);
                                                }
                                            }

                                            return response()->json([
                                                "message" => "Traitement reussi avec succès",
                                                "code" => 200,
                                                "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $request->menageid)->get()
                                            ], 200);
                                        }
                                    }
                                }
                            }
                        } else {
                            return response()->json([
                                "message" => "Le numèro de téléphone 1 existe déjà dans le système!",
                                "code" => 402
                            ], 402);
                        }
                    } else {
                        return response()->json([
                            "message" => "Le numèro de téléphone 2 existe déjà dans le système!",
                            "code" => 402
                        ], 402);
                    }
                } else {
                    return response()->json([
                        "message" => "La taille de menage est déjà atteint! veiller contacter l'administrateur système!",
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
                "code" => 402
            ], 402);
        }
    }
    public function create_personne_empreinte(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'postnom' => 'required',
            'prenom' => 'required',
            'sexe' => 'required',
            'roleid' => 'required',
            'typepersonneid' => 'required',
            'nom_pere' => 'required',
            'lieu_naissance' => 'required',
            'datenaiss' => 'required',
            "manageid" => 'required',
            "nom_mere" => 'required',
            "orgid" => 'required',

        ]);

        $image = UtilController::uploadImageUrl($request->photo, '/uploads/vulnerable/');
        $user = Auth::user();
        $image = UtilController::uploadImageUrl($request->photo, '/uploads/vulnerable/');
        $permission = Permission::where('name', 'update_personne')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();

        $datamenage = MenageModel::where('id', $request->manageid)->first();
        $roleid = RoleMenageModel::where('name', $request->roleid)->first();
        $typepersonneid = TypePersonneModel::where('name', $request->typepersonneid)->first();

        if ($organisation) {
            if ($permission_gap) {

                $datarole = RoleMenageModel::where('id', $roleid->id)->first();
                $datamenage_role = PersonnesModel::where('roleid', $datarole->id)
                    ->where('manageid', $request->menageid)->first();

                $datatype = TypePersonneModel::where('id', $typepersonneid->id)->first();
                $datamenage = PersonnesModel::where('typepersonneid', $datatype->id)
                    ->where('manageid', $request->menageid)->first();


                $menage = MenageModel::where('id', $request->manageid)->first();
                $personne = PersonnesModel::where('manageid', $menage->id)->get();

                if ($menage->taille > (count($personne))) {

                    if (!PersonnesModel::find($request->phone_1)) {
                        if (!PersonnesModel::find($request->phone_2)) {
                            if (
                                $datarole->name == "grand mére"
                                || $datarole->name == "grand père"
                                || $datarole->name == "grand frère"
                                || $datarole->name == "grand soeur"
                                || $datarole->name == "petite soeur"
                                || $datarole->name == "petit frère"
                                || $datarole->name == "Neveux"
                                || $datarole->name == "Nièce"
                            ) {
                                if ($datatype->name == "Non") {

                                    $datapersonne = PersonnesModel::create([
                                        'nom' => $request->nom,
                                        'postnom' => $request->postnom,
                                        'prenom' => $request->prenom,
                                        'sexe' => $request->sexe,
                                        'phone_1' => $request->phone_1,
                                        'phone_2' => $request->phone_2,
                                        'roleid' => $roleid->id,
                                        'typepersonneid' => $typepersonneid->id,
                                        'nom_pere' => $request->nom_pere,
                                        'probleme_sante' => $request->probleme_sante,
                                        'lieu_naissance' => $request->lieu_naissance,
                                        'datenaiss' => $request->datenaiss,
                                        'calendrier' => $request->calendrier,
                                        'sous_moustiquaire' => $request->sous_moustiquaire,
                                        'nom_mere' => $request->nom_mere,
                                        'manageid' => $request->manageid,
                                        'femme_enceinte' => $request->femme_enceinte,
                                        'femme_allaitante' => $request->femme_allaitante,
                                        'empreinte_digital' => $request->empreinte_digital,
                                        'photo' => "https://apiafiagap.cosamed.org/public/uploads/user/a01f3ca6e3e4ece8e1a30696f52844bc.png",
                                    ]);

                                    // $personne = PersonnesModel::where('id', $datapersonne->id)->first();
                                    // //INSERTION CRISE GAP
                                    // if ($personne) {
                                    //     $personne->datapersonne()->detach();
                                    //     foreach ($request->critere as $item) {
                                    //         $personne->datapersonne()->attach([$datapersonne->id =>
                                    //         [
                                    //             'cretereid' => $item,
                                    //         ]]);
                                    //     }
                                    // }

                                    //dataallcritere

                                    return response()->json([
                                        "message" => "Traitement reussi avec succès",
                                        "code" => 200,
                                        "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $request->menageid)->get()
                                    ], 200);
                                } else {
                                    if ($datamenage) {
                                        return response()->json([
                                            "message" => "Le" . " " . $datatype->name . " " . "existe déjà dans ce menage!",
                                            "code" => 402,
                                            "data" => MenageModel::with('dataallcritere.dataallcritere', 'datapersonne.datatype_personne', 'datapersonne.datarole')
                                                ->where('id', $request->manageid)->get()
                                        ], 402);
                                    } else {
                                        $datapersonne = PersonnesModel::create([
                                            'nom' => $request->nom,
                                            'postnom' => $request->postnom,
                                            'prenom' => $request->prenom,
                                            'sexe' => $request->sexe,
                                            'phone_1' => $request->phone_1,
                                            'phone_2' => $request->phone_2,
                                            'roleid' => $roleid->id,
                                            'typepersonneid' => $typepersonneid->id,
                                            'nom_pere' => $request->nom_pere,
                                            'probleme_sante' => $request->probleme_sante,
                                            'lieu_naissance' => $request->lieu_naissance,
                                            'datenaiss' => $request->datenaiss,
                                            'calendrier' => $request->calendrier,
                                            'sous_moustiquaire' => $request->sous_moustiquaire,
                                            'nom_mere' => $request->nom_mere,
                                            'manageid' => $request->manageid,
                                            'femme_enceinte' => $request->femme_enceinte,
                                            'femme_allaitante' => $request->femme_allaitante,
                                            'empreinte_digital' => $request->empreinte_digital,
                                            'photo' => "https://apiafiagap.cosamed.org/public/uploads/user/a01f3ca6e3e4ece8e1a30696f52844bc.png",
                                        ]);

                                        // $personne = PersonnesModel::where('id', $datapersonne->id)->first();
                                        // //INSERTION CRISE GAP
                                        // if ($personne) {
                                        //     $personne->datapersonne()->detach();
                                        //     foreach ($request->critere as $item) {
                                        //         $personne->datapersonne()->attach([$datapersonne->id =>
                                        //         [
                                        //             'cretereid' => $item,
                                        //         ]]);
                                        //     }
                                        // }

                                        return response()->json([
                                            "message" => "Traitement reussi avec succès",
                                            "code" => 200,
                                            "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $request->manageid)->get()
                                        ], 200);
                                    }
                                }
                            } else {
                                if ($datamenage_role) {
                                    return response()->json([
                                        "message" => "Le" . " " . $datarole->name . " " . "existe déjà dans ce menage!",
                                        "code" => 402,
                                        "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')
                                            ->where('id', $request->manageid)->get()
                                    ], 402);
                                } else {
                                    if ($datatype->name == "Non") {

                                        $datapersonne = PersonnesModel::create([
                                            'nom' => $request->nom,
                                            'postnom' => $request->postnom,
                                            'prenom' => $request->prenom,
                                            'sexe' => $request->sexe,
                                            'phone_1' => $request->phone_1,
                                            'phone_2' => $request->phone_2,
                                            'roleid' => $roleid->id,
                                            'typepersonneid' => $typepersonneid->id,
                                            'nom_pere' => $request->nom_pere,
                                            'probleme_sante' => $request->probleme_sante,
                                            'lieu_naissance' => $request->lieu_naissance,
                                            'datenaiss' => $request->datenaiss,
                                            'calendrier' => $request->calendrier,
                                            'sous_moustiquaire' => $request->sous_moustiquaire,
                                            'nom_mere' => $request->nom_mere,
                                            'manageid' => $request->manageid,
                                            'femme_enceinte' => $request->femme_enceinte,
                                            'femme_allaitante' => $request->femme_allaitante,
                                            'empreinte_digital' => $request->empreinte_digital,
                                            'photo' => "https://apiafiagap.cosamed.org/public/uploads/user/a01f3ca6e3e4ece8e1a30696f52844bc.png",
                                        ]);

                                        // $personne = PersonnesModel::where('id', $datapersonne->id)->first();
                                        // //INSERTION CRISE GAP
                                        // if ($personne) {
                                        //     $personne->dataallcritere()->detach();
                                        //     foreach ($request->critere as $item) {
                                        //         $personne->dataallcritere()->attach([$datapersonne->id =>
                                        //         [
                                        //             'cretereid' => $item,
                                        //         ]]);
                                        //     }
                                        // }

                                        return response()->json([
                                            "message" => "Traitement reussi avec succès",
                                            "code" => 200,
                                            "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $request->manageid)->get()
                                        ], 200);
                                    } else {
                                        if ($datamenage) {
                                            return response()->json([
                                                "message" => "Le" . " " . $datatype->name . " " . "existe déjà dans ce menage!",
                                                "code" => 402,
                                                "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')
                                                    ->where('id', $request->menageid)->get()
                                            ], 402);
                                        } else {
                                            $datapersonne = PersonnesModel::create([
                                                'nom' => $request->nom,
                                                'postnom' => $request->postnom,
                                                'prenom' => $request->prenom,
                                                'sexe' => $request->sexe,
                                                'phone_1' => $request->phone_1,
                                                'phone_2' => $request->phone_2,
                                                'roleid' => $roleid->id,
                                                'typepersonneid' => $typepersonneid->id,
                                                'nom_pere' => $request->nom_pere,
                                                'probleme_sante' => $request->probleme_sante,
                                                'lieu_naissance' => $request->lieu_naissance,
                                                'datenaiss' => $request->datenaiss,
                                                'calendrier' => $request->calendrier,
                                                'sous_moustiquaire' => $request->sous_moustiquaire,
                                                'nom_mere' => $request->nom_mere,
                                                'manageid' => $request->manageid,
                                                'femme_enceinte' => $request->femme_enceinte,
                                                'femme_allaitante' => $request->femme_allaitante,
                                                'empreinte_digital' => $request->empreinte_digital,
                                                'photo' => "https://apiafiagap.cosamed.org/public/uploads/user/a01f3ca6e3e4ece8e1a30696f52844bc.png",
                                            ]);


                                            // $personne = PersonnesModel::where('id', $datapersonne->id)->first();
                                            // //INSERTION CRISE GAP
                                            // if ($personne) {
                                            //     $personne->dataallcritere()->detach();
                                            //     foreach ($request->critere as $item) {
                                            //         $personne->dataallcritere()->attach([$datapersonne->id =>
                                            //         [
                                            //             'cretereid' => $item,
                                            //         ]]);
                                            //     }
                                            // }

                                            return response()->json([
                                                "message" => "Traitement reussi avec succès",
                                                "code" => 200,
                                                "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->orderBy('created_at', 'desc')->where('id', $request->manageid)->get()
                                            ], 200);
                                        }
                                    }
                                }
                            }
                        } else {
                            return response()->json([
                                "message" => "Le numèro de téléphone 1 existe déjà dans le système!",
                                "code" => 402
                            ], 402);
                        }
                    } else {
                        return response()->json([
                            "message" => "Le numèro de téléphone 2 existe déjà dans le système!",
                            "code" => 402
                        ], 402);
                    }
                } else {
                    return response()->json([
                        "message" => "La taille de menage est déjà atteint! veiller contacter l'administrateur système!",
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
                "code" => 402
            ], 402);
        }
    }


    public function CodeMenage($codemenage)
    {

        $datamenage = MenageModel::where('code_menage', $codemenage)->first();
        if ($datamenage) {
            return response()->json([
                "message" => "Liste des menages",
                "code" => 200,
                "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->where('code_menage', $codemenage)->first()
            ], 200);
        } else {
            return response()->json([
                "message" => "Not data",
                "code" => 402,
            ], 402);
        }
    }

    public function DetailMenage($id)
    {
        $datamenage = MenageModel::where('id', $id)->first();
        if ($datamenage) {
            return response()->json([
                "message" => "Liste des menages",
                "code" => 200,
                "data" => MenageModel::with('datapersonne.datatype_personne', 'datapersonne.datarole', 'datapersonne.dataallcriteres.datacritere', 'sitedeplace')->where('id', $id)->first()
            ], 200);
        } else {
            return response()->json([
                "message" => "Not data",
                "code" => 402,
            ], 402);
        }
    }

    public function search_data_menage(Request $request)
    {
        if ($request->keyword) {
            $data = MenageModel::where('t_menages.taille', 'like', '%' . $request->keyword . '%')
                ->orwhere('t_menages.code_menage', 'like', '%' . $request->keyword . '%')
                ->orwhere('t_menages.origine', 'like', '%' . $request->keyword . '%')
                ->orwhere('t_menages.habitation', 'like', '%' . $request->keyword . '%')
                ->leftJoin('t_site_deplace as site', 'site.id', '=', 't_menages.site_id')
                ->select(
                    'site.name as site',
                    't_menages.id',
                    't_menages.code_menage as code_menage',
                    't_menages.origine as origine',
                    't_menages.habitation as habitation',
                    't_menages.taille as taille',
                );
        }
        $alldata = $data->get();
        return response(
            [
                "message" => "Success",
                "code" => 200,
                "data" => $alldata,
            ],
            200
        );
    }

    public function search_alldata_menage(Request $request)
    {
        $dataMenage = MenageModel::with(
            'datapersonne.datatype_personne',
            'datapersonne.datarole'
        );
        if ($request->keyword) {
            $data = $dataMenage->where('t_menages.taille', 'like', '%' . $request->keyword . '%')
                ->orwhere('t_menages.code_menage', 'like', '%' . $request->keyword . '%')
                ->orwhere('t_menages.origine', 'like', '%' . $request->keyword . '%')
                ->orwhere('t_menages.habitation', 'like', '%' . $request->keyword . '%')
                ->orwhere('t.nom', 'like', '%' . $request->keyword . '%')
                ->orwhere('t.postnom', 'like', '%' . $request->keyword . '%')
                ->orwhere('t.prenom', 'like', '%' . $request->keyword . '%')
                ->orwhere('t.nom_pere', 'like', '%' . $request->keyword . '%')
                ->orwhere('t.nom_mere', 'like', '%' . $request->keyword . '%')
                ->orwhere('type.name', 'like', '%' . $request->keyword . '%')
                ->orwhere('role.name', 'like', '%' . $request->keyword . '%')
                ->leftJoin('t_personnes as t', 't.manageid', '=', 't_menages.id')
                ->leftJoin('t_type_personnes as type', 'type.id', '=', 't.typepersonneid')
                ->leftJoin('t_roles_menage as role', 'role.id', '=', 't.roleid')
                ->leftJoin('t_site_deplace as site', 'site.id', '=', 't_menages.site_id')
                ->select(
                    't_menages.id as manageid',
                    't_menages.code_menage as code_menage',
                    't_menages.origine as origine',
                    't_menages.habitation as habitation',
                    't_menages.taille as taille',
                    't.nom as nom',
                    't.postnom as postnom',
                    't.prenom as prenom',
                    't.sexe as sexe',
                    't.phone_1 as phone_1',
                    't.phone_2 as phone_2',
                    't.nom_pere as nom_pere',
                    't.nom_pere as nom_mere',
                    't.probleme_sante as problema_sante',
                    't.lieu_naissance as lieu_naissance',
                    'type.name as typepersonne',
                    'role.name as role',
                    'site.name as site',
                    't.empreinte_digital',
                    't.probleme_sante',
                    't.datenaiss',
                    't.sous_moustiquaire',
                    't.photo',
                    't.id'
                );
        }
        $alldata = $data->get();
        return response(
            [
                "message" => "Success",
                "code" => 200,
                "data" => $alldata,
            ],
            200
        );
    }

    public function dashboard()
    {
        $totafemme   = count(PersonnesModel::where('sexe', 'feminin')->get());
        $totahomme   = count(PersonnesModel::where('sexe', 'masculin')->get());
        $totalmenage = count(MenageModel::all());
        $totalbenef  = count(PersonnesModel::all());

        return response()->json([
            "message" => "Dashboard",
            "code" => 200,
            "totafemme"   =>   $totafemme,
            "totahomme"   =>   $totahomme,
            "totalmenage" =>   $totalmenage,
            "totalbenef"  =>   $totalbenef,
        ], 200);
    }
}
