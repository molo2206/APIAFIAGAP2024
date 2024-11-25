<?php

namespace App\Http\Controllers;

use App\Models\AffectationModel;
use App\Models\AffectationPermission;
use App\Models\airesante;
use App\Models\AlertModel;
use App\Models\ImageAlertModel;
use App\Models\Maladie;
use App\Models\Notifications;
use App\Models\Permission;
use App\Models\province;
use App\Models\territoir;
use App\Models\TokenUsers;
use App\Models\User;
use App\Models\zonesante;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Illuminate\Support\Facades\DB;

class AlertController extends Controller
{
    public function sendAlert(Request $request)
    {
        $request->validate([
            'name_point_focal' => 'required',
            "phone" => 'required',
            "airid" => 'required',

        ]);
        $user = Auth::user();
        if ($user->checkPermissions('Alert', 'create')) {
            $aire = airesante::find($request->airid);
            $zone = zonesante::find($aire->zoneid);
            $territoire = territoir::find($zone->territoirid);
            $province = province::find($territoire->provinceid);

            if ($aire) {
                if ($request->dece_disponible == "oui" || $request->dece_disponible == "non") {
                    if ($request->animal_malade == "oui" || $request->animal_malade == "non") {
                        if ($request->animal_mort == "oui" || $request->animal_mort == "non") {
                            if ($request->evenement == "oui" || $request->evenement == "non") {
                                $alert = AlertModel::create([
                                    'localisation' => $province->name . '/' . $territoire->name . '/' . $zone->name . '/' . $aire->name . '/' . $request->name_point_focal,
                                    'name_point_focal' => $request->name_point_focal,
                                    "phone" => $request->phone,
                                    "airid" => $request->airid,
                                    "date_notification" => $request->date_notification,
                                    "datealert" => $request->datealert,
                                    "timealert" => $request->timealert,
                                    "nbr_touche" => $request->nbr_touche,
                                    "dece_disponible" => $request->dece_disponible,
                                    "nbr_dece" => $request->nbr_dece,
                                    "animal_malade" => $request->animal_malade,
                                    "animal_mort" => $request->animal_mort,
                                    "evenement" => $request->evenement,
                                    "mesure" => $request->mesure,
                                    "maladieid" => $request->maladieid,
                                    "description" => $request->description,
                                    "nb_animal_malade" => $request->nb_animal_malade,
                                    "nb_animal_mort" => $request->nb_animal_mort,
                                    "date_detection" => $request->date_detection,
                                    "time_detection"  => $request->time_detection,
                                    "userid" => $user->id,
                                    "orguserid" => $request->orgid,
                                    "status" => 0
                                ]);

                                return response()->json([
                                    "code" => 200,
                                    "message" => 'Alert envoyé avec succès!',
                                    "data" => AlertModel::with(
                                        'datauser',
                                        'dataaire.zonesante.territoir.province',
                                        'maladie',
                                        'images'
                                    )->where('id', $alert->id)->where('deleted', 0)->where('status', 0)->first(),
                                ], 200);
                            } else {
                                return response()->json([
                                    "message" => "evemenent doit etre soit oui ou non !"
                                ], 402);
                            }
                        } else {
                            return response()->json([
                                "message" => "animal_mort doit etre soit oui ou non !"
                            ], 402);
                        }
                    } else {
                        return response()->json([
                            "message" => "animal_malade doit etre soit oui ou non !"
                        ], 402);
                    }
                } else {
                    return response()->json([
                        "message" => "dece_disponible doit etre soit oui ou non !"
                    ], 402);
                }
            } else {
                return response()->json([
                    "message" => "aireid not found "
                ], 402);
            }
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function validerAlert(Request $request, $alertid)
    {

        $request->validate([
            'name_point_focal' => 'required',
            "phone" => 'required',
            "airid" => 'required',
            "date_notification" => 'required',
            "datealert" => 'required',
            "timealert" => 'required',
            "nbr_touche" => 'required',
            "dece_disponible" => 'required',
            "nbr_dece" => 'required',
            "animal_malade" => 'required',
            "animal_mort" => 'required',
            "evenement" => 'required',
            "mesure" => 'required',
            "maladieid" => 'required',
            "nb_animal_malade" => 'required',
            "nb_animal_mort" => 'required',
            "date_detection" => 'required',
            "time_detection"  => 'required',
            "orgid" => 'required',
        ]);

        $user = Auth::user();

        if ($user->checkPermissions('Project', 'create')) {
            $datagalert = AlertModel::where('id', $alertid)->where('status', 0)->first();
            $dataalertvalide = AlertModel::where('id', $alertid)->where('status', 1)->where('deleted', 0)->first();
            if ($dataalertvalide) {
                return response()->json([
                    "message" => "Cette alerte est déjà validé",
                    "code" => 422,
                ], 422);
            } else {
                if ($datagalert) {
                    $datagalert->status = 1;
                    $datagalert->save();
                }
                $aire = airesante::find($request->airid);
                $zone = zonesante::find($aire->zoneid);
                $territoire = territoir::find($zone->territoirid);
                $province = province::find($territoire->provinceid);
                $status = 1;
                if ($aire) {
                    if ($request->dece_disponible == "oui" || $request->dece_disponible == "non") {
                        if ($request->animal_malade == "oui" || $request->animal_malade == "non") {
                            if ($request->animal_mort == "oui" || $request->animal_mort == "non") {
                                if ($request->evenement == "oui" || $request->evenement == "non") {
                                    $alert = AlertModel::create([
                                        'name_point_focal' => $request->name_point_focal,
                                        'localisation' => $province->name . '/' . $territoire->name . '/' . $zone->name . '/' . $aire->name . '/' . $request->name_point_focal,
                                        "phone" => $request->phone,
                                        "airid" => $request->airid,
                                        "date_notification" => $request->date_notification,
                                        "datealert" => $request->datealert,
                                        "timealert" => $request->timealert,
                                        "nbr_touche" => $request->nbr_touche,
                                        "dece_disponible" => $request->dece_disponible,
                                        "nbr_dece" => $request->nbr_dece,
                                        "animal_malade" => $request->animal_malade,
                                        "animal_mort" => $request->animal_mort,
                                        "evenement" => $request->evenement,
                                        "mesure" => $request->mesure,
                                        "maladieid" => $request->maladieid,
                                        "description" => $request->description,
                                        "nb_animal_malade" => $request->nb_animal_malade,
                                        "nb_animal_mort" => $request->nb_animal_mort,
                                        "date_detection" => $request->date_detection,
                                        "time_detection"  => $request->time_detection,
                                        "userid_valider" => $user->id,
                                        "children" => $datagalert->id,
                                        "orguserid" => $request->orgid,
                                        "status" => $status
                                    ]);
                                    //INSERTION IMAGES ALERT
                                    if ($datagalert) {
                                        foreach ($datagalert->images()->get() as $item) {
                                            $alert->imagesalert()->attach([$alert->id =>
                                            [
                                                'image' => $item->image,
                                            ]]);
                                        }
                                    }

                                    // $image = env('IMAGE_ALERT');
                                    // $dataToken_for_user = TokenUsers::all();
                                    // foreach ($dataToken_for_user as $item) {
                                    //     PushNotification::sendPushNotification($item->token, "Une alerte a été mise en ligne par Cosamed", $alert->title, $image);
                                    // }

                                    // foreach (User::get() as $key => $value) {
                                    //     Notifications::create([
                                    //         "user_id" => $value->id,
                                    //         "title" => "Une alerte a été mise en ligne par Cosamed",
                                    //         "description" => "Une alerte a été lancée" . "au niveau de " . $alert->localisation,
                                    //         "id_type" => $alert->id,
                                    //         "type" => "gap"
                                    //     ]);
                                    // }

                                    return response()->json([
                                        "message" => 'Alert investiguée avec succès!',
                                        "data" => AlertModel::with(
                                            'datauser',
                                            'dataaire.zonesante.territoir.province',
                                            'maladie',
                                            'images'
                                        )->orderBy('updated_at', 'desc')->where('deleted', 0)->where('status', 1)->find($request->id),
                                    ], 200);
                                } else {
                                    return response()->json([
                                        "message" => "evemenent doit etre soit oui ou non !"
                                    ], 402);
                                }
                            } else {
                                return response()->json([
                                    "message" => "animal_mort doit etre soit oui ou non !"
                                ], 402);
                            }
                        } else {
                            return response()->json([
                                "message" => "animal_malade doit etre soit oui ou non !"
                            ], 402);
                        }
                    } else {
                        return response()->json([
                            "message" => "dece_disponible doit etre soit oui ou non !"
                        ], 402);
                    }
                } else {
                    return response()->json([
                        "message" => "aireid not found "
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


    public function updateAlert(Request $request, $id)
    {
        $request->validate([
            'name_point_focal' => 'required',
            "phone" => 'required',
            "airid" => 'required',
            "date_notification" => 'required',
            "datealert" => 'required',
            "timealert" => 'required',
            "nbr_touche" => 'required',
            "dece_disponible" => 'required',
            "nbr_dece" => 'required',
            "animal_malade" => 'required',
            "animal_mort" => 'required',
            "evenement" => 'required',
            "mesure" => 'required',
            "maladieid" => 'required',
            "nb_animal_malade" => 'required',
            "nb_animal_mort" => 'required',
            "date_detection" => 'required',
            "time_detection"  => 'required',
            "orgid" => 'required',
        ]);

        $user = Auth::user();
        if ($user->checkPermissions('Project', 'read')) {
            $datagalert = AlertModel::where('id', $id)->where('status', '0')->first();
            if ($datagalert) {
                $aire = airesante::find($request->airid);
                if ($aire) {
                    if ($request->dece_disponible == "oui" || $request->dece_disponible == "non") {
                        if ($request->animal_malade == "oui" || $request->animal_malade == "non") {
                            if ($request->animal_mort == "oui" || $request->animal_mort == "non") {
                                if ($request->evenement == "oui" || $request->evenement == "non") {
                                    $datagalert->name_point_focal = $request->name_point_focal;
                                    $datagalert->phone = $request->phone;
                                    $datagalert->airid = $request->airid;
                                    $datagalert->date_notification = $request->date_notification;
                                    $datagalert->datealert = $request->datealert;
                                    $datagalert->timealert = $request->timealert;
                                    $datagalert->nbr_touche = $request->nbr_touche;
                                    $datagalert->dece_disponible = $request->dece_disponible;
                                    $datagalert->nbr_dece = $request->nbr_dece;
                                    $datagalert->animal_malade = $request->animal_malade;
                                    $datagalert->animal_mort = $request->animal_mort;
                                    $datagalert->evenement = $request->evenement;
                                    $datagalert->mesure = $request->mesure;
                                    $datagalert->maladieid = $request->maladieid;
                                    $datagalert->description = $request->description;
                                    $datagalert->nb_animal_malade = $request->nb_animal_malade;
                                    $datagalert->nb_animal_mort = $request->nb_animal_mort;
                                    $datagalert->date_detection = $request->date_detection;
                                    $datagalert->time_detection = $request->time_detection;
                                    $datagalert->userid = $user->id;
                                    $datagalert->orguserid = $request->orgid;
                                    $datagalert->save();


                                    return response()->json([
                                        "code" => 200,
                                        "message" => 'Alert modifié avec succès!',
                                        "data" => AlertModel::with(
                                            'datauser',
                                            'dataaire.zonesante.territoir.province',
                                            'maladie',
                                            'images'
                                        )->orderBy('updated_at', 'desc')->where('deleted', 0)->where('status', 0)->get(),
                                    ], 200);
                                } else {
                                    return response()->json([
                                        "message" => "evemenent doit etre soit oui ou non !"
                                    ], 402);
                                }
                            } else {
                                return response()->json([
                                    "message" => "animal_mort doit etre soit oui ou non !"
                                ], 402);
                            }
                        } else {
                            return response()->json([
                                "message" => "animal_malade doit etre soit oui ou non !"
                            ], 402);
                        }
                    } else {
                        return response()->json([
                            "message" => "dece_disponible doit etre soit oui ou non !"
                        ], 402);
                    }
                } else {
                    return response()->json([
                        "message" => "aireid not found "
                    ], 402);
                }
            } else {
                return response()->json([
                    "message" => "Erreur de traitement avec cette id:" . $id
                ], 402);
            }
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function suppressionalert(Request $request, $id)
    {
        $request->validate([
            "orgid" => "required"
        ]);
        $user = Auth::user();
        if ($user->checkPermissions('Project', 'delete')) {
            $datagalert = AlertModel::where('id', $id)->where('status', '0')->first();
            if ($datagalert) {
                $datagalert->deleted = 1;
                $datagalert->save();
                return response()->json([
                    "code" => 200,
                    "message" => 'Alerte est rejeté avec succès!',
                    "data" => AlertModel::with(
                        'datauser',
                        'dataaire.zonesante.territoir.province',
                        'maladie',
                        'images'
                    )->where('deleted', 0)->where('status', 0)->get(),
                ], 200);
            } else {
                return response()->json([
                    "code" => 402,
                    "message" => 'Cette alerte n\'existe pas dans le système',
                ], 200);
            }
        } else {
            return response()->json([
                "code" => 402,
                "message" => "Vous ne pouvez pas éffectuer cette action"
            ], 402);
        }
    }

    public function rejetealert(Request $request, $id)
    {
        $request->validate([
            "orgid" => "required"
        ]);

        $user = Auth::user();
        if ($user->checkPermissions('Project', 'delete')) {
            $datagalert = AlertModel::where('id', $id)->where('status', '0')->first();
            if ($datagalert) {
                $datagalert->status = 2;
                $datagalert->save();
                return response()->json([
                    "code" => 200,
                    "message" => 'Alerte est rejeté avec succès!',
                    "data" => AlertModel::with(
                        'dataaire.zonesante.territoir.province',
                        'maladie',
                        'images'
                    )->where('status', 2)->get(),
                ], 200);
            } else {
                return response()->json([
                    "code" => 402,
                    "message" => 'Cette alerte n\'existe pas dans le système',
                ], 200);
            }
        } else {
            return response()->json([
                "code" => 402,
                "message" => "Vous ne pouvez pas éffectuer cette action"
            ], 402);
        }
    }

    public function getAlert($orgid)
    {
        $user = Auth::user();
        if ($user->checkPermissions('Project', 'read')) {;
            return response()->json([
                "message" => "Liste des alerts",
                "data" => AlertModel::with(
                    'datauser',
                    'dataaire.zonesante.territoir.province',
                    'maladie',
                    'images'
                )->where('deleted', 0)->where('status', 0)->where('children', null)->get(),
            ], 200);
        } else {
            return response()->json([
                "message" => "Vous ne pouvez pas éffectuer cette action",
                "code" => 402
            ], 402);
        }
    }
    public function getAlertvalide($orgid)
    {
        $user = Auth::user();
        if ($user->checkPermissions('Project', 'read')) {;
            return response()->json([
                "message" => "Liste des alerts",
                "data" => AlertModel::with(
                    'datauser',
                    'dataaire.zonesante.territoir.province',
                    'maladie',
                    'images'
                )->orderby('created_at', 'desc')->where('deleted', 0)->where('status', 1)->whereNot('children', null)->get(),
            ], 200);
        } else {
            return response()->json([
                "message" => "Vous ne pouvez pas éffectuer cette action",
                "code" => 402
            ], 402);
        }
    }
    public function getAlertvalideByuser($orgid)
    {
        $user = Auth::user();
        if ($user->checkPermissions('Project', 'read')) {;
            return response()->json([
                "message" => "Liste des alertes validées de " . $user->full_name . "(" . $user->email . ")",
                "data" => AlertModel::with(
                    'datauser',
                    'dataaire.zonesante.territoir.province',
                    'maladie',
                    'images'
                )->orderby('created_at', 'desc')->where('orguserid', $orgid)->where('deleted', 0)->where('status', 1)->where('userid_valider', $user->id)->whereNot('children', null)->get(),
            ], 200);
        } else {
            return response()->json([
                "message" => "Vous ne pouvez pas éffectuer cette action",
                "code" => 402
            ], 402);
        }
    }

    public function getDetailAlert($id)
    {
        $alert = AlertModel::find($id);
        if ($alert) {
            return response()->json([
                "message" => "Detail de l'alert",
                "data" => AlertModel::with(
                    'datauser',
                    'dataaire.zonesante.territoir.province',
                    'maladie',
                    'images'
                )->where('id', $alert->id)->first(),
            ], 200);
        } else {
            return response()->json([
                "message" => "Identifiant not found",
                "code" => "402"
            ], 402);
        }
    }
    public function alertuser($orgid)
    {
        $user = Auth::user();
        if ($user->checkPermissions('Project', 'read')) {;
            if ($user) {
                return response()->json([
                    "message" => "Liste des alerts de " . $user->full_name . "(" . $user->email . ")",
                    "data" => AlertModel::with(
                        'datauser',
                        'dataaire.zonesante.territoir.province',
                        'maladie',
                        'images'
                    )->orderby('created_at', 'desc')->where('orguserid', $orgid)->where('deleted', 0)->where('status', 0)->where('userid', $user->id)->get(),
                ], 200);
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
    }
    public function getAlertInvalideByuser($orgid)
    {
        $user = Auth::user();
        if ($user->checkPermissions('Project', 'read')) {;
            return response()->json([
                "message" => "Liste des alertes invalides de " . $user->full_name . "(" . $user->email . ")",
                "data" => AlertModel::with(
                    'datauser',
                    'dataaire.zonesante.territoir.province',
                    'maladie',
                    'images'
                )->where('children', null)->where('status', 2)
                    ->where('userid', $user->id)->get(),
            ], 200);
        } else {
            return response()->json([
                "message" => "Vous ne pouvez pas éffectuer cette action",
                "code" => 402
            ], 402);
        }
    }

    public function getAlertInvalide($orgid)
    {
        $user = Auth::user();
        if ($user->checkPermissions('Project', 'read')) {;
            return response()->json([
                "message" => "Liste des alertes invalides",
                "data" => AlertModel::with(
                    'datauser',
                    'dataaire.zonesante.territoir.province',
                    'maladie',
                    'images'
                )->where('children', null)->where('status', 2)->get(),
            ], 200);
        } else {
            return response()->json([
                "message" => "Vous ne pouvez pas éffectuer cette action",
                "code" => 402
            ], 402);
        }
    }
    public function getlastalertvalide()
    {

        $dt = new DateTime();
        $startDate = $dt->format('Y-m-d');

        return response()->json([
            "message" => "Liste des alerts",
            "data" => AlertModel::with(
                'datauser',
                'dataaire.zonesante.territoir.province',
                'maladie',
                'images'
            )
                ->orderby('datealert', 'desc')->whereNot('children', null)->get(),
        ], 200);
    }
    public function deleteImageAlert($id)
    {
        $images = ImageAlertModel::where('id', $id)->where('deleted', 0)->first();
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
    public function Imagealert(Request $request, $id)
    {
        $dataalert = AlertModel::where('id', $id)->first();
        if ($dataalert) {
            $image = UtilController::uploadMultipleImage($request->images, '/uploads/alert/');
            if (count($request->images) > 0) {
                foreach ($image as $item) {
                    $dataalert->imagesalert()->attach([$dataalert->id =>
                    [
                        'image' => $item,
                    ]]);
                }
            }
            return response()->json([
                "message" => 'Traitement réussi avec succès!',
                "code" => 200,
                "data" => AlertModel::with(
                    'datauser',
                    'dataaire.zonesante.territoir.province',
                    'maladie',
                    'images'
                )->where('id', $id)->first(),
            ], 200);
        } else {
            return response()->json([
                "message" => "C'est identifiant d'alert n'existe pas!",
                "code" => 402,
            ], 402);
        }
    }
    public function List_Maladie()
    {
        return response()->json([
            "message" => 'Traitement réussi avec succès!',
            "code" => 200,
            "data" => Maladie::whereHas('alert')->get()
        ], 200);
    }

    public function Nbr_Alert(Request $request)
    {
        $province = $request->get('province') ? $request->get('province') : "all";
        $territoir = $request->get('territoir') ? $request->get('territoir') : "all";
        $zone = $request->get('zone') ? $request->get('zone') : "all";
        $aire = $request->get('aire') ? $request->get('aire') : "all";
        $maladie = $request->get('maladie') ? $request->get('maladie') :  "all";
        $type = $request->get('type');

        if ($maladie == "all") {
            if ($province == "all") {
                $allalert = AlertModel::where('status', 1)->where('deleted', 0)->whereNot('children', null)->getQuery();
            } else {
                $allalert = AlertModel::with('dataaire.zonesante.territoir.province')->where('status', 1)->where('deleted', 0)
                    ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)->whereNot('children', null)->getQuery();
                if ($territoir == "all") {
                    $allalert = $allalert;
                } else {
                    $allalert = AlertModel::with('dataaire')->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)
                        ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)->whereNot('children', null)->getQuery();
                    if ($zone == "all") {
                        $allalert =  $allalert;
                    } else {
                        $allalert = AlertModel::with('dataaire')->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)
                            ->whereRelation('dataaire.zonesante', 'id', $zone)
                            ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                            ->whereNot('children', null)->getQuery();

                        if ($aire == "all") {
                            $allalert =  $allalert;
                        } else {
                            $allalert =  AlertModel::with('dataaire')->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)
                                ->whereRelation('dataaire.zonesante', 'id', $zone)
                                ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                                ->whereRelation('dataaire', 'id', $aire)->whereNot('children', null)
                                ->getQuery();
                        }
                    }
                }
            }
        } else {
            if ($province == "all") {
                $allalert = AlertModel::where('status', 1)->where('deleted', 0)
                    ->where('maladieid', $maladie)->whereNot('children', null)->getQuery();
            } else {
                $allalert = AlertModel::with('dataaire.zonesante.territoir.province')->where('status', 1)->where('deleted', 0)
                    ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                    ->where('maladieid', $maladie)->whereNot('children', null)->getQuery();
                if ($territoir == "all") {
                    $allalert = $allalert;
                } else {
                    $allalert = AlertModel::with('dataaire')->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)
                        ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                        ->where('maladieid', $maladie)->whereNot('children', null)->getQuery();
                    if ($zone == "all") {
                        $allalert =  $allalert;
                    } else {
                        $allalert = AlertModel::with('dataaire')->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)
                            ->whereRelation('dataaire.zonesante', 'id', $zone)
                            ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                            ->where('maladieid', $maladie)->whereNot('children', null)->getQuery();

                        if ($aire == "all") {
                            $allalert =  $allalert;
                        } else {
                            $allalert =  AlertModel::with('dataaire')->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)
                                ->whereRelation('dataaire.zonesante', 'id', $zone)
                                ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                                ->whereRelation('dataaire', 'id', $aire)->where('maladieid', $maladie)
                                ->whereNot('children', null)->getQuery();
                        }
                    }
                }
            }
        }

        if ($maladie == "all") {
            if ($province == "all") {
                $dataalert = AlertModel::where('status', 1)->where('deleted', 0)->whereNot('children', null)->getQuery();
            } else {
                $dataalert = AlertModel::with('dataaire.zonesante.territoir.province')->where('status', 1)->where('deleted', 0)
                    ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                    ->whereNot('children', null)->getQuery();
                if ($territoir == "all") {
                    $dataalert = $dataalert;
                } else {
                    $dataalert = AlertModel::with('dataaire')->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)
                        ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                        ->whereNot('children', null)->getQuery();
                    if ($zone == "all") {
                        $dataalert =  $dataalert;
                    } else {
                        $dataalert = AlertModel::with('dataaire')->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)
                            ->whereRelation('dataaire.zonesante', 'id', $zone)
                            ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                            ->whereNot('children', null)->getQuery();
                    }
                }
            }
        } else {
            if ($province == "all") {
                $dataalert = AlertModel::where('status', 1)->where('deleted', 0)
                    ->where('maladieid', $maladie)->whereNot('children', null)->getQuery();
            } else {
                $dataalert = AlertModel::with('dataaire.zonesante.territoir.province')->where('status', 1)
                    ->whereNot('children', null)->where('deleted', 0)
                    ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                    ->whereNot('children', null)->where('maladieid', $maladie)->getQuery();
                if ($territoir == "all") {
                    $dataalert = $dataalert;
                } else {
                    $dataalert = AlertModel::with('dataaire')->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)
                        ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                        ->whereNot('children', null)->where('maladieid', $maladie)->getQuery();
                    if ($zone == "all") {
                        $dataalert =  $dataalert;
                    } else {
                        $dataalert = AlertModel::with('dataaire')->whereRelation('dataaire.zonesante.territoir', 'id', $territoir)
                            ->whereRelation('dataaire.zonesante', 'id', $zone)
                            ->whereRelation('dataaire.zonesante.territoir.province', 'id', $province)
                            ->whereNot('children', null)->where('maladieid', $maladie)->getQuery();
                    }
                }
            }
        }

        $startOfLastWeek = date("Y-m-d");
        $endOfLastWeek = date_create($startOfLastWeek)->modify('-7 days')->format('Y-m-d');

        if ($type == "cases") {
            $data = [
                "total" => $allalert->get()->sum("nbr_touche"),
                "dataaire" =>
                $dataalert->groupBy('airid')->selectRaw('sum(nbr_touche)as total,airid')->get(),
                "total_seven_day" => $allalert->get()->whereBetween('datealert', [
                    $endOfLastWeek,
                    $startOfLastWeek
                ])->sum('nbr_touche'),
                "total_by_month" => $allalert->orderBy('datealert')->selectRaw(
                    "sum(nbr_touche) as total,
                    DATE_FORMAT(datealert,'%M') as month"
                )->whereYear('datealert', date('Y'))
                    ->groupBy('month')->get(),
                "weeks" => []
            ];
        }

        if ($type == "deaths") {
            $data = [
                "total" => $allalert->get()->sum("nbr_dece"),
                "dataaire" =>
                $dataalert->groupBy('airid')->selectRaw('sum(nbr_dece)as total,airid')->get(),
                "total_seven_day" => $allalert->get()->whereBetween('datealert', [
                    $endOfLastWeek,
                    $startOfLastWeek
                ])->sum('nbr_dece'),
                "total_by_month" => $allalert->orderBy('datealert')->selectRaw(
                    "sum(nbr_dece) as total,
                    DATE_FORMAT(datealert,'%M') as month"
                )->whereYear('datealert', date('Y'))
                    ->groupBy('month')->get()
            ];
        }

        if ($type == "sick-animals") {
            $data = [
                "total" => $allalert->get()->sum("nb_animal_malade"),
                "dataaire" =>
                $dataalert->groupBy('airid')->selectRaw('sum(nb_animal_malade)as total,airid')->get(),
                "total_seven_day" => $allalert->get()->whereBetween('datealert', [
                    $endOfLastWeek,
                    $startOfLastWeek
                ])->sum('nb_animal_malade'),
                "total_by_month" => $allalert->orderBy('datealert')->selectRaw(
                    "sum(nbr_dece) as total,
                    DATE_FORMAT(datealert,'%M') as month"
                )->whereYear('datealert', date('Y'))
                    ->groupBy('month')->get()
            ];
        }

        if ($type == "dead-animals") {
            $data = [
                "total" => $allalert->get()->sum("nb_animal_mort"),
                "dataaire" =>
                $dataalert->groupBy('airid')->selectRaw('sum(nb_animal_mort)as total,airid')->get(),
                "total_seven_day" => $allalert->get()->whereBetween('datealert', [
                    $endOfLastWeek,
                    $startOfLastWeek
                ])->sum('nb_animal_mort'),
                "total_by_month" => $allalert->orderBy('datealert')->selectRaw(
                    "sum(nbr_dece) as total,
                    DATE_FORMAT(datealert,'%M') as month"
                )->whereYear('datealert', date('Y'))
                    ->groupBy('month')->get()

            ];
        }


        return response()->json([
            "message" => "Liste des alerts",
            "code" => "200",
            "data" => $data,
        ], 200);
    }
    public function AllProvince_Nbr_Alert(Request $request)
    {
        $province = $request->get('province') ? $request->get('province') : "all";
        $territoir = $request->get('territoir') ? $request->get('territoir') : "all";
        $zone = $request->get('zone') ? $request->get('zone') : "all";
        $aire = $request->get('aire') ? $request->get('aire') : "all";
        $maladie = $request->get('maladie') ? $request->get('maladie') :  "all";
        $type = $request->get('type');


        if ($maladie == "all") {
            $allalert = AlertModel::where('t_alert.status', 1)->where('t_alert.deleted', 0)->whereNot('t_alert.children', null)
                ->join('t_aire_sante', 't_alert.airid', '=', 't_aire_sante.id')
                ->join('t_zone', 't_aire_sante.zoneid', '=', 't_zone.id')
                ->join('t_territoire', 't_zone.territoirid', '=', 't_territoire.id')
                ->join('t_province', 't_territoire.provinceid', '=', 't_province.id')
                ->select(
                    't_aire_sante.id',
                    't_alert.nbr_touche',
                    't_province.id as province',
                    't_territoire.id as territoire',
                    't_zone.id as zone',
                    't_aire_sante.id as aire',
                    't_alert.nb_animal_mort',
                    't_alert.nb_animal_malade',
                    't_alert.nbr_dece',
                    't_alert.datealert',
                    't_alert.maladieid',
                    't_alert.id as alertid'
                )->get();
            if ($province == "all") {
                if ($type == "cases") {
                    $allprovince = [];
                    foreach (province::all() as $key => $value) {
                        array_push($allprovince, [
                            "nom" => $value->name,
                            "total" => $allalert->where('province', $value->id)->sum('nbr_touche')
                        ]);
                    }
                    $data = [
                        "allprovince" => $allprovince
                    ];
                }

                if ($type == "deaths") {
                    $allprovince = [];
                    foreach (province::all() as $key => $value) {
                        array_push($allprovince, [
                            "nom" => $value->name,
                            "total" => $allalert->where('province', $value->id)->sum('nbr_dece')
                        ]);
                    }
                    $data = [
                        "allprovince" => $allprovince
                    ];
                }

                if ($type == "sick-animals") {
                    $allprovince = [];
                    foreach (province::all() as $key => $value) {
                        array_push($allprovince, [
                            "nom" => $value->name,
                            "total" => $allalert->where('province', $value->id)->sum('nb_animal_malade')
                        ]);
                    }
                    $data = [
                        "allprovince" => $allprovince
                    ];
                }
                if ($type == "dead-animals") {
                    $allprovince = [];
                    foreach (province::all() as $key => $value) {
                        array_push($allprovince, [
                            "nom" => $value->name,
                            "total" => $allalert->where('province', $value->id)->sum('nb_animal_mort')
                        ]);
                    }
                    $data = [
                        "allprovince" => $allprovince
                    ];
                }
            } else {

                if ($type == "cases") {
                    $allprovince = [];
                    foreach (zonesante::whereRelation('territoir.province', 'id', $province)->get() as $key => $value) {
                        array_push($allprovince, [
                            "nom" => $value->name,
                            "total" => $allalert->where('zone', $value->id)->sum('nbr_touche')
                        ]);
                    }
                    $data = [
                        "allprovince" => $allprovince
                    ];
                }

                if ($type == "deaths") {
                    $allprovince = [];
                    foreach (zonesante::whereRelation('territoir.province', 'id', $province)->get() as $key => $value) {
                        array_push($allprovince, [
                            "nom" => $value->name,
                            "total" => $allalert->where('zone', $value->id)->sum('nbr_dece')
                        ]);
                    }
                    $data = [
                        "allprovince" => $allprovince
                    ];
                }

                if ($type == "sick-animals") {
                    $allprovince = [];
                    foreach (zonesante::whereRelation('territoir.province', 'id', $province)->get() as $key => $value) {
                        array_push($allprovince, [
                            "nom" => $value->name,
                            "total" => $allalert->where('zone', $value->id)->sum('nb_animal_malade')
                        ]);
                    }
                    $data = [
                        "allprovince" => $allprovince
                    ];
                }


                if ($type == "dead-animals") {
                    $allprovince = [];
                    foreach (zonesante::whereRelation('territoir.province', 'id', $province)->get() as $key => $value) {
                        array_push($allprovince, [
                            "nom" => $value->name,
                            "total" => $allalert->where('zone', $value->id)->sum('nb_animal_mort')
                        ]);
                    }
                    $data = [
                        "allprovince" => $allprovince
                    ];
                }
                if ($territoir == "all") {
                    $data = $data;
                } else {
                    $data = $data;
                    if ($zone == "all") {
                        $data = $data;
                    } else {
                        if ($type == "cases") {
                            $allprovince = [];
                            foreach (airesante::whereRelation('zonesante', 'id', $zone)->get() as $key => $value) {
                                array_push($allprovince, [
                                    "nom" => $value->name,
                                    "total" => $allalert->where('id', $value->id)->sum('nbr_touche')
                                ]);
                            }
                            $data = [
                                "allprovince" => $allprovince
                            ];
                        }

                        if ($type == "deaths") {
                            $allprovince = [];
                            foreach (airesante::whereRelation('zonesante', 'id', $zone)->get() as $key => $value) {
                                array_push($allprovince, [
                                    "nom" => $value->name,
                                    "total" => $allalert->where('id', $value->id)->sum('nbr_dece')
                                ]);
                            }
                            $data = [
                                "allprovince" => $allprovince
                            ];
                        }

                        if ($type == "sick-animals") {
                            $allprovince = [];
                            foreach (airesante::whereRelation('zonesante', 'id', $zone)->get() as $key => $value) {
                                array_push($allprovince, [
                                    "nom" => $value->name,
                                    "total" => $allalert->where('id', $value->id)->sum('nb_animal_malade')
                                ]);
                            }
                            $data = [
                                "allprovince" => $allprovince
                            ];
                        }
                        if ($type == "dead-animals") {
                            $allprovince = [];
                            foreach (airesante::whereRelation('zonesante', 'id', $zone)->get() as $key => $value) {
                                array_push($allprovince, [
                                    "nom" => $value->name,
                                    "total" => $allalert->where('id', $value->id)->sum('nb_animal_mort')
                                ]);
                            }
                            $data = [
                                "allprovince" => $allprovince
                            ];
                        }
                    }
                }
            }
        } else {
            $allalert = AlertModel::where('t_alert.status', 1)->where('t_alert.maladieid', $maladie)->where('t_alert.deleted', 0)->whereNot('t_alert.children', null)
                ->join('t_aire_sante', 't_alert.airid', '=', 't_aire_sante.id')
                ->join('t_zone', 't_aire_sante.zoneid', '=', 't_zone.id')
                ->join('t_territoire', 't_zone.territoirid', '=', 't_territoire.id')
                ->join('t_province', 't_territoire.provinceid', '=', 't_province.id')
                ->select(
                    't_aire_sante.id',
                    't_alert.nbr_touche',
                    't_province.id as province',
                    't_territoire.id as territoire',
                    't_zone.id as zone',
                    't_aire_sante.id as aire',
                    't_alert.nb_animal_mort',
                    't_alert.nb_animal_malade',
                    't_alert.nbr_dece',
                    't_alert.datealert',
                    't_alert.maladieid',
                    't_alert.id as alertid'
                )->get();
            if ($province == "all") {
                if ($type == "cases") {
                    $allprovince = [];
                    foreach (province::all() as $key => $value) {
                        array_push($allprovince, [
                            "nom" => $value->name,
                            "total" => $allalert->where('province', $value->id)->sum('nbr_touche')
                        ]);
                    }
                    $data = [
                        "allprovince" => $allprovince
                    ];
                }

                if ($type == "deaths") {
                    $allprovince = [];
                    foreach (province::all() as $key => $value) {
                        array_push($allprovince, [
                            "nom" => $value->name,
                            "total" => $allalert->where('province', $value->id)->sum('nbr_dece')
                        ]);
                    }
                    $data = [
                        "allprovince" => $allprovince
                    ];
                }

                if ($type == "sick-animals") {
                    $allprovince = [];
                    foreach (province::all() as $key => $value) {
                        array_push($allprovince, [
                            "nom" => $value->name,
                            "total" => $allalert->where('province', $value->id)->sum('nb_animal_malade')
                        ]);
                    }
                    $data = [
                        "allprovince" => $allprovince
                    ];
                }
                if ($type == "dead-animals") {
                    $allprovince = [];
                    foreach (province::all() as $key => $value) {
                        array_push($allprovince, [
                            "nom" => $value->name,
                            "total" => $allalert->where('province', $value->id)->sum('nb_animal_mort')
                        ]);
                    }
                    $data = [
                        "allprovince" => $allprovince
                    ];
                }
            } else {

                if ($type == "cases") {
                    $allprovince = [];
                    foreach (zonesante::whereRelation('territoir.province', 'id', $province)->get() as $key => $value) {
                        array_push($allprovince, [
                            "nom" => $value->name,
                            "total" => $allalert->where('zone', $value->id)->sum('nbr_touche')
                        ]);
                    }
                    $data = [
                        "allprovince" => $allprovince
                    ];
                }

                if ($type == "deaths") {
                    $allprovince = [];
                    foreach (zonesante::whereRelation('territoir.province', 'id', $province)->get() as $key => $value) {
                        array_push($allprovince, [
                            "nom" => $value->name,
                            "total" => $allalert->where('zone', $value->id)->sum('nbr_dece')
                        ]);
                    }
                    $data = [
                        "allprovince" => $allprovince
                    ];
                }

                if ($type == "sick-animals") {
                    $allprovince = [];
                    foreach (zonesante::whereRelation('territoir.province', 'id', $province)->get() as $key => $value) {
                        array_push($allprovince, [
                            "nom" => $value->name,
                            "total" => $allalert->where('zone', $value->id)->sum('nb_animal_malade')
                        ]);
                    }
                    $data = [
                        "allprovince" => $allprovince
                    ];
                }


                if ($type == "dead-animals") {
                    $allprovince = [];
                    foreach (zonesante::whereRelation('territoir.province', 'id', $province)->get() as $key => $value) {
                        array_push($allprovince, [
                            "nom" => $value->name,
                            "total" => $allalert->where('zone', $value->id)->sum('nb_animal_mort')
                        ]);
                    }
                    $data = [
                        "allprovince" => $allprovince
                    ];
                }
                if ($territoir == "all") {
                    $data = $data;
                } else {
                    $data = $data;
                    if ($zone == "all") {
                        $data = $data;
                    } else {
                        if ($type == "cases") {
                            $allprovince = [];
                            foreach (airesante::whereRelation('zonesante', 'id', $zone)->get() as $key => $value) {
                                array_push($allprovince, [
                                    "nom" => $value->name,
                                    "total" => $allalert->where('id', $value->id)->sum('nbr_touche')
                                ]);
                            }
                            $data = [
                                "allprovince" => $allprovince
                            ];
                        }

                        if ($type == "deaths") {
                            $allprovince = [];
                            foreach (airesante::whereRelation('zonesante', 'id', $zone)->get() as $key => $value) {
                                array_push($allprovince, [
                                    "nom" => $value->name,
                                    "total" => $allalert->where('id', $value->id)->sum('nbr_dece')
                                ]);
                            }
                            $data = [
                                "allprovince" => $allprovince
                            ];
                        }

                        if ($type == "sick-animals") {
                            $allprovince = [];
                            foreach (airesante::whereRelation('zonesante', 'id', $zone)->get() as $key => $value) {
                                array_push($allprovince, [
                                    "nom" => $value->name,
                                    "total" => $allalert->where('id', $value->id)->sum('nb_animal_malade')
                                ]);
                            }
                            $data = [
                                "allprovince" => $allprovince
                            ];
                        }
                        if ($type == "dead-animals") {
                            $allprovince = [];
                            foreach (airesante::whereRelation('zonesante', 'id', $zone)->get() as $key => $value) {
                                array_push($allprovince, [
                                    "nom" => $value->name,
                                    "total" => $allalert->where('id', $value->id)->sum('nb_animal_mort')
                                ]);
                            }
                            $data = [
                                "allprovince" => $allprovince
                            ];
                        }
                    }
                }
            }
        }





        return response()->json([
            "message" => "Liste des alerts",
            "code" => "200",
            "data" => $data,
        ], 200);
    }
    public function ListAire()
    {
        $allaire = airesante::get();
        return response()->json([
            "message" => "Liste aires santes!",
            "data" => $allaire,
            "code" => 200,
        ], 200);
    }
}
