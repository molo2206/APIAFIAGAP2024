<?php

namespace App\Http\Controllers;

use App\Models\Form_has_project_has_orga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UtilController;
use App\Models\formsModel;
use App\Models\Organisation;

class FormHasProjectHasOrganisation extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'form_id' => 'required',
            'project_id' => 'required',
            'org_id' => 'required',
        ]);
        $user = Auth::user();
        if ($user->checkPermission('create_form')) {
            if (Form_has_project_has_orga::where('form_id', $request->form_id)
                ->where('project_id', $request->project_id)->where('org_id', $request->org_id)->exists()
            ) {
                return response()->json([
                    "message" => "Désolé, mais ce formulaire est déjà associé à ce même projet.",
                    "code" => 404,
                ], 404);
            } else {
                $otp_form = UtilController::generateCode();
                $data = [
                    'form_id' => $request->form_id,
                    'project_id' => $request->project_id,
                    'org_id' => $request->org_id,
                    'otp_form' => $otp_form,
                ];
                //Save the data to the database
                $currentform = Form_has_project_has_orga::create($data);
                return response()->json([
                    "message" => "Data created successfully",
                    "code" => 200,
                    "data" => Form_has_project_has_orga::with('form.fields.typefield', 'organisation', 'project.struturesantes')->where('status', 1)->where('deleted', 0)->find($currentform->id)
                ]);
            }
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'form_id' => 'required',
            'project_id' => 'required',
            'org_id' => 'required',
        ]);
        $user = Auth::user();
        if ($user->checkPermission('create_form')) {
            $formhas = Form_has_project_has_orga::find($id);
            if ($formhas) {
                $formhas->form_id = $request->form_id;
                $formhas->project_id = $request->project_id;
                $formhas->org_id = $request->org_id;
                //Update the data to the database
                $formhas->save();
                return response()->json([
                    "message" => "Data updated successfully",
                    "code" => 200,
                    "data" => Form_has_project_has_orga::with('form.fields.typefield', 'organisation', 'project.struturesantes')->where('status', 1)->where('deleted', 0)->find($formhas->id)
                ]);
            } else {
                return response()->json([
                    "message" => "Id not found.",
                    "code" => 404,
                ], 404);
            }
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function get_has_form($id)
    {
        $user = Auth::user();
        if (Organisation::find($id)) {
            if ($user->checkPermission('view_alert')) {
                return response()->json([
                    "message" => "Data created successfully",
                    "code" => 200,
                    "data" => Form_has_project_has_orga::with('form.fields.typefield', 'organisation', 'project.struturesantes')->where('status', 1)->where('deleted', 0)->where('org_id', $id)->get()
                ]);
            } else {
                return response()->json([
                    "message" => "not authorized",
                    "code" => 404,
                ], 404);
            }
        } else {
            return response()->json([
                "message" => "id not found",
                "code" => 404,
            ], 404);
        }
    }
    public function getby_otp($otp)
    {
        $user = Auth::user();
        if (Form_has_project_has_orga::where('otp_form', $otp)->first()) {
            // if ($user->checkPermission('create_form')) {
            return response()->json([
                "message" => "Data created successfully",
                "code" => 200,
                "data" => Form_has_project_has_orga::with('form.fields.typefield', 'organisation', 'project.struturesantes')->where('status', 1)
                    ->where('deleted', 0)->where('otp_form', $otp)->first()
            ]);
        } else {
            return response()->json([
                "message" => "otp not found",
                "code" => 404,
            ], 404);
        }
    }
    public function userforms()
    {
        $user = Auth::user();
        return response()->json([
            "message" => "Data created successfully",
            "code" => 200,
            "data" => $user->forms()->with(
                'user_org_hasforms.form.fields.typefield',
                'user_org_hasforms.organisation',
                'user_org_hasforms.project.struturesantes'
            )->where('status', 1)
                ->get()
        ]);
    }
}
