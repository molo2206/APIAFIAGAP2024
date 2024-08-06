<?php

namespace App\Http\Controllers;

use App\Models\Form_has_project_has_orga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UtilController;
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
            if ($user->checkPermission('create_form')) {
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
        if (Form_has_project_has_orga::where('otp_form', $otp)->first())
        {
            if ($user->checkPermission('create_form')) {
                return response()->json([
                    "message" => "Data created successfully",
                    "code" => 200,
                    "data" => Form_has_project_has_orga::with('form.fields.typefield', 'organisation', 'project.struturesantes')->where('status', 1)->where('deleted', 0)->where('otp_form', $otp)->first()
                ]);
            } else {
                return response()->json([
                    "message" => "not authorized",
                    "code" => 404,
                ], 404);
            }
        } else {
            return response()->json([
                "message" => "otp not found",
                "code" => 404,
            ], 404);
        }
    }
}
