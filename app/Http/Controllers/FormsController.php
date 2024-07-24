<?php

namespace App\Http\Controllers;

use App\Models\formsModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UtilController;
use App\Models\Organisation;
use App\Models\ProjetModel;
use App\Models\ResponseForm;
use App\Models\UserHasForm;

class FormsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->checkPermission('create_form')) {
            return response()->json([
                "message" => "Forms list",
                "code" => "200",
                "data" => formsModel::with('organisation', 'project')->where('status', 1)->where('deleted', 0)->get(),
            ]);
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function get_form_data($id)
    {
        $form = formsModel::where('id', $id)->first();
        if ($form) {
            return response()->json([
                "message" => "Form data",
                "code" => "200",
                "data" => $form->hasform()->with('form.fields', 'response')->get(),
            ]);
        } else {
            return response()->json([
                "message" => "Form not found",
                "code" => "404"
            ], 404);
        }
    }

    public function form_data_by_User()
    {
        $user = Auth::user();
        return response()->json([
            "message" => "Form data",
            "code" => "200",
            "data" => UserHasForm::with('form.fields', 'response')->where('userid', $user->id)->get(),
        ]);
    }


    public function get_by_org($id)
    {
        $org = Organisation::where('id', $id)->first();
        if ($org) {
            $user = Auth::user();
            if ($user->checkPermission('create_form')) {
                return response()->json([
                    "message" => "Forms list",
                    "code" => "200",
                    "data" => formsModel::with('organisation', 'project')->where('orgid', $org->id)->where('status', 1)->where('deleted', 0)->get(),
                ]);
            } else {
                return response()->json([
                    "message" => "not authorized",
                    "code" => 404,
                ], 404);
            }
        } else {
            return response()->json([
                "message" => "Organisation not found",
                "code" => 404,
            ], 404);
        }
    }
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->checkPermission('create_form')) {
            $request->validate([
                'title' => 'required',
                'description' => 'required',
                'project_id'  => 'required',
                'type' => 'required',
                'orgid' => 'required'
            ]);

            $projet = ProjetModel::where('id', $request->project_id)->first();
            $org = Organisation::where('id', $request->orgid)->first();

            if ($projet) {
                if ($org) {
                    $otp_form = UtilController::generateCode();
                    $form = [
                        'title' => $request->title,
                        'description' => $request->description,
                        'otp_form' => $otp_form,
                        'project_id'  => $request->project_id,
                        'type' => $request->type,
                        'orgid' => $request->orgid
                    ];

                    $currentform = formsModel::create($form);
                    return response()->json([
                        "message" => "Saved successfully",
                        "code" => 200,
                        "data" => formsModel::with('organisation', 'project')->where('status', 1)->where('deleted', 0)->find($currentform->id)
                    ], 200);
                } else {
                    return response()->json([
                        "message" => "id organization not found",
                        "code" => 404,
                    ], 404);
                }
            } else {
                return response()->json([
                    "message" => "id project not found",
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

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->checkPermission('create_form')) {
            $form = formsModel::with('project')->find($id);
            if ($form) {
                $form->title = $form->title ? $form->title : $request->title;
                $form->description = $form->description ? $form->description : $request->description;
                $form->project_id  = $form->project_id ? $form->project_id : $request->project_id;
                $form->type =  $form->type ? $form->type : $request->type;
                $form->orgid =  $form->orgid ? $form->orgid : $request->orgid;
                $form->save();
                return response()->json([
                    "message" => "Updated successfully",
                    "code" => 200,
                    "data" => formsModel::with('organisation', 'project')->where('status', 1)->where('deleted', 0)->find($form->id),
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
        if ($user->checkPermission('create_form')) {
            $form = formsModel::find($id);
            if ($form) {
                $form->status = $request->status;
                $form->save();
                return response()->json([
                    "message" => "Status successfully",
                    "code" => 200,
                    "data" => formsModel::with('organisation', 'project')->where('status', 1)->where('deleted', 0)->get()
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
        if ($user->checkPermission('create_form')) {
            $form = formsModel::find($id);
            if ($form) {
                $form->deleted = 1;
                $form->save();
                return response()->json([
                    "message" => "Deleted successfully",
                    "code" => 200,
                    "data" => formsModel::with('organisation', 'project')->where('status', 1)->where('deleted', 0)->get()
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
