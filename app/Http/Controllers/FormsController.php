<?php

namespace App\Http\Controllers;

use App\Models\formsModel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UtilController;
use App\Models\Form_has_project_has_orga;
use App\Models\Organisation;
use App\Models\ProjetModel;
use App\Models\UserHasForm;

class FormsController extends Controller
{
    public function index()
    {

        $user = Auth::user();
        if ($user->checkPermissions('Formulaire', 'read'))
        {
            return response()->json([
                "message" => "Forms list",
                "code" => "200",
                "data" => formsModel::where('status', 1)->where('deleted', 0)->get(),
            ]);
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function get_form_data($id, $org_id)
    {
        $org = Organisation::where('delete', 0)->find($org_id);
        if ($org) {
            $form = Form_has_project_has_orga::where('id', $id)->where('org_id', $org->id)->first();
            if ($form) {
                return response()->json([
                    "message" => "Form data",
                    "code" => "200",
                    "data" => $form->hasform()->with('structure', 'user', 'form.form.fields','form.project.struturesantes', 'response')->get(),
                ]);
            } else {
                return response()->json([
                    "message" => "Form not found",
                    "code" => "404"
                ], 404);
            }
        } else {
            return response()->json([
                "message" => "Organisation not found",
                "code" => 404,
            ], 404);
        }
    }


    public function form_data_by_User()
    {
        $user = Auth::user();
        return response()->json([
            "message" => "Form data",
            "code" => "200",
            "data" => UserHasForm::with('structure', 'user', 'form.fields', 'response')->where('userid', $user->id)->get(),
        ]);
    }

    public function get_by_org($id)
    {
        $user = Auth::user();
        if ($user->checkPermissions('Formulaire', 'read'))
        {
            return response()->json([
                "message" => "Forms list",
                "code" => "200",
                "data" => formsModel::where('status', 1)->where('deleted', 0)->get(),
            ]);
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }


    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->checkPermissions('Formulaire', 'create'))
        {
            $request->validate([
                'title' => 'required',
                'description' => 'required',
                'type' => 'required',
            ]);

            $otp_form = UtilController::generateCode();
            $form = [
                'title' => $request->title,
                'description' => $request->description,
                'otp_form' => $otp_form,
                'type' => $request->type,
            ];

            $currentform = formsModel::create($form);
            return response()->json([
                "message" => "Saved successfully",
                "code" => 200,
                "data" => formsModel::where('status', 1)->where('deleted', 0)->find($currentform->id)
            ], 200);
        } else {
            return response()->json([
                "message" => "id organization not found",
                "code" => 404,
            ], 404);
        }
    }

    public function deployed(Request $request, $id)
    {
        $form = formsModel::where('id', $id)->first();
        if ($form) {
            $form->deployed = $request->deployed;
            $form->save();
            return response()->json([
                "message" => "Status successfully",
                "code" => 200,
            ], 200);
        } else {
            return response()->json([
                "message" => "Form not found",
                "code" => 404,
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->checkPermissions('Formulaire', 'update'))
        {
            $form = formsModel::find($id);
            if ($form) {
                $form->title =  $request->title;
                $form->description = $request->description;
                $form->type =  $request->type;
                $form->save();
                return response()->json([
                    "message" => "Updated successfully",
                    "code" => 200,
                    "data" => formsModel::where('status', 1)->where('deleted', 0),
                ], 200);
            } else {
                return response()->json([
                    "message" => "Id not found",
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

    public function status(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->checkPermissions('Formulaire', 'status'))
        {
            $form = formsModel::find($id);
            if ($form) {
                $form->status = $request->status;
                $form->save();
                return response()->json([
                    "message" => "Status successfully",
                    "code" => 200,
                    "data" => formsModel::where('status', 1)->where('deleted', 0)->get()
                ], 200);
            } else {
                return response()->json([
                    "message" => "Id not found",
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

    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->checkPermissions('Formulaire', 'delete'))
        {
            $form = formsModel::find($id);
            if ($form) {
                $form->deleted = 1;
                $form->save();
                return response()->json([
                    "message" => "Deleted successfully",
                    "code" => 200,
                    "data" => formsModel::where('status', 1)->where('deleted', 0)->get()
                ], 200);
            } else {
                return response()->json([
                    "message" => "Id not found",
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
}
