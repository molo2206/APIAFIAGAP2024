<?php

namespace App\Http\Controllers;

use App\Models\AffectationModel;
use App\Models\AffectationPermission;
use App\Models\EnteteScoreModel;
use App\Models\EvaluationModels;
use App\Models\QuestionModel;
use App\Models\ReponseModel;
use App\Models\GapsModel;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScoreCardController extends Controller
{

    public function AddEntete(Request $request)
    {
        $request->validate([
            "name_entete" => "required",
        ]);
        $entetescore = EnteteScoreModel::where('name_entete', $request->name_entete)->exists();
        if ($entetescore) {
            return response()->json([
                "message" => "Cette nomaclature :(" . $request->name_entete . ") dans le système!",
            ], 422);
        } else {
            EnteteScoreModel::create([
                "name_entete" => $request->name_entete
            ]);
            return response()->json([
                "message" => "Traitement réussie!"
            ], 200);
        }
    }

    public function list_entete()
    {
        return response()->json([
            "message" => "Liste des entetes ScoreCard",
            "code" => 200,
            "data" => EnteteScoreModel::with('dataquestion')->get(),
        ], 200);
    }

    public function addquestion(Request $request)
    {
        $request->validate([
            "name_question" => "required",
            'enteteid' => "required",
        ]);
        $question = QuestionModel::where('name_question', $request->name_question)->exists();
        if ($question) {
            return response()->json([
                "message" => "Cette question " . "(" . $request->name_question . ")" . " existe dans le système!",
            ], 422);
        } else {
            $entetescore = EnteteScoreModel::find($request->enteteid);
            if ($entetescore) {
                QuestionModel::create([
                    "name_question" => $request->name_question,
                    "enteteid" => $request->enteteid
                ]);
            } else {
                return response()->json([
                    "message" => "Traitement réussie!",
                    "code" => "200"
                ], 200);
            }
            return response()->json([
                "message" => "Traitement réussie!",
                "code" => "200"
            ], 200);
        }
    }
    public function ListQuestionRubrique($id)
    {
        $entete = EnteteScoreModel::find($id);
        if ($entete) {
            return response()->json([
                "message" => "Liste des questionnaires",
                "data" => EnteteScoreModel::with('dataquestion')->where('id', $id)->first(),
                "code" => 200,
            ], 200);
        } else {
            return response()->json([
                "message" => "Identifiant not found",
                "code" => "422"
            ], 422);
        }
    }

    public function sendscoreCard(Request $request)
    {
        $request->validate([
            'gapid' => 'required',
            "datareponse" => "required|array",
        ]);
        $gapid = GapsModel::find($request->gapid);
        $datascore = ReponseModel::where('gapid', $request->gapid)->first();
        if ($datascore) {
            //MODIFICATION SCORECARD
            if ($gapid) {
                $gapid->scorecardgap()->detach();
                foreach ($request->datareponse as $item) {
                    $reponse_question = ReponseModel::where('questionid', $item['questionid'])->first();
                    $gapid->scorecardgap()->attach([$request->gapid =>
                    [
                        $reponse_question->response = $item['reponse']
                    ]]);
                }
            }
            return response()->json([
                "message" => "Traitement réussie avec succès!",
                "code" => "200",
                "data" => GapsModel::with(
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
                    'datascorecard'
                )->where('id', $gapid->id)->first(),
            ], 200);
        } else {
            if ($gapid) {
                foreach ($request->datareponse as $item) {
                    ReponseModel::create([
                        'gapid' => $request->gapid,
                        'response' => $item['reponse'],
                        'questionid' => $item['questionid'],
                    ]);
                }
                return response()->json([
                    "message" => "Traitement réussie avec succès!",
                    "code" => "200",
                    "data" => GapsModel::with(
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
                        'datascorecard'
                    )->where('id', $gapid->id)->first(),
                ], 200);
            } else {
                return response()->json([
                    "message" => "Identifiant gap :" . ($request->gapid) . " not foud!",
                    "code" => "422"
                ], 422);
            }
        }
    }

    public function evaluation(Request $request)
    {
        $user = Auth::user();
        $permission = Permission::where('name', 'create_gap')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        if ($organisation) {
            if ($permission) {
                $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
                $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
                    ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
                if ($permission_gap) {
                    $request->validate([
                        'scoreid' => 'required',
                        'thematiqueid' => 'required',
                        'questionid' => 'required',
                        'correction' => 'required',
                        "responsable" => 'required',
                        "echeance" => 'required',
                        "suivi" => 'required',
                    ]);
                    $test_thematique = EvaluationModels::where('scoreid', $request->scoreid)
                        ->where('thematiqueid', $request->thematiqueid)
                        ->where('questionid', $request->questionid)->first();
                    if ($test_thematique) {
                        return response()->json([
                            "message" => "Ces infomations semblent existées, car la cette thematique avec cette meme question sur ce scorecard existe!",
                            "code" => "422",
                        ], 422);
                    }
                    EvaluationModels::create([
                        'scoreid' => $request->scoreid,
                        'thematiqueid' => $request->thematiqueid,
                        'questionid' => $request->questionid,
                        'correction' => $request->correction,
                        'responsable' => $request->responsable,
                        'echeance' => $request->echeance,
                        'suivi' => $request->suivi
                    ]);
                    return response()->json([
                        "message" => "succèes",
                        "code" => "200",
                        "data" => EvaluationModels::with('datarubrique', 'dataquestion')->first()
                    ], 200);
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
    }
    public function updateEvaluation(Request $request, $id)
    {
        $request->validate([
            'scoreid' => 'required',
            'thematiqueid' => 'required',
            'questionid' => 'required',
            'correction' => 'required',
            "responsable" => 'required',
            "echeance" => 'required',
            "suivi" => 'required',
        ]);

        $scoreid = EvaluationModels::where('id', $id)->first();
        if ($scoreid) {
            $scoreid->scoreid = $request->scoreid;
            $scoreid->thematiqueid = $request->thematiqueid;
            $scoreid->questionid = $request->questionid;
            $scoreid->correction = $request->correction;
            $scoreid->responsable = $request->responsable;
            $scoreid->echeance = $request->echeance;
            $scoreid->suivi = $request->suivi;
            $scoreid->save();
            return response()->json([
                "message" => "succèes",
                "code" => "200",
                "data" => EvaluationModels::with('datarubrique', 'dataquestion')->first()
            ], 200);
        }
    }

    public function destroyEvlution($id)
    {
    }

    public function list_evaluation()
    {
        return response()->json([
            "data" => EvaluationModels::with('datarubrique')->get()
        ], 200);
    }

    public function Thematique_avec_questions($id)
    {
        $gap = GapsModel::find($id);
        return response()->json([
            "message" => "Liste des Thèmaques et questions",
            "code" => "200",
            "data" => ReponseModel::with('dataquestion.datarubrique')->where('response', "0")->where('gapid', $gap->id)->get()
        ], 200);
    }
}
