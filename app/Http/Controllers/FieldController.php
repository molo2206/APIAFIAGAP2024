<?php

namespace App\Http\Controllers;

use App\Models\FieldsModel;
use App\Models\formsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FieldController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->checkPermission('create_gap')) {
            return response()->json([
                "message" => "Forms list",
                "code" => "200",
                "data" => FieldsModel::where('deleted', 0)->get(),
            ]);
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function show($otp)
    {
        $form = formsModel::where('otp_form', $otp)->first();
        if ($form) {
            return response()->json([
                "message" => "Saved successfully",
                "code" => 200,
                "data" => formsModel::with('fields.typefield','project.struturesantes')->where('otp_form', $form->otp_form)->first()
            ], 200);
        } else {
            return response()->json([
                "message" => "Otp not found",
                "code" => "404"
            ], 404);
        }
    }



    public function show_by_id($id)
    {
        $form = formsModel::find($id);
        if ($form) {
            return response()->json([
                "message" => "Saved successfully",
                "code" => 200,
                "data" => formsModel::with('fields.typefield','project.struturesantes')->where('id', $form->id)->first()
            ], 200);
        } else {
            return response()->json([
                "message" => "Id not found",
                "code" => "404"
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->checkPermission('create_gap')) {
            $request->validate([
                'form_id' => 'required',
                'fields' => 'required',
            ]);
            $form = formsModel::where('id', $request->form_id)->first();
            if ($form) {
                $form->field()->detach();
                foreach ($request->fields as $item) {
                    $form->field()->attach([$form->id =>
                    [
                        'name' => $item['name'],
                        'label' => $item['label'],
                        'fieldtype_id' => $item['fieldtype_id'],
                        'isOptional' => $item['isOptional']
                    ]]);
                }
                return response()->json([
                    "message" => "Saved successfully",
                    "code" => 200,
                    "data" => formsModel::with('fields.typefield')->where('id', $form->id)->first()
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

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->checkPermission('create_gap')) {
            $request->validate([
                'name' => 'required',
                'label' => 'required',
                'form_id' => 'required',
                'fieldtype_id' => 'required',
            ]);

            $form = FieldsModel::with('fields.typefield')->find($id);
            if ($form) {

                $form->name = $form->name ? $form->name : $request->name;
                $form->label = $form->label ? $form->label : $request->label;
                $form->form_id = $form->form_id ? $form->form_id : $request->form_id;
                $form->fieldtype_id = $form->fieldtype_id ? $form->fieldtype_id : $request->fieldtype_id;
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
            $form = FieldsModel::find($id);
            if ($form) {
                $form->status = $request->status;
                $form->save();

                return response()->json([
                    "message" => "Status successfully",
                    "code" => 200,
                    "data" => FieldsModel::where('deleted', 0)->where('status', 1)->get()
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
            $form = FieldsModel::find($id);
            if ($form) {
                $form->deleted = 1;
                $form->save();
                return response()->json([
                    "message" => "Deleted successfully",
                    "code" => 200,
                    "data" => FieldsModel::where('deleted', 0)->where('status', 1)->get()
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
