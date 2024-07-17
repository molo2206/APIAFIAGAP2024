<?php

namespace App\Http\Controllers;

use App\Models\formsModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UtilController;

class FormsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->checkPermission('create_gap')) {
            return response()->json([
                "message" => "Forms list",
                "code" => "200",
                "data" => formsModel::where('deleted', 0)->get(),
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
        if ($user->checkPermission('create_gap')) {
            $request->validate([
                'title' => 'required',
                'description' => 'required',
            ]);

            $otp_form = UtilController::generateCode();
            $form = [
                'title' => $request->title,
                'description' => $request->description,
                'otp_form' => $otp_form
            ];

            formsModel::create($form);
            return response()->json([
                "message" => "Saved successfully",
                "code" => 200,
            ], 200);
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function show_form_by_id($id)
    {
        $form = formsModel::with('fieldsdata.response')->find($id);
        if ($form) {
            return response()->json([
                "message" => "Forms list",
                "code" => "200",
                "data" =>  $form,
            ]);
        } else {
            return response()->json([
                "message" => "Id not found",
                "code" => 404,
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->checkPermission('create_gap')) {
            $form = formsModel::find($id);
            if ($form) {
                $form->title = $form->title ? $form->title : $request->title;
                $form->description = $form->description ? $form->description : $request->description;
                $form->save();
                return response()->json([
                    "message" => "Updated successfully",
                    "code" => 200,
                    "data" => $form,
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
        if ($user->checkPermission('create_gap')) {
            $form = formsModel::find($id);
            if ($form) {
                $form->status = $request->status;
                $form->save();
                return response()->json([
                    "message" => "Status successfully",
                    "code" => 200,
                    "data" => formsModel::where('deleted', 0)->where('status', 1)->get()
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
        if ($user->checkPermission('create_gap')) {
            $form = formsModel::find($id);
            if ($form) {
                $form->deleted = 1;
                $form->save();
                return response()->json([
                    "message" => "Deleted successfully",
                    "code" => 200,
                    "data" => formsModel::where('deleted', 0)->where('status', 1)->get()
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
